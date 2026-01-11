<?php

require_once __DIR__ . '/../Core/Database.php';

class RenderRequest
{
    public static function create(int $apiKeyId, int $creditsUsed): int
    {
        $db = Database::connection();
        $stmt = $db->prepare("
            INSERT INTO render_requests (api_key_id, status, credits_used) 
            VALUES (?, 'completed', ?)
        ");
        $stmt->execute([$apiKeyId, $creditsUsed]);
        return (int)$db->lastInsertId();
    }

    public static function getStats(int $apiKeyId): array
    {
        $db = Database::connection();
        $stmt = $db->prepare("
            SELECT COUNT(*) as total_requests, SUM(credits_used) as total_credits_used
            FROM render_requests 
            WHERE api_key_id = ?
        ");
        $stmt->execute([$apiKeyId]);
        return $stmt->fetch();
    }
}