<?php

require_once __DIR__ . '/../Middleware/ApiKeyAuth.php';
require_once __DIR__ . '/../Middleware/RateLimiter.php';
require_once __DIR__ . '/../Models/RenderRequest.php';

class RenderController
{
    public function render(): void
    {
        header('Content-Type: application/json');

        $keyData = ApiKeyAuth::handle();
        RateLimiter::check($keyData);

        $creditsNeeded = 2;
        RateLimiter::checkCredits($keyData, $creditsNeeded);

        $input = json_decode(file_get_contents('php://input'), true);
        $template = $input['template'] ?? 'basic';

        RateLimiter::deductCredits($keyData['id'], $creditsNeeded);

  
        $requestId = RenderRequest::create($keyData['id'], $creditsNeeded);

        $mockOutputUrl = "https://cdn.rendria.dev/renders/mock_{$requestId}.png";

        $creditsRemaining = (int)$keyData['credits'] - $creditsNeeded;

        echo json_encode([
            'request_id' => $requestId,
            'status' => 'completed',
            'output_url' => $mockOutputUrl,
            'template' => $template,
            'credits_used' => $creditsNeeded,
            'credits_remaining' => $creditsRemaining,
            'message' => 'Render completed successfully'
        ]);
    }
}