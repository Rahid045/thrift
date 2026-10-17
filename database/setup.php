<?php
require_once __DIR__ . '/../config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '`');
    $pdo->exec('USE `' . DB_NAME . '`');

    $sql = file_get_contents(__DIR__ . '/schema.sql');
    if ($sql === false) {
        throw new RuntimeException('Unable to read schema file.');
    }

    $pdo->exec($sql);
    echo "Database setup complete.\n";
} catch (Throwable $e) {
    echo 'Database setup failed: ' . $e->getMessage() . "\n";
    exit(1);
}
