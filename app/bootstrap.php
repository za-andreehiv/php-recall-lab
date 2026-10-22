<?php
declare(strict_types=1);

$configPath = dirname(__DIR__) . '/config.php';
if (!is_file($configPath)) {
    http_response_code(503);
    echo '<h1>PHP Recall Lab не настроен</h1><p>Скопируйте config.example.php в config.php и заполните параметры базы.</p>';
    exit;
}

$config = require $configPath;
date_default_timezone_set((string)($config['app']['timezone'] ?? 'Europe/Moscow'));

$secureCookie = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_name('php_recall_lab');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => rtrim((string)($config['app']['base_path'] ?? ''), '/') . '/',
    'secure' => $secureCookie,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$db = $config['db'] ?? [];
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $db['host'] ?? 'localhost',
    (int)($db['port'] ?? 3306),
    $db['database'] ?? '',
    $db['charset'] ?? 'utf8mb4'
);

try {
    $pdo = new PDO($dsn, (string)($db['username'] ?? ''), (string)($db['password'] ?? ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $error) {
    http_response_code(503);
    $message = !empty($config['app']['debug']) ? htmlspecialchars($error->getMessage()) : 'Не удалось подключиться к базе данных.';
    echo '<h1>Ошибка подключения</h1><p>' . $message . '</p>';
    exit;
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/checker.php';
require_once __DIR__ . '/layout.php';
