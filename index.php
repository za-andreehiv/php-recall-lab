<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
if (!app_installed()) {
    redirect('install.php');
}
$user = require_auth();
$userId = (int)$user['id'];

$unlockSql = "(t.prerequisite_id IS NULL OR EXISTS (
    SELECT 1 FROM topic_progress pp
    WHERE pp.user_id = ? AND pp.topic_id = t.prerequisite_id AND pp.status = 'mastered'
))";

$stmt = db()->prepare("SELECT COUNT(*) FROM user_cards uc
    JOIN cards c ON c.id = uc.card_id JOIN topics t ON t.id = c.topic_id
    WHERE uc.user_id = ? AND uc.due_at <= NOW() AND $unlockSql");
$stmt->execute([$userId, $userId]);
$dueCards = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM tasks task JOIN topics t ON t.id = task.topic_id
    WHERE $unlockSql AND NOT EXISTS (
        SELECT 1 FROM attempts a WHERE a.user_id = ? AND a.task_id = task.id AND a.auto_passed = 1
    )");
$stmt->execute([$userId, $userId]);
$pendingTasks = (int)$stmt->fetchColumn();

$statsStmt = db()->prepare("SELECT
    (SELECT COUNT(DISTINCT task_id) FROM attempts WHERE user_id = ? AND auto_passed = 1) solved,
    (SELECT COUNT(*) FROM card_reviews WHERE user_id = ?) reviews,
    (SELECT COUNT(*) FROM topic_progress WHERE user_id = ? AND status = 'mastered') mastered");
$statsStmt->execute([$userId, $userId, $userId]);
$stats = $statsStmt->fetch();

db()->prepare('INSERT INTO daily_sessions (user_id, session_date, due_cards, pending_tasks)
    VALUES (?, CURDATE(), ?, ?)
    ON DUPLICATE KEY UPDATE due_cards = VALUES(due_cards), pending_tasks = VALUES(pending_tasks)')
    ->execute([$userId, $dueCards, $pendingTasks]);

$topics = db()->prepare('SELECT t.*, COALESCE(p.solved_count, 0) solved_count,
    COALESCE(p.status, "not_started") progress_status
    FROM topics t LEFT JOIN topic_progress p ON p.topic_id = t.id AND p.user_id = ?
    ORDER BY t.sort_order');
$topics->execute([$userId]);
$topicRows = $topics->fetchAll();

render_header('Сегодня', 'today');
?>
<section class="hero-panel">
    <div>
        <p class="eyebrow">Ваш учебный цикл</p>
        <h2>Вспомнить → решить → проверить → повторить</h2>
        <p>Сначала отвечайте без подсказки. Если автоматическая проверка не объясняет ошибку — скопируйте контекст задания в десктопное приложение.</p>
    </div>
    <a class="button primary large" href="<?= h(url($dueCards > 0 ? 'cards.php' : 'task.php')) ?>">
        <?= $dueCards > 0 ? 'Повторить карточки' : 'Решать задачу' ?>
    </a>
</section>

<div class="metric-grid">
    <article class="metric"><strong><?= $dueCards ?></strong><span>карточек на сегодня</span></article>
    <article class="metric"><strong><?= $pendingTasks ?></strong><span>открытых задач</span></article>
    <article class="metric"><strong><?= (int)$stats['solved'] ?></strong><span>задач решено</span></article>
    <article class="metric"><strong><?= (int)$stats['mastered'] ?>/14</strong><span>тем освоено</span></article>
</div>

<section class="panel">
    <div class="section-heading">
        <div><p class="eyebrow">Фундамент</p><h2>Первые 100 задач</h2></div>
        <span class="status mastered">Пройдено: <?= h(setting('foundation_tasks_completed', '100')) ?></span>
    </div>
    <p>Этот материал считается пройденным и будет постепенно переноситься в карточки по мере добавления точных формулировок.</p>
</section>

<section class="panel">
    <div class="section-heading"><div><p class="eyebrow">Маршрут</p><h2>Темы 11.01–12.04</h2></div><a href="<?= h(url('progress.php')) ?>">Вся программа →</a></div>
    <div class="topic-list compact">
        <?php foreach (array_slice($topicRows, 0, 5) as $topic): ?>
            <div class="topic-row">
                <span class="topic-code"><?= h($topic['code']) ?></span>
                <div class="grow"><strong><?= h($topic['title']) ?></strong>
                    <div class="progress-track"><span style="width:<?= min(100, (int)$topic['solved_count'] * 10) ?>%"></span></div>
                </div>
                <span><?= (int)$topic['solved_count'] ?>/10</span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php render_footer(); ?>
