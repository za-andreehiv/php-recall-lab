<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/app/bootstrap.php';

$lockName = 'php_recall_lab_course447_daily';
$lockStmt = db()->prepare('SELECT GET_LOCK(?,0)');
$lockStmt->execute([$lockName]);
if ((int)$lockStmt->fetchColumn() !== 1) {
    fwrite(STDERR,"Задание уже выполняется.\n");
    exit(0);
}

$runId = 0;
try {
    db()->prepare("INSERT INTO cron_runs (job_name,status) VALUES ('course447_daily','running')")->execute();
    $runId = (int)db()->lastInsertId();

    $users = db()->query('SELECT id FROM users WHERE is_active=1')->fetchAll();
    $upsert = db()->prepare('INSERT INTO daily_sessions (user_id,session_date,due_cards,pending_tasks)
        VALUES (?,CURDATE(),?,?)
        ON DUPLICATE KEY UPDATE due_cards=VALUES(due_cards),pending_tasks=VALUES(pending_tasks)');

    foreach ($users as $row) {
        $userId = (int)$row['id'];

        $cards = db()->prepare('SELECT COUNT(*) FROM user_course_cards WHERE user_id=? AND due_at<=NOW()');
        $cards->execute([$userId]);

        $tasks = db()->prepare("SELECT COUNT(*) FROM course_exercises e
            WHERE NOT EXISTS (
              SELECT 1 FROM user_exercise_progress p
              WHERE p.user_id=? AND p.exercise_id=e.id AND p.best_status='passed'
            )");
        $tasks->execute([$userId]);

        $upsert->execute([$userId,(int)$cards->fetchColumn(),(int)$tasks->fetchColumn()]);
    }

    db()->prepare('INSERT INTO app_settings (setting_key,value_text) VALUES ("last_cron_at",?)
        ON DUPLICATE KEY UPDATE value_text=VALUES(value_text)')->execute([date('c')]);
    db()->prepare("UPDATE cron_runs SET status='success',details=?,finished_at=NOW() WHERE id=?")
        ->execute(['Обработано пользователей: '.count($users),$runId]);

    echo json_encode(['ok'=>true,'users'=>count($users),'at'=>date('c')],JSON_UNESCAPED_UNICODE).PHP_EOL;
} catch (Throwable $e) {
    if ($runId) {
        db()->prepare("UPDATE cron_runs SET status='failed',details=?,finished_at=NOW() WHERE id=?")
            ->execute([substr($e->getMessage(),0,1000),$runId]);
    }
    error_log($e->__toString());
    fwrite(STDERR,"Daily job failed.\n");
    exit(1);
} finally {
    db()->prepare('SELECT RELEASE_LOCK(?)')->execute([$lockName]);
}
