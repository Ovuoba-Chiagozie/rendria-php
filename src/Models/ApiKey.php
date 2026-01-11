<?php

require_once __DIR__ . '/../Core/Database.php';

class ApiKey
{
    public static function findByHash(string $keyHash): ?array
    {
        $db = Database::connection();
        $stmt = $db->prepare("
            SELECT ak.*, u.email 
            FROM api_keys ak
            JOIN users u ON ak.user_id = u.id
            WHERE ak.key_hash = ? AND ak.is_active = 1
        ");
        $stmt->execute([$keyHash]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $userId, string $keyHash): int
    {
        $db = Database::connection();
        $stmt = $db->prepare("
            INSERT INTO api_keys (user_id, key_hash, credits, rate_limit) 
            VALUES (?, ?, 100, 100)
        ");
        $stmt->execute([$userId, $keyHash]);
        return (int)$db->lastInsertId();
    }

    public static function deductCredits(int $id, int $amount): void
    {
        $db = Database::connection();
        $stmt = $db->prepare("UPDATE api_keys SET credits = credits - ? WHERE id = ?");
        $stmt->execute([$amount, $id]);
    }

    public static function incrementRequests(int $id, int $count, string $date): void
    {
        $db = Database::connection();
        $stmt = $db->prepare("
            UPDATE api_keys 
            SET requests_today = ?, last_request_date = ?
            WHERE id = ?
        ");
        $stmt->execute([$count, $date, $id]);
    }
}