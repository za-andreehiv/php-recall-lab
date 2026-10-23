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
        flash('error','Некорректная оценка.');
        redirect('mistakes.php');
    }
    db()->beginTransaction();
    try {
        $stmt = db()->prepare('SELECT step_index FROM mistake_cards WHERE id=? AND user_id=? AND active=1 FOR UPDATE');
        $stmt->execute([$cardId,$userId]);
        $old = $stmt->fetchColumn();
        if ($old === false) {
            throw new RuntimeException('Карточка не найдена');
        }
        $new = next_review_level((int)$old,$grade);
        $due = (new DateTimeImmutable())->modify('+'.$steps[$new]['seconds'].' seconds')->format('Y-m-d H:i:s');
        db()->prepare('UPDATE mistake_cards SET step_index=?,due_at=?,last_grade=?,last_reviewed_at=NOW() WHERE id=? AND user_id=?')
          ->execute([$new,$due,$grade,$cardId,$userId]);
        db()->commit();
        flash('success','Ошибка вернётся через '.$steps[$new]['label'].'.');
    } catch (Throwable $e) {
        db()->rollBack();
        error_log($e->__toString());
        flash('error','Не удалось сохранить повторение.');
    }
    redirect('mistakes.php');
}

$stmt = db()->prepare("SELECT m.*,e.code,e.title FROM mistake_cards m
  JOIN course_exercises e ON e.id=m.exercise_id
  WHERE m.user_id=? AND m.active=1 AND m.due_at<=NOW()
  ORDER BY m.due_at,m.id LIMIT 1");
$stmt->execute([$userId]);
$card = $stmt->fetch();

$count = db()->prepare('SELECT COUNT(*) FROM mistake_cards WHERE user_id=? AND active=1 AND due_at<=NOW()');
$count->execute([$userId]);
$due = (int)$count->fetchColumn();

render_header('Повторение ошибок', 'mistakes');
?>
<?php if (!$card): ?>
<section class="empty-state"><div class="success-orb">✓</div><h2>Ошибок к повторению сейчас нет</h2><p>Новая карточка появится после неудачной проверки или в назначенный интервал.</p><a class="button primary" href="<?= h(url('course.php')) ?>">Вернуться к курсу</a></section>
<?php else: ?>
<section class="study-shell">
  <div class="study-meta"><span><?= h($card['code']) ?> · <?= h($card['title']) ?></span><span>к повторению: <?= $due ?></span></div>
  <article class="flashcard">
    <p class="eyebrow">Карточка ошибки</p>
    <h2><?= h($card['question']) ?></h2>
    <button type="button" class="button primary" data-reveal-answer>Показать разбор</button>
    <div class="answer-box" data-answer hidden><p><?= nl2br(h($card['answer'])) ?></p><a href="<?= h(url('exercise.php?id='.(int)$card['exercise_id'])) ?>">Открыть исходное задание →</a></div>
  </article>
  <div class="grade-panel" data-grades hidden>
    <p>Смогли объяснить ошибку без подсказки?</p>
    <div class="grade-grid">
    <?php foreach ([['Не помню'],['Трудно'],['Хорошо'],['Легко']] as $grade=>$label): $next=next_review_level((int)$card['step_index'],$grade); ?>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="card_id" value="<?= (int)$card['id'] ?>"><input type="hidden" name="grade" value="<?= $grade ?>"><button class="grade grade-<?= $grade ?>" type="submit"><strong><?= h($label[0]) ?></strong><span><?= h($steps[$next]['label']) ?></span></button></form>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php render_footer(); ?>
