<?php
declare(strict_types=1);

function db(): PDO
{
    global $pdo;
    return $pdo;
}

function app_config(?string $section = null): mixed
{
    global $config;
    return $section === null ? $config : ($config[$section] ?? []);
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $app = app_config('app');
    $base = '/' . trim((string)($app['base_path'] ?? ''), '/');
    if ($base === '/') {
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    $token = (string)($_POST['csrf'] ?? '');
    if (!hash_equals((string)($_SESSION['csrf'] ?? ''), $token)) {
        http_response_code(419);
        exit('Сессия формы устарела. Обновите страницу.');
    }
}

function current_user(): ?array
{
    static $loaded = false;
    static $user = null;
    if ($loaded) {
        return $user;
    }
    $loaded = true;
    $id = (int)($_SESSION['user_id'] ?? 0);
    if ($id < 1) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, login, created_at, last_login_at FROM users WHERE id = ? AND is_active = 1');
    $stmt->execute([$id]);
    $user = $stmt->fetch() ?: null;
    return $user;
}

function require_auth(): array
{
    $user = current_user();
    if (!$user) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'] ?? url('');
        redirect('login.php');
    }
    return $user;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function app_installed(): bool
{
    try {
        return (bool)db()->query("SELECT value_text FROM app_settings WHERE setting_key = 'installed_at' LIMIT 1")->fetchColumn();
    } catch (Throwable) {
        return false;
    }
}

function setting(string $key, string $default = ''): string
{
    try {
        $stmt = db()->prepare('SELECT value_text FROM app_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value === false ? $default : (string)$value;
    } catch (Throwable) {
        return $default;
    }
}

function review_steps(): array
{
    return [
        ['label' => '10 минут', 'seconds' => 600],
        ['label' => '1 день', 'seconds' => 86400],
        ['label' => '3 дня', 'seconds' => 259200],
        ['label' => '7 дней', 'seconds' => 604800],
        ['label' => '14 дней', 'seconds' => 1209600],
        ['label' => '30 дней', 'seconds' => 2592000],
        ['label' => '45 дней', 'seconds' => 3888000],
        ['label' => '60 дней', 'seconds' => 5184000],
    ];
}

function next_review_level(int $current, int $grade): int
{
    if ($grade === 0) {
        return 0;
    }
    if ($current === 0) {
        return [0, 1, 2, 3][$grade] ?? 1;
    }
    if ($grade === 1) {
        return max(1, $current);
    }
    if ($grade === 2) {
        return min(7, $current + 1);
    }
    return min(7, $current + 2);
}

function update_topic_progress(int $userId, int $topicId): void
{
    $stmt = db()->prepare('SELECT COUNT(DISTINCT task_id) FROM attempts WHERE user_id = ? AND topic_id = ? AND auto_passed = 1');
    $stmt->execute([$userId, $topicId]);
    $solved = (int)$stmt->fetchColumn();

    $requiredStmt = db()->prepare('SELECT required_solutions FROM topics WHERE id = ?');
    $requiredStmt->execute([$topicId]);
    $required = max(1, (int)$requiredStmt->fetchColumn());
    $status = $solved >= $required ? 'mastered' : ($solved > 0 ? 'in_progress' : 'not_started');

    $sql = 'INSERT INTO topic_progress (user_id, topic_id, solved_count, status, mastered_at)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE solved_count = VALUES(solved_count), status = VALUES(status), mastered_at = VALUES(mastered_at)';
    db()->prepare($sql)->execute([
        $userId,
        $topicId,
        $solved,
        $status,
        $status === 'mastered' ? date('Y-m-d H:i:s') : null,
    ]);
}


function h(mixed $value): string
{
    return e($value);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function set_flash(string $type, string $message): void
{
    flash($type, $message);
}

function take_flash(): ?array
{
    return pull_flash();
}
