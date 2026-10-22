<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
$user = require_auth();
$userId = (int)$user['id'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $current = (string)($_POST['current_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['new_password_confirm'] ?? '');

    $stmt = db()->prepare('SELECT password_hash FROM users WHERE id=?');
    $stmt->execute([$userId]);
    $hash = (string)$stmt->fetchColumn();

    if (!password_verify($current, $hash)) {
        $error = 'Текущий пароль указан неверно.';
    } elseif (strlen($new) < 12) {
        $error = 'Новый пароль должен содержать не менее 12 символов.';
    } elseif ($new !== $confirm) {
        $error = 'Новые пароли не совпадают.';
    } else {
        db()->prepare('UPDATE users SET password_hash=? WHERE id=?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), $userId]);
        session_regenerate_id(true);
        flash('success', 'Пароль обновлён.');
        redirect('settings.php');
    }
}

render_header('Настройки', 'settings');
?>
<div class="two-column">
<section class="panel">
    <p class="eyebrow">Безопасность</p><h2>Изменить пароль</h2>
    <?php if ($error): ?><div class="flash error"><?= h($error) ?></div><?php endif; ?>
    <form method="post" class="stack">
        <?= csrf_field() ?>
        <label>Текущий пароль<input type="password" name="current_password" required autocomplete="current-password"></label>
        <label>Новый пароль<input type="password" name="new_password" required minlength="12" autocomplete="new-password"></label>
        <label>Повторите новый пароль<input type="password" name="new_password_confirm" required minlength="12" autocomplete="new-password"></label>
        <button class="button primary" type="submit">Обновить пароль</button>
    </form>
</section>
<aside class="panel">
    <p class="eyebrow">Система повторений</p><h2>Шаги до 60 дней</h2>
    <div class="intervals vertical"><?php foreach (review_steps() as $step): ?><span><?= h($step['label']) ?></span><?php endforeach; ?></div>
    <p class="muted">«Не помню» возвращает карточку к первому шагу. «Хорошо» и «Легко» двигают вперёд.</p>
    <hr>
    <p><strong>Последний запуск cron:</strong><br><?= h(setting('last_cron_at', 'ещё не запускался')) ?></p>
</aside>
</div>
<?php render_footer(); ?>
