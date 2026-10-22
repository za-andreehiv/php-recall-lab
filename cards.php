<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
$user = require_auth();
$userId = (int)$user['id'];
$steps = review_steps();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $cardId = (int)($_POST['card_id'] ?? 0);
    $grade = (int)($_POST['grade'] ?? -1);
    if ($cardId < 1 || $grade < 0 || $grade > 3) {
        flash('error', 'Некорректная оценка карточки.');
        redirect('cards.php');
    }

    $stmt = db()->prepare('SELECT step_index FROM user_cards WHERE user_id = ? AND card_id = ? FOR UPDATE');
    db()->beginTransaction();
    try {
        $stmt->execute([$userId, $cardId]);
        $old = $stmt->fetchColumn();
        if ($old === false) {
            throw new RuntimeException('Карточка не найдена');
        }
        $old = (int)$old;
        $new = next_review_level($old, $grade);
        $dueAt = (new DateTimeImmutable())->modify('+' . $steps[$new]['seconds'] . ' seconds')->format('Y-m-d H:i:s');
        db()->prepare('UPDATE user_cards SET step_index = ?, due_at = ?, last_grade = ?, last_reviewed_at = NOW()
            WHERE user_id = ? AND card_id = ?')->execute([$new, $dueAt, $grade, $userId, $cardId]);
        db()->prepare('INSERT INTO card_reviews (user_id, card_id, grade, old_step, new_step)
            VALUES (?, ?, ?, ?, ?)')->execute([$userId, $cardId, $grade, $old, $new]);
        db()->commit();
        flash('success', 'Карточка запланирована через ' . $steps[$new]['label'] . '.');
    } catch (Throwable $e) {
        db()->rollBack();
        error_log($e->__toString());
        flash('error', 'Не удалось сохранить повторение.');
    }
    redirect('cards.php');
}

$sql = "SELECT c.*, t.code, t.title topic_title, uc.step_index
    FROM user_cards uc JOIN cards c ON c.id = uc.card_id JOIN topics t ON t.id = c.topic_id
    WHERE uc.user_id = ? AND uc.due_at <= NOW()
      AND (t.prerequisite_id IS NULL OR EXISTS (
          SELECT 1 FROM topic_progress p
          WHERE p.user_id = ? AND p.topic_id = t.prerequisite_id AND p.status = 'mastered'
      ))
    ORDER BY uc.due_at, c.id LIMIT 1";
$stmt = db()->prepare($sql);
$stmt->execute([$userId, $userId]);
$card = $stmt->fetch();

$remainingStmt = db()->prepare("SELECT COUNT(*) FROM user_cards uc JOIN cards c ON c.id=uc.card_id JOIN topics t ON t.id=c.topic_id
    WHERE uc.user_id=? AND uc.due_at<=NOW() AND (t.prerequisite_id IS NULL OR EXISTS (
      SELECT 1 FROM topic_progress p WHERE p.user_id=? AND p.topic_id=t.prerequisite_id AND p.status='mastered'))");
$remainingStmt->execute([$userId, $userId]);
$remaining = (int)$remainingStmt->fetchColumn();

render_header('Карточки', 'cards');
?>
<?php if (!$card): ?>
<section class="empty-state">
    <div class="success-orb">✓</div>
    <h2>На сейчас всё повторено</h2>
    <p>Следующая карточка появится в назначенный срок. Можно перейти к самостоятельной практике.</p>
    <a class="button primary" href="<?= h(url('task.php')) ?>">Перейти к задачам</a>
</section>
<?php else: ?>
<section class="study-shell">
    <div class="study-meta"><span><?= h($card['code']) ?> · <?= h($card['topic_title']) ?></span><span>осталось: <?= $remaining ?></span></div>
    <article class="flashcard">
        <p class="eyebrow">Вспомните ответ</p>
        <h2><?= h($card['question']) ?></h2>
        <button type="button" class="button primary" data-reveal-answer>Показать ответ</button>
        <div class="answer-box" data-answer hidden>
            <p class="eyebrow">Ответ</p>
            <p><?= nl2br(h($card['answer'])) ?></p>
        </div>
    </article>
    <div class="grade-panel" data-grades hidden>
        <p>Насколько уверенно вы ответили до открытия?</p>
        <div class="grade-grid">
            <?php
            $labels = [['Не помню','Сначала'],['Трудно','Не повышать'],['Хорошо','+1 шаг'],['Легко','+2 шага']];
            foreach ($labels as $grade => [$label,$note]):
                $next = next_review_level((int)$card['step_index'], $grade);
            ?>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="card_id" value="<?= (int)$card['id'] ?>">
                <input type="hidden" name="grade" value="<?= $grade ?>">
                <button class="grade grade-<?= $grade ?>" type="submit"><strong><?= h($label) ?></strong><span><?= h($steps[$next]['label']) ?></span></button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="intervals">
        <?php foreach ($steps as $i => $step): ?><span class="<?= $i === (int)$card['step_index'] ? 'current' : '' ?>"><?= h($step['label']) ?></span><?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php render_footer(); ?>
