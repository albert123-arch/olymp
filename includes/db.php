<?php
function app_config()
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $configPath = __DIR__ . '/config.php';
    $rootConfigPath = dirname(__DIR__) . '/config.php';
    $examplePath = __DIR__ . '/config.example.php';
    $GLOBALS['APP_CONFIG_PATH'] = null;
    if (file_exists($configPath)) {
        $config = require $configPath;
        $GLOBALS['APP_CONFIG_PATH'] = $configPath;
    } elseif (file_exists($rootConfigPath)) {
        $config = require $rootConfigPath;
        $GLOBALS['APP_CONFIG_PATH'] = $rootConfigPath;
    } else {
        $config = require $examplePath;
        $GLOBALS['APP_CONFIG_PATH'] = $examplePath;
    }
    if (!isset($config['app'])) {
        $config['app'] = [
            'base_url' => $config['base_url'] ?? '',
            'default_lang' => 'ru',
            'debug' => false,
        ];
    }
    if (isset($config['db']['password']) && !isset($config['db']['pass'])) {
        $config['db']['pass'] = $config['db']['password'];
    }
    return $config;
}

function has_real_config()
{
    return file_exists(__DIR__ . '/config.php') || file_exists(dirname(__DIR__) . '/config.php');
}

function db()
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = app_config()['db'];
    if (empty($config['user']) || empty($config['name'])) {
        throw new RuntimeException('Database config is missing db.user or db.name. Loaded config: ' . ($GLOBALS['APP_CONFIG_PATH'] ?? 'unknown'));
    }
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $config['host'],
        $config['name'],
        $config['charset'] ?? 'utf8mb4'
    );

    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function fetch_all($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetch_one($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

function execute_query($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    return $stmt->execute($params);
}

function table_exists($table)
{
    static $cache = [];
    if (isset($cache[$table])) {
        return $cache[$table];
    }
    try {
        $stmt = db()->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        $cache[$table] = (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $cache[$table] = false;
    }
    return $cache[$table];
}

function column_exists($table, $column)
{
    static $cache = [];
    $key = $table . '.' . $column;
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    try {
        $stmt = db()->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        $cache[$key] = (bool) $stmt->fetch();
    } catch (Throwable $e) {
        $cache[$key] = false;
    }
    return $cache[$key];
}

function column_type($table, $column)
{
    static $cache = [];
    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    try {
        $stmt = db()->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
        $stmt->execute([$column]);
        $row = $stmt->fetch();
        $cache[$key] = $row['Type'] ?? null;
    } catch (Throwable $e) {
        $cache[$key] = null;
    }
    return $cache[$key];
}
