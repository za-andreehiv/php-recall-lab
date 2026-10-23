<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/course_seed.php';
$user = require_auth();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        foreach (['migrations/v2.sql','migrations/v2_1.sql'] as $migration) {
            $sql = file_get_contents(__DIR__.'/'.$migration);
            if ($sql === false) {
                throw new RuntimeException('Не найден '.$migration);
            }
            foreach (explode('-- SPLIT --',$sql) as $statement) {
                $statement = trim($statement);
                if ($statement !== '') {
                    db()->exec($statement);
                }
            }
        }
        db()->beginTransaction();
        seed_prime_course(db());
        db()->prepare('INSERT INTO app_settings (setting_key,value_text) VALUES ("course_v21_installed",?)
          ON DUPLICATE KEY UPDATE value_text=VALUES(value_text)')->execute([date('c')]);
        db()->commit();
        flash('success','Курс обновлён: создано '.prime_course_total().' самостоятельных заданий.');
        redirect('course.php');
    } catch (Throwable $e) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        error_log($e->__toString());
        $error = 'Обновление не завершено. Проверьте права пользователя MySQL на CREATE/ALTER/INSERT.';
    }
}

$installed = setting('course_v21_installed','') !== '';

render_header('Обновление курса');
?>
<section class="auth-card">
  <?php if ($error): ?><div class="flash error"><?= h($error) ?></div><?php endif; ?>
  <?php if ($installed): ?>
    <div class="flash success">Версия 2.1 уже установлена: вся теория и практика находятся внутри платформы.</div>
    <a class="button primary" href="<?= h(url('course.php')) ?>">Открыть курс</a>
  <?php else: ?>
    <div class="callout"><strong>Что изменится:</strong> обновится внутренняя теория и будут удалены все внешние ссылки. Старые результаты и карточки сохранятся.</div>
    <form method="post"><?= csrf_field() ?><button class="button primary" type="submit">Установить обновление курса</button></form>
  <?php endif; ?>
</section>
<?php render_footer(); ?>
