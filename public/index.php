<?php

require_once __DIR__ . '/../src/Core/Router.php';

$router = new Router();


$router->get('/health', function () {
    echo json_encode(['status' => 'ok']);
});


$router->post('/api/v1/keys', 'ApiKeyController@create');


$router->post('/api/v1/render', 'RenderController@render');
$router->get('/api/v1/usage', 'UsageController@show');

$router->dispatch();