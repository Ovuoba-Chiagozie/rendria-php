<?php

require_once __DIR__ . '/../Models/ApiKey.php';

class ApiKeyAuth
{
    public static function handle(): ?array
    {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;

        if (!$apiKey) {
            http_response_code(401);
            echo json_encode(['error' => 'API key required']);
            exit;
        }

        $keyHash = hash('sha256', $apiKey);
        $keyData = ApiKey::findByHash($keyHash);

        if (!$keyData) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid API key']);
            exit;
        }

        return $keyData;
    }
}