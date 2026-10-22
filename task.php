<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
$user = require_auth();
$userId = (int)$user['id'];

function load_accessible_task(int $userId, ?int $taskId = null): ?array
{
    $whereId = $taskId ? 'AND task.id = ?' : '';
    $sql = "SELECT task.*, t.code, t.title topic_title
        FROM tasks task JOIN topics t ON t.id = task.topic_id
        WHERE (t.prerequisite_id IS NULL OR EXISTS (
            SELECT 1 FROM topic_progress p
            WHERE p.user_id = ? AND p.topic_id = t.prerequisite_id AND p.status = 'mastered'
        )) $whereId
        ORDER BY
          EXISTS(SELECT 1 FROM attempts a WHERE a.user_id = ? AND a.task_id = task.id AND a.auto_passed = 1),
          t.sort_order, task.sort_order
        LIMIT 1";
    $params = $taskId ? [$userId, $taskId, $userId] : [$userId, $userId];
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch() ?: null;
}

$requestedId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$task = load_accessible_task($userId, $requestedId);
$result = null;
$answer = '';
$attemptId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? 'check');

    if ($action === 'manual_review') {
        $attemptId = (int)($_POST['attempt_id'] ?? 0);
        $status = (string)($_POST['manual_status'] ?? '');
        $note = trim((string)($_POST['manual_note'] ?? ''));
        if (!in_array($status, ['needs_work', 'accepted'], true)) {
            flash('error', 'Выберите результат ручной проверки.');
            redirect('task.php');
        }
        $stmt = db()->prepare('SELECT a.*, task.topic_id FROM attempts a JOIN tasks task ON task.id=a.task_id
            WHERE a.id=? AND a.user_id=?');
        $stmt->execute([$attemptId, $userId]);
        $attempt = $stmt->fetch();
        if (!$attempt) {
            http_response_code(404);
            exit('Попытка не найдена');
        }
        db()->prepare('UPDATE attempts SET manual_status=?, manual_note=?, auto_passed=? WHERE id=? AND user_id=?')
            ->execute([$status, $note, $status === 'accepted' ? 1 : (int)$attempt['auto_passed'], $attemptId, $userId]);
        if ($status === 'accepted') {
            update_topic_progress($userId, (int)$attempt['topic_id']);
        }
        flash('success', $status === 'accepted' ? 'Ручная проверка принята и учтена.' : 'Комментарий сохранён — задание ждёт доработки.');
        redirect('task.php?id=' . (int)$attempt['task_id']);
    }

    $postedTaskId = (int)($_POST['task_id'] ?? 0);
    $task = load_accessible_task($userId, $postedTaskId);
    if (!$task) {
        http_response_code(404);
        exit('Задание недоступно');
    }
    $answer = trim((string)($_POST['answer'] ?? ''));
    if ($answer === '') {
        $result = ['passed' => false, 'messages' => ['Введите самостоятельный ответ.'], 'passed_count' => 0, 'total' => 1];
    } else {
        $result = check_task_answer($task, $answer);
        $stmt = db()->prepare('INSERT INTO attempts
            (user_id, task_id, topic_id, answer, auto_passed, result_json, hints_used)
            VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $userId, $task['id'], $task['topic_id'], $answer, $result['passed'] ? 1 : 0,
            json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            min(2, max(0, (int)($_POST['hints_used'] ?? 0))),
        ]);
        $attemptId = (int)db()->lastInsertId();
        if ($result['passed']) {
            update_topic_progress($userId, (int)$task['topic_id']);
        }
    }
}

$lastAttempt = null;
if ($task) {
    $stmt = db()->prepare('SELECT * FROM attempts WHERE user_id=? AND task_id=? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$userId, $task['id']]);
    $lastAttempt = $stmt->fetch() ?: null;
}

render_header('Самостоятельная практика', 'task');
?>
<?php if (!$task): ?>
<section class="empty-state"><div class="success-orb">✓</div><h2>Все открытые задачи решены</h2><p>Освойте текущую тему полностью — следующая откроется автоматически.</p><a class="button primary" href="<?= h(url('progress.php')) ?>">Посмотреть программу</a></section>
<?php else: ?>
<section class="study-shell task-shell">
    <div class="study-meta"><span><?= h($task['code']) ?> · <?= h($task['topic_title']) ?></span><span>задача <?= (int)$task['sort_order'] ?>/10</span></div>
    <article class="panel">
        <p class="eyebrow"><?= h($task['title']) ?></p>
        <h2><?= h($task['prompt']) ?></h2>
        <p class="muted">Решите без копирования. Проверка ищет обязательные смысловые или синтаксические элементы, но не запускает введённый код.</p>
        <form method="post" class="stack" id="task-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="check">
            <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
            <input type="hidden" name="hints_used" id="hints-used" value="0">
            <label>Ваш ответ
                <textarea id="answer" name="answer" rows="12" spellcheck="false" placeholder="<?= h($task['starter_text'] ?: 'Введите решение и объясните ход мысли…') ?>"><?= h($answer ?: (string)$task['starter_text']) ?></textarea>
            </label>
            <?php if ($task['hint1']): ?><details class="hint" data-hint><summary>Подсказка 1</summary><p><?= h($task['hint1']) ?></p></details><?php endif; ?>
            <?php if ($task['hint2']): ?><details class="hint" data-hint><summary>Подсказка 2</summary><p><?= h($task['hint2']) ?></p></details><?php endif; ?>
            <div class="button-row">
                <button class="button primary" type="submit">Проверить самостоятельно</button>
                <button class="button secondary" type="button" id="copy-review"
                    data-code="<?= h($task['code']) ?>"
                    data-title="<?= h($task['title']) ?>"
                    data-prompt="<?= h($task['prompt']) ?>">Скопировать для проверки в ChatGPT</button>
            </div>
        </form>
    </article>

    <?php if ($result): ?>
    <section class="result-card <?= $result['passed'] ? 'passed' : 'failed' ?>">
        <h2><?= $result['passed'] ? 'Автопроверка пройдена' : 'Есть что доработать' ?></h2>
        <p>Критерии: <?= (int)$result['passed_count'] ?> из <?= (int)$result['total'] ?>.</p>
        <ul><?php foreach ($result['messages'] as $message): ?><li><?= h($message) ?></li><?php endforeach; ?></ul>
        <?php if ($result['passed']): ?><a class="button primary" href="<?= h(url('task.php')) ?>">Следующая задача</a><?php endif; ?>
    </section>
    <?php endif; ?>

    <?php if ($lastAttempt): ?>
    <section class="panel">
        <p class="eyebrow">Обратная связь из десктопного приложения</p>
        <h2>Сохранить итог ручной проверки</h2>
        <form method="post" class="stack">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="manual_review">
            <input type="hidden" name="attempt_id" value="<?= (int)$lastAttempt['id'] ?>">
            <label>Результат
                <select name="manual_status" required>
                    <option value="">Выберите…</option>
                    <option value="needs_work" <?= $lastAttempt['manual_status'] === 'needs_work' ? 'selected' : '' ?>>Нужно доработать</option>
                    <option value="accepted" <?= $lastAttempt['manual_status'] === 'accepted' ? 'selected' : '' ?>>Принято</option>
                </select>
            </label>
            <label>Комментарий
                <textarea name="manual_note" rows="5" placeholder="Вставьте сюда мой разбор или краткий вывод"><?= h($lastAttempt['manual_note']) ?></textarea>
            </label>
            <button class="button secondary" type="submit">Сохранить проверку</button>
        </form>
    </section>
    <?php endif; ?>
</section>
<?php endif; ?>
<?php render_footer(); ?>
