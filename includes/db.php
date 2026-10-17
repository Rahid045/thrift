<?php
require_once __DIR__ . '/../config.php';

function ensureDatabaseReady(): void
{
    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '`');
        $pdo->exec('USE `' . DB_NAME . '`');

        $result = $pdo->query("SHOW TABLES LIKE 'products'");
        if ($result instanceof PDOStatement && $result->fetch() === false) {
            $schema = @file_get_contents(__DIR__ . '/../database/schema.sql');
            if ($schema !== false) {
                $pdo->exec($schema);
            }
        }

        $pdo->exec("CREATE TABLE IF NOT EXISTS `product_images` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `product_id` INT NOT NULL,
            `image` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `product_id_index` (`product_id`),
            CONSTRAINT `product_images_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (Throwable $e) {
        // Let the app fail loudly only if MySQL itself is unavailable.
        if (!headers_sent()) {
            http_response_code(500);
        }
        throw $e;
    }
}

function getDb(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    ensureDatabaseReady();

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
