<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
$user = require_auth();
$userId = (int)$user['id'];

function load_course_exercise(int $id): ?array
{
    $stmt = db()->prepare("SELECT e.*,l.title lesson_title,l.concept_summary,s.id section_id,s.code section_code,s.title section_title,
      d.goal,d.theory_text,d.input_data,d.expected_result,d.constraints_text,d.self_check,d.hint1,d.hint2
      FROM course_exercises e JOIN course_lessons l ON l.id=e.lesson_id
      JOIN course_sections s ON s.id=l.section_id
      LEFT JOIN exercise_details d ON d.exercise_id=e.id
      WHERE e.id=?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

$exercise = null;
if (isset($_GET['id'])) {
    $exercise = load_course_exercise((int)$_GET['id']);
} elseif (isset($_GET['section'])) {
    $sectionId = (int)$_GET['section'];
    $stmt = db()->prepare("SELECT e.id FROM course_exercises e JOIN course_lessons l ON l.id=e.lesson_id
      LEFT JOIN user_exercise_progress p ON p.exercise_id=e.id AND p.user_id=?
      WHERE l.section_id=? AND COALESCE(p.best_status,'not_started')<>'passed'
      ORDER BY e.sort_order LIMIT 30");
    $stmt->execute([$userId,$sectionId]);
    foreach ($stmt->fetchAll() as $row) {
        if (course_exercise_unlocked($userId,(int)$row['id'])) {
            $exercise = load_course_exercise((int)$row['id']);
            break;
        }
    }
} else {
    $exercise = next_course_exercise($userId);
}

if ($exercise && !course_exercise_unlocked($userId,(int)$exercise['id'])) {
    flash('error','Сначала завершите предыдущий блок: не менее 8 из 10 заданий.');
    redirect('course.php');
}

$result = null;
$answer = '';
$submissionId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? 'check');

    if ($action === 'manual_review') {
        $submissionId = (int)($_POST['submission_id'] ?? 0);
        $status = (string)($_POST['manual_status'] ?? '');
        $note = trim((string)($_POST['manual_note'] ?? ''));
        $stmt = db()->prepare('SELECT * FROM exercise_submissions WHERE id=? AND user_id=?');
        $stmt->execute([$submissionId,$userId]);
        $submission = $stmt->fetch();
        if (!$submission || !in_array($status,['needs_work','manual_accepted'],true)) {
            http_response_code(400);
            exit('Некорректная ручная проверка');
        }
        db()->prepare('UPDATE exercise_submissions SET status=?,manual_note=? WHERE id=?')
          ->execute([$status,$note,$submissionId]);
        if ($status === 'manual_accepted') {
            db()->prepare("INSERT INTO user_exercise_progress
              (user_id,exercise_id,best_status,attempts,last_attempt_at,accepted_at)
              VALUES (?,?,'passed',1,NOW(),NOW())
              ON DUPLICATE KEY UPDATE best_status='passed',accepted_at=NOW(),last_attempt_at=NOW()")
              ->execute([$userId,$submission['exercise_id']]);
            activate_course_cards_for_exercise($userId,(int)$submission['exercise_id']);
            db()->prepare('UPDATE mistake_cards SET active=0 WHERE user_id=? AND exercise_id=?')
              ->execute([$userId,$submission['exercise_id']]);
        }
        flash('success',$status === 'manual_accepted' ? 'Проверка из чата учтена: задание принято.' : 'Комментарий сохранён, задание оставлено на доработку.');
        redirect('exercise.php?id='.(int)$submission['exercise_id']);
    }

    $exercise = load_course_exercise((int)($_POST['exercise_id'] ?? 0));
    if (!$exercise || !course_exercise_unlocked($userId,(int)$exercise['id'])) {
        http_response_code(404);
        exit('Задание недоступно');
    }
    $answer = trim((string)($_POST['answer'] ?? ''));
    $result = analyze_course_answer($exercise,$answer);
    $submissionId = save_course_submission($userId,$exercise,$answer,$result);
}

$last = null;
if ($exercise) {
    $stmt = db()->prepare('SELECT * FROM exercise_submissions WHERE user_id=? AND exercise_id=? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$userId,$exercise['id']]);
    $last = $stmt->fetch() ?: null;
}

$checkerCriteria = [];
if ($exercise) {
    $checkerConfig = json_decode((string)$exercise['checker_config'],true);
    if (is_array($checkerConfig)) {
        foreach (($checkerConfig['checks'] ?? []) as $criterion) {
            $label = trim((string)($criterion['label'] ?? $criterion['needle'] ?? ''));
            if ($label !== '') {
                $checkerCriteria[] = $label;
            }
        }
        if (($checkerConfig['syntax'] ?? false) === true) {
            array_unshift($checkerCriteria,'корректный синтаксис PHP');
        }
        if ((string)$exercise['exercise_type'] === 'sql') {
            array_unshift($checkerCriteria,'корректное начало и безопасная структура SQL');
        }
    }
}

render_header('Решение задания', 'course');
?>
<?php if (!$exercise): ?>
<section class="empty-state"><div class="success-orb">✓</div><h2>Все доступные задания приняты</h2><p>Откройте программу или повторите карточки ошибок.</p><a class="button primary" href="<?= h(url('course.php')) ?>">К программе</a></section>
<?php else: ?>
<section class="study-shell task-shell">
  <div class="study-meta"><span><?= h($exercise['section_code']) ?> · <?= h($exercise['section_title']) ?></span><span><?= h($exercise['code']) ?> · сложность <?= (int)$exercise['difficulty'] ?>/5</span></div>
  <article class="panel">
    <p class="eyebrow"><?= h($exercise['exercise_type']) ?> · <?= h($exercise['lesson_title']) ?></p>
    <h2><?= h($exercise['title']) ?></h2>

    <section class="lesson-block goal-block">
      <h3>Цель</h3>
      <p><?= nl2br(h((string)($exercise['goal'] ?: 'Закрепить тему на самостоятельном примере.'))) ?></p>
    </section>

    <details class="hint theory-panel" open>
      <summary>Вся необходимая теория</summary>
      <div class="theory-text"><?= nl2br(h((string)($exercise['theory_text'] ?: $exercise['concept_summary']))) ?></div>
    </details>

    <section class="lesson-block">
      <h3>Исходные данные</h3>
      <pre class="code-block"><?= h((string)($exercise['input_data'] ?: 'Исходные данные указаны в условии.')) ?></pre>
    </section>

    <section class="lesson-block">
      <h3>Задача</h3>
      <div class="task-steps"><?= nl2br(h($exercise['prompt'])) ?></div>
    </section>

    <section class="lesson-block expected-block">
      <h3>Ожидаемый результат</h3>
      <pre class="code-block"><?= h((string)($exercise['expected_result'] ?: 'Результат должен соответствовать условию.')) ?></pre>
    </section>

    <section class="lesson-block">
      <h3>Ограничения</h3>
      <div class="task-steps"><?= nl2br(h((string)($exercise['constraints_text'] ?: 'Используйте только изученные конструкции.'))) ?></div>
    </section>

    <form method="post" class="stack solution-form">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="check">
      <input type="hidden" name="exercise_id" value="<?= (int)$exercise['id'] ?>">
      <label><strong>Ваше самостоятельное решение</strong>
        <textarea id="answer" name="answer" rows="18" spellcheck="false"><?= h($answer ?: (string)$exercise['starter_code']) ?></textarea>
      </label>
      <div class="button-row">
        <button class="button primary" type="submit">Проверить</button>
        <button class="button secondary" type="button" id="copy-review"
          data-code="<?= h($exercise['code']) ?>" data-title="<?= h($exercise['title']) ?>"
          data-prompt="<?= h($exercise['prompt']."\n\nИсходные данные:\n".(string)$exercise['input_data']."\n\nОжидаемый результат:\n".(string)$exercise['expected_result']) ?>">Скопировать пакет для чата</button>
      </div>
      <p class="muted">PHP-код не выполняется: проверяются синтаксис, структура и обязательные элементы. SQL-запросы также не запускаются на боевой базе.</p>
    </form>

    <details class="hint">
      <summary>Самопроверка перед отправкой</summary>
      <div class="theory-text"><?= nl2br(h((string)$exercise['self_check'])) ?></div>
    </details>

    <details class="hint">
      <summary>Что проверит сайт</summary>
      <?php if ($checkerCriteria): ?>
      <ul class="criteria-list"><?php foreach ($checkerCriteria as $criterion): ?><li><?= h($criterion) ?></li><?php endforeach; ?></ul>
      <?php else: ?><p>Полноту ответа, отсутствие заглушек и безопасную структуру решения.</p><?php endif; ?>
    </details>

    <details class="hint">
      <summary>Подсказка 1</summary>
      <p><?= h((string)$exercise['hint1']) ?></p>
    </details>
    <details class="hint">
      <summary>Подсказка 2</summary>
      <p><?= h((string)$exercise['hint2']) ?></p>
    </details>
  </article>

  <?php if ($result): ?>
  <section class="result-card <?= $result['passed'] ? 'passed' : 'failed' ?>">
    <h2><?= $result['passed'] ? 'Автопроверка пройдена' : 'Найдены ошибки или пропуски' ?></h2>
    <ul class="check-list">
      <?php foreach ($result['checks'] as $check): ?><li class="<?= $check['passed'] ? 'ok' : 'bad' ?>"><?= $check['passed'] ? '✓' : '×' ?> <?= h($check['name']) ?></li><?php endforeach; ?>
    </ul>
    <?php foreach ($result['messages'] as $message): ?><p><?= h($message) ?></p><?php endforeach; ?>
    <?php if ($result['passed']): ?><a class="button primary" href="<?= h(url('exercise.php')) ?>">Следующее задание</a><?php endif; ?>
  </section>
  <?php endif; ?>

  <?php if ($last): ?>
  <section class="panel">
    <p class="eyebrow">Крайний случай — проверка в чате</p>
    <h2>Сохранить итог разбора</h2>
    <p>Скопируйте пакет, пришлите его в этот чат, затем вставьте мой комментарий и отметьте итог.</p>
    <form method="post" class="stack">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="manual_review">
      <input type="hidden" name="submission_id" value="<?= (int)$last['id'] ?>">
      <label>Статус
        <select name="manual_status" required>
          <option value="">Выберите…</option>
          <option value="needs_work">Нужно доработать</option>
          <option value="manual_accepted">Принято в чате</option>
        </select>
      </label>
      <label>Комментарий проверки<textarea name="manual_note" rows="5"><?= h($last['manual_note']) ?></textarea></label>
      <button class="button secondary" type="submit">Сохранить результат</button>
    </form>
  </section>
  <?php endif; ?>
</section>
<?php endif; ?>
<?php render_footer(); ?>
