<?php
declare(strict_types=1);

function db_config(): array
{
    $defaults = [
        'host' => '127.0.0.1',
        'database' => 'olymp',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ];

    $configFile = dirname(__DIR__) . '/config.php';
    if (is_file($configFile)) {
        $custom = require $configFile;
        if (is_array($custom)) {
            return array_merge($defaults, $custom);
        }
    }

    return $defaults;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $c = db_config();
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $c['host'], $c['database'], $c['charset']);
    $pdo = new PDO($dsn, $c['username'], $c['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function db_available(): bool
{
    try {
        db()->query('SELECT 1');
        return true;
    } catch (Throwable) {
        return false;
    }
}

