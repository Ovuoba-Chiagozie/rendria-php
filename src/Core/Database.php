<?php

require_once __DIR__ . '/Env.php';

class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            // Load environment variables
            Env::load(__DIR__ . '/../../.env');

            // Load config
            $config = require __DIR__ . '/../../config/database.php';

            try {
                self::$pdo = new PDO(
                    "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}",
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    'error' => 'Database connection failed',
                    'message' => 'Please check your .env configuration'
                ]);
                exit;
            }
        }

        return self::$pdo;
    }
}