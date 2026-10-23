<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
if (!app_installed()) {
    redirect('install.php');
}
$user = require_auth();
$userId = (int)$user['id'];

$stmt = db()->prepare('SELECT COUNT(*) FROM user_course_cards WHERE user_id=? AND due_at<=NOW()');
$stmt->execute([$userId]);
$dueCards = (int)$stmt->fetchColumn();

$overview = course_overview($userId);
$total = (int)$overview['total'];
$accepted = (int)$overview['accepted'];
$pending = max(0,$total-$accepted);

$reviewStmt = db()->prepare('SELECT COUNT(*) FROM course_card_reviews WHERE user_id=?');
$reviewStmt->execute([$userId]);
$reviews = (int)$reviewStmt->fetchColumn();

$activeStmt = db()->prepare('SELECT COUNT(*) FROM user_course_cards WHERE user_id=?');
$activeStmt->execute([$userId]);
$activeCards = (int)$activeStmt->fetchColumn();
$cardBank = (int)db()->query('SELECT COUNT(*) FROM course_cards')->fetchColumn();

db()->prepare('INSERT INTO daily_sessions (user_id,session_date,due_cards,pending_tasks)
    VALUES (?,CURDATE(),?,?)
    ON DUPLICATE KEY UPDATE due_cards=VALUES(due_cards),pending_tasks=VALUES(pending_tasks)')
    ->execute([$userId,$dueCards,$pending]);

$sections = course_sections_with_progress($userId);
$masteredSections = 0;
foreach ($sections as $section) {
    if ((int)$section['exercise_count'] > 0 && (int)$section['accepted_count'] === (int)$section['exercise_count']) {
        $masteredSections++;
    }
}

render_header('Сегодня','today');
?>
<section class="hero-panel">
    <div>
        <p class="eyebrow">Курс 447 + интервальные повторения</p>
        <h2>Вспомнить → решить → проверить → повторить</h2>
        <p>Карточки открываются по мере работы с темами. Шаги повторения: 10 минут, 1, 3, 7, 14, 30, 45 и 60 дней.</p>
    </div>
    <a class="button primary large" href="<?= h(url($dueCards > 0 ? 'cards.php' : 'exercise.php')) ?>">
        <?= $dueCards > 0 ? 'Повторить карточки' : 'Решать следующее задание' ?>
    </a>
</section>

<div class="metric-grid">
    <article class="metric"><strong><?= $dueCards ?></strong><span>карточек на сегодня</span></article>
    <article class="metric"><strong><?= $accepted ?>/<?= $total ?></strong><span>заданий принято</span></article>
    <article class="metric"><strong><?= $activeCards ?>/<?= $cardBank ?></strong><span>карточек открыто</span></article>
    <article class="metric"><strong><?= $masteredSections ?>/<?= count($sections) ?></strong><span>разделов завершено</span></article>
</div>

<section class="panel">
    <div class="section-heading">
        <div><p class="eyebrow">Единый учебный маршрут</p><h2>PHP + MySQL · 447 заданий</h2></div>
        <a class="button primary" href="<?= h(url('course.php')) ?>">Открыть программу</a>
    </div>
    <p>Каждое задание содержит теорию, точные исходные данные, ожидаемый результат, ограничения, самопроверку и подсказки. После первого принятого задания темы её карточки добавляются в интервальное повторение.</p>
</section>

<section class="panel">
    <div class="section-heading">
        <div><p class="eyebrow">Прогресс по разделам</p><h2><?= $pending ?> заданий впереди</h2></div>
        <span><?= $reviews ?> повторений выполнено</span>
    </div>
    <div class="topic-list compact">
        <?php foreach ($sections as $section):
            $count=(int)$section['exercise_count'];
            $done=(int)$section['accepted_count'];
            $percent=$count > 0 ? (int)round($done*100/$count) : 0;
        ?>
        <div class="topic-row">
            <span class="topic-code"><?= h($section['code']) ?></span>
            <div class="grow">
                <strong><?= h($section['title']) ?></strong>
                <div class="progress-track"><span style="width:<?= $percent ?>%"></span></div>
            </div>
            <span><?= $done ?>/<?= $count ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php render_footer(); ?>
