<?php
namespace App\Core;

use PDO;
use PDOException;

final class DB {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (!self::$pdo) {
            try {
                self::$pdo = new PDO(DB_DSN, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    // PDO::ATTR_PERSISTENT      => true, // optionnel
                ]);
            } catch (PDOException $e) {
                if (APP_DEBUG) {
                    throw $e; // stack trace en dev
                }
                // Message neutre en prod
                http_response_code(500);
                exit('Database connection error.');
            }
        }
        return self::$pdo;
    }
}