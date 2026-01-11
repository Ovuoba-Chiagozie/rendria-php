<?php

require_once __DIR__ . '/../Core/Database.php';

class User
{
    public static function findByEmail(string $email): ?array
    {
        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function create(string $email): int
    {
        $db = Database::connection();
        $stmt = $db->prepare("INSERT INTO users (email) VALUES (?)");
        $stmt->execute([$email]);
        return (int)$db->lastInsertId();
    }
}