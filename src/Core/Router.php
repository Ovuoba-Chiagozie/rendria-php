<?php

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        
        $basePath = '/rendria-php/public';

        if (str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = $uri ?: '/';


        $handler = $this->routes[$method][$uri] ?? null;

        if (!$handler) {
            $this->jsonResponse(['error' => 'Route not found'], 404);
            return;
        }

        if (is_callable($handler)) {
            call_user_func($handler);
            return;
        }

        $this->callController($handler);
    }

    private function callController(string $handler): void
    {
        [$controller, $method] = explode('@', $handler);

        $controllerFile = __DIR__ . '/../Controllers/' . $controller . '.php';

        if (!file_exists($controllerFile)) {
            $this->jsonResponse(['error' => 'Controller not found'], 500);
            return;
        }

        require_once $controllerFile;

        if (!method_exists($controller, $method)) {
            $this->jsonResponse(['error' => 'Method not found'], 500);
            return;
        }

        $instance = new $controller();
        $instance->$method();
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
