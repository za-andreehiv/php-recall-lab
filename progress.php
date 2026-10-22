<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
$user = require_auth();
$userId = (int)$user['id'];

$stmt = db()->prepare('SELECT t.*, COALESCE(p.solved_count,0) solved_count,
    COALESCE(p.status,"not_started") progress_status, p.mastered_at,
    prereq.code prerequisite_code,
    CASE WHEN t.prerequisite_id IS NULL OR pp.status="mastered" THEN 1 ELSE 0 END unlocked
    FROM topics t
    LEFT JOIN topic_progress p ON p.topic_id=t.id AND p.user_id=?
    LEFT JOIN topics prereq ON prereq.id=t.prerequisite_id
    LEFT JOIN topic_progress pp ON pp.topic_id=t.prerequisite_id AND pp.user_id=?
    ORDER BY t.sort_order');
$stmt->execute([$userId, $userId]);
$topics = $stmt->fetchAll();

$totalSolved = array_sum(array_map(fn($t) => (int)$t['solved_count'], $topics));
$totalRequired = array_sum(array_map(fn($t) => (int)$t['required_solutions'], $topics));
$percent = $totalRequired ? (int)round($totalSolved / $totalRequired * 100) : 0;

render_header('Программа и прогресс', 'progress');
?>
<section class="hero-panel slim">
    <div><p class="eyebrow">Освоение</p><h2><?= $percent ?>% практики выполнено</h2><p>Новая тема открывается после 10 принятых решений в предыдущей.</p></div>
    <div class="ring" style="--value:<?= $percent ?>"><span><?= $percent ?>%</span></div>
</section>
<section class="panel">
    <div class="topic-list">
        <?php foreach ($topics as $topic):
            $status = (string)$topic['progress_status'];
            $unlocked = (bool)$topic['unlocked'];
        ?>
        <article class="topic-row <?= !$unlocked ? 'locked' : '' ?>">
            <span class="topic-code"><?= h($topic['code']) ?></span>
            <div class="grow">
                <div class="topic-title-line">
                    <strong><?= h($topic['title']) ?></strong>
                    <?php if (!$unlocked): ?><span class="status">Закрыто до <?= h($topic['prerequisite_code']) ?></span>
                    <?php elseif ($status === 'mastered'): ?><span class="status mastered">Освоено</span>
                    <?php elseif ($status === 'in_progress'): ?><span class="status active">В работе</span>
                    <?php else: ?><span class="status">Не начато</span><?php endif; ?>
                </div>
                <div class="progress-track"><span style="width:<?= min(100,(int)$topic['solved_count']*10) ?>%"></span></div>
                <?php if ($topic['mastered_at']): ?><small>Освоено <?= h(date('d.m.Y', strtotime((string)$topic['mastered_at']))) ?></small><?php endif; ?>
            </div>
            <span class="score"><?= (int)$topic['solved_count'] ?>/<?= (int)$topic['required_solutions'] ?></span>
            <?php if ($unlocked): ?><a class="button ghost small" href="<?= h(url('task.php')) ?>">Открыть</a><?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<section class="callout">
    <strong>Как считается освоение:</strong> одна задача учитывается один раз после успешной автопроверки или ручной отметки «Принято». Повторные попытки не накручивают прогресс.
</section>
<?php render_footer(); ?>
