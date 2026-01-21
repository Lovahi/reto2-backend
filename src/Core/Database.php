<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            // En un entorno real, esto vendría de un archivo .env o config.php
            // Cuando tengamos que hacer despligue en Docker, se cambia.
            $config = [
                'host' => 'localhost',
                'db'   => 'gamefest',
                'user' => 'root',
                'pass' => '',
                'charset' => 'utf8'
            ];

            $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], $options);
            } catch (PDOException $e) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
                exit;
            }
        }

        return self::$instance;
    }
}
