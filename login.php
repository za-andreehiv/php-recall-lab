<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

if (!app_installed()) {
    redirect('install.php');
}
if (current_user()) {
    redirect('index.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $login = trim((string)($_POST['login'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM users WHERE login = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$login]);
    $row = $stmt->fetch();

    $locked = $row && $row['locked_until'] && strtotime((string)$row['locked_until']) > time();
    if (!$row || $locked || !password_verify($password, (string)($row['password_hash'] ?? ''))) {
        if ($row && !$locked) {
            $failed = (int)$row['failed_login_count'] + 1;
            $lockSql = $failed >= 5 ? 'DATE_ADD(NOW(), INTERVAL 5 MINUTE)' : 'NULL';
            db()->prepare("UPDATE users SET failed_login_count = ?, locked_until = $lockSql WHERE id = ?")
                ->execute([$failed >= 5 ? 0 : $failed, $row['id']]);
        }
        usleep(250000);
        $error = 'Неверный логин или пароль. После 5 ошибок вход блокируется на 5 минут.';
    } else {
        db()->prepare('UPDATE users SET failed_login_count = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?')
            ->execute([$row['id']]);
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$row['id'];
        redirect('index.php');
    }
}

render_header('Вход');
?>
<section class="auth-card">
    <?php if ($error): ?><div class="flash error"><?= h($error) ?></div><?php endif; ?>
    <form method="post" class="stack">
        <?= csrf_field() ?>
        <label>Логин
            <input name="login" required autocomplete="username" autofocus>
        </label>
        <label>Пароль
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button class="button primary" type="submit">Войти в тренажёр</button>
    </form>
</section>
<?php render_footer(); ?>
