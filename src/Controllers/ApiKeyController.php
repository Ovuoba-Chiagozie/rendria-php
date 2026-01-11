<?php

require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/ApiKey.php';

class ApiKeyController
{
    public function create(): void
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? null;

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Valid email is required']);
            return;
        }

       
        $existingUser = User::findByEmail($email);

        if ($existingUser) {
            http_response_code(409);
            echo json_encode([
                'error' => 'User with this email already exists',
                'message' => 'Each email can only have one API key. Please use a different email.'
            ]);
            return;
        }

     
        $userId = User::create($email);

      
        $apiKey = 'rnd_' . bin2hex(random_bytes(16));
        // Hashing key before storage
        $keyHash = hash('sha256', $apiKey);

        
        ApiKey::create($userId, $keyHash);

        http_response_code(201);
        echo json_encode([
            'api_key' => $apiKey,
            'credits' => 100,
            'rate_limit' => 100,
            'message' => 'API key created successfully',
            'warning' => 'Store this key securely. It will not be shown again.'
        ]);
    }
}