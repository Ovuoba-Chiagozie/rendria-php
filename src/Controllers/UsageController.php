<?php

require_once __DIR__ . '/../Middleware/ApiKeyAuth.php';
require_once __DIR__ . '/../Models/RenderRequest.php';

class UsageController
{
    public function show(): void
    {
        header('Content-Type: application/json');

        $keyData = ApiKeyAuth::handle();

        $stats = RenderRequest::getStats($keyData['id']);

        $today = date('Y-m-d');
        $requestsToday = ($keyData['last_request_date'] === $today) 
            ? (int)$keyData['requests_today'] 
            : 0;

        echo json_encode([
            'email' => $keyData['email'],
            'credits_remaining' => (int)$keyData['credits'],
            'rate_limit' => (int)$keyData['rate_limit'],
            'requests_today' => $requestsToday,
            'total_requests' => (int)$stats['total_requests'],
            'total_credits_used' => (int)$stats['total_credits_used'] ?? 0,
            'account_active' => (bool)$keyData['is_active']
        ]);
    }
}