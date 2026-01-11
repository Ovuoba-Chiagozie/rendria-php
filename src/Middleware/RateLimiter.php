<?php

require_once __DIR__ . '/../Models/ApiKey.php';

class RateLimiter
{

    public static function check(array $keyData): void
    {
        $today = date('Y-m-d');
        $lastRequestDate = $keyData['last_request_date'];
        $requestsToday = (int)$keyData['requests_today'];
        $rateLimit = (int)$keyData['rate_limit'];

        if ($lastRequestDate !== $today) {
            $requestsToday = 0;
        }

        if ($requestsToday >= $rateLimit) {
            http_response_code(429);
            echo json_encode([
                'error' => 'Rate limit exceeded',
                'message' => "You have exceeded your daily limit of {$rateLimit} requests.",
                'requests_today' => $requestsToday,
                'rate_limit' => $rateLimit
            ]);
            exit;
        }

        ApiKey::incrementRequests($keyData['id'], $requestsToday + 1, $today);
    }

    public static function checkCredits(array $keyData, int $creditsNeeded = 1): void
    {
        $remainingCredits = (int)$keyData['credits'];

        if ($remainingCredits < $creditsNeeded) {
            http_response_code(402);
            echo json_encode([
                'error' => 'Insufficient credits',
                'message' => "This request requires {$creditsNeeded} credits but you only have {$remainingCredits} remaining.",
                'credits_remaining' => $remainingCredits,
                'credits_needed' => $creditsNeeded
            ]);
            exit;
        }
    }

    public static function deductCredits(int $apiKeyId, int $credits = 1): void
    {
        ApiKey::deductCredits($apiKeyId, $credits);
    }
}