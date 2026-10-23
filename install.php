<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/course_seed.php';

if (app_installed()) {
    redirect(current_user() ? 'index.php' : 'login.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9_.-]{3,80}$/', $login)) {
        $error = 'Логин: 3–80 символов, латиница, цифры, точка, дефис или подчёркивание.';
    } elseif (strlen($password) < 12) {
        $error = 'Пароль должен содержать не менее 12 символов.';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают.';
    } else {
        try {
            $schema = file_get_contents(__DIR__ . '/schema.sql');
            if ($schema === false) {
                throw new RuntimeException('Не удалось прочитать schema.sql');
            }
            foreach (explode('-- SPLIT --', $schema) as $statement) {
                $statement = trim($statement);
                if ($statement !== '') {
                    db()->exec($statement);
                }
            }

            db()->beginTransaction();
            seed_prime_course(db());

            $stmt = db()->prepare('INSERT INTO users (login, password_hash) VALUES (?, ?)');
            $stmt->execute([$login, password_hash($password, PASSWORD_DEFAULT)]);
            $userId = (int) db()->lastInsertId();

            seed_course_cards(db());
            activate_first_course_cards($userId);

            $settings = db()->prepare('INSERT INTO app_settings (setting_key, value_text) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE value_text = VALUES(value_text)');
            $settings->execute(['installed_at', date('c')]);
            $settings->execute(['review_intervals', '10 минут, 1, 3, 7, 14, 30, 45, 60 дней']);
            $settings->execute(['edition', 'course-447-cards']);
            $settings->execute(['course_exercises', (string)prime_course_total()]);
            $settings->execute(['course_cards', (string)course_card_total()]);
            db()->commit();

            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            set_flash('success', 'Чистый курс 447 установлен. Первые карточки готовы к повторению.');
            redirect('index.php');
        } catch (Throwable $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            error_log($e->__toString());
            $error = 'Установка не завершена. Проверьте доступ к базе и журнал ошибок PHP.';
        }
    }
}

render_header('Первичная установка');
?>
<section class="auth-card">
    <div class="callout">
        <strong>Один личный аккаунт.</strong>
        Роли администратора, учителя и ученика в этой версии намеренно не добавлены.
    </div>
    <?php if ($error): ?><div class="flash error"><?= h($error) ?></div><?php endif; ?>
    <form method="post" class="stack">
        <?= csrf_field() ?>
        <label>Логин
            <input name="login" required minlength="3" maxlength="80" autocomplete="username">
        </label>
        <label>Пароль
            <input type="password" name="password" required minlength="12" autocomplete="new-password">
        </label>
        <label>Повторите пароль
            <input type="password" name="password_confirm" required minlength="12" autocomplete="new-password">
        </label>
        <button class="button primary" type="submit">Создать аккаунт и установить курс 447</button>
    </form>
    <p class="muted">Для этого выпуска нужна полностью пустая MySQL-база и файл <code>config.php</code> по примеру.</p>
</section>
<?php render_footer(); ?>
