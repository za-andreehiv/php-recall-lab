<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/course_seed.php';
$user = require_auth();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        foreach (['migrations/v2.sql','migrations/v2_1.sql','migrations/v2_2.sql'] as $migration) {
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
        db()->prepare('INSERT INTO app_settings (setting_key,value_text) VALUES ("course_v22_installed",?)
          ON DUPLICATE KEY UPDATE value_text=VALUES(value_text)')->execute([date('c')]);
        db()->commit();
        flash('success','Курс обновлён: подробно оформлено '.prime_course_total().' самостоятельных заданий.');
        redirect('course.php');
    } catch (Throwable $e) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        error_log($e->__toString());
        $error = 'Обновление не завершено. Проверьте права пользователя MySQL на CREATE/ALTER/INSERT.';
    }
}

$installed = setting('course_v22_installed','') !== '';

render_header('Обновление курса');
?>
<section class="auth-card">
  <?php if ($error): ?><div class="flash error"><?= h($error) ?></div><?php endif; ?>
  <?php if ($installed): ?>
    <div class="flash success">Версия 2.2 уже установлена: все 447 заданий содержат теорию, точные данные, ожидаемый результат, самопроверку и подсказки.</div>
    <a class="button primary" href="<?= h(url('course.php')) ?>">Открыть курс</a>
  <?php else: ?>
    <div class="callout"><strong>Что изменится:</strong> все 447 заданий будут перестроены по подробному шаблону; автопроверка не примет неизменённый стартовый код. Старые результаты, попытки и карточки повторения сохранятся.</div>
    <form method="post"><?= csrf_field() ?><button class="button primary" type="submit">Установить обновление курса</button></form>
  <?php endif; ?>
</section>
<?php render_footer(); ?>
