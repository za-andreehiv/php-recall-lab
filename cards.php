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
        flash('error','Некорректная оценка карточки.');
        redirect('cards.php');
    }

    db()->beginTransaction();
    try {
        $stmt = db()->prepare('SELECT step_index FROM user_course_cards WHERE user_id=? AND card_id=? FOR UPDATE');
        $stmt->execute([$userId,$cardId]);
        $old = $stmt->fetchColumn();
        if ($old === false) {
            throw new RuntimeException('Карточка не найдена');
        }
        $old = (int)$old;
        $new = next_review_level($old,$grade);
        $dueAt = (new DateTimeImmutable())->modify('+'.$steps[$new]['seconds'].' seconds')->format('Y-m-d H:i:s');

        db()->prepare('UPDATE user_course_cards SET step_index=?,due_at=?,last_grade=?,last_reviewed_at=NOW()
            WHERE user_id=? AND card_id=?')->execute([$new,$dueAt,$grade,$userId,$cardId]);
        db()->prepare('INSERT INTO course_card_reviews (user_id,card_id,grade,old_step,new_step)
            VALUES (?,?,?,?,?)')->execute([$userId,$cardId,$grade,$old,$new]);
        db()->commit();
        flash('success','Карточка запланирована через '.$steps[$new]['label'].'.');
    } catch (Throwable $e) {
        if (db()->inTransaction()) {
            db()->rollBack();
        }
        error_log($e->__toString());
        flash('error','Не удалось сохранить повторение.');
    }
    redirect('cards.php');
}

$stmt = db()->prepare("SELECT c.*,l.title lesson_title,s.code section_code,s.title section_title,uc.step_index
    FROM user_course_cards uc
    JOIN course_cards c ON c.id=uc.card_id
    JOIN course_lessons l ON l.id=c.lesson_id
    JOIN course_sections s ON s.id=l.section_id
    WHERE uc.user_id=? AND uc.due_at<=NOW()
    ORDER BY uc.due_at,s.sort_order,l.sort_order,
      FIELD(c.card_type,'meaning','syntax','check') LIMIT 1");
$stmt->execute([$userId]);
$card = $stmt->fetch();

$remainingStmt = db()->prepare('SELECT COUNT(*) FROM user_course_cards WHERE user_id=? AND due_at<=NOW()');
$remainingStmt->execute([$userId]);
$remaining = (int)$remainingStmt->fetchColumn();

$bankTotal = (int)db()->query('SELECT COUNT(*) FROM course_cards')->fetchColumn();
$activeStmt = db()->prepare('SELECT COUNT(*) FROM user_course_cards WHERE user_id=?');
$activeStmt->execute([$userId]);
$activeTotal = (int)$activeStmt->fetchColumn();

render_header('Карточки курса', 'cards');
?>
<?php if (!$card): ?>
<section class="empty-state">
    <div class="success-orb">✓</div>
    <h2>На сейчас всё повторено</h2>
    <p>В банке <?= $bankTotal ?> карточки. Сейчас открыто <?= $activeTotal ?>: новые карточки темы добавляются после первого принятого задания по ней.</p>
    <a class="button primary" href="<?= h(url('exercise.php')) ?>">Перейти к следующему заданию</a>
</section>
<?php else: ?>
<section class="study-shell">
    <div class="study-meta">
        <span><?= h($card['section_code']) ?> · <?= h($card['section_title']) ?> · <?= h($card['lesson_title']) ?></span>
        <span>осталось сегодня: <?= $remaining ?></span>
    </div>
    <article class="flashcard">
        <p class="eyebrow"><?= h([
            'meaning'=>'Понимание',
            'syntax'=>'Синтаксис',
            'check'=>'Самопроверка',
        ][$card['card_type']] ?? 'Повторение') ?></p>
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
                $next = next_review_level((int)$card['step_index'],$grade);
            ?>
            <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="card_id" value="<?= (int)$card['id'] ?>">
                <input type="hidden" name="grade" value="<?= $grade ?>">
                <button class="grade grade-<?= $grade ?>" type="submit">
                    <strong><?= h($label) ?></strong><span><?= h($steps[$next]['label']) ?></span>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="intervals">
        <?php foreach ($steps as $i => $step): ?>
            <span class="<?= $i === (int)$card['step_index'] ? 'current' : '' ?>"><?= h($step['label']) ?></span>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php render_footer(); ?>
