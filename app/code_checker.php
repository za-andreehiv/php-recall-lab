<?php
declare(strict_types=1);

function course_fold(string $value): string
{
    $value = preg_replace('/\s+/u',' ',trim($value)) ?? trim($value);
    return function_exists('mb_strtolower') ? mb_strtolower($value,'UTF-8') : strtolower($value);
}

function analyze_course_answer(array $exercise, string $answer): array
{
    $config = json_decode((string)$exercise['checker_config'],true);
    if (!is_array($config)) {
        return ['passed'=>false,'messages'=>['Настройки проверки повреждены.'],'checks'=>[],'fingerprint'=>hash('sha256','config')];
    }

    $messages = [];
    $checks = [];
    $folded = course_fold($answer);
    $minLength = (int)($config['min_length'] ?? 20);

    $length = function_exists('mb_strlen') ? mb_strlen($answer,'UTF-8') : strlen($answer);
    $ok = $length >= $minLength;
    $checks[] = ['name'=>'Самостоятельный ответ','passed'=>$ok];
    if (!$ok) {
        $messages[] = 'Ответ слишком короткий: покажите ход решения, а не только название конструкции.';
    }

    foreach (($config['forbidden'] ?? []) as $forbidden) {
        $found = str_contains($folded,course_fold((string)$forbidden));
        $checks[] = ['name'=>'Без опасной конструкции '.$forbidden,'passed'=>!$found];
        if ($found) {
            $messages[] = 'Уберите опасную конструкцию «'.$forbidden.'». Она не нужна для учебного решения.';
        }
    }

    $type = (string)$exercise['exercise_type'];
    if (($config['syntax'] ?? false) && in_array($type,['php','project'],true)) {
        $code = trim($answer);
        if (!str_starts_with($code,'<?php')) {
            $code = "<?php\n".$code;
        }
        try {
            token_get_all($code,TOKEN_PARSE);
            $checks[] = ['name'=>'Синтаксис PHP','passed'=>true];
        } catch (ParseError $error) {
            $checks[] = ['name'=>'Синтаксис PHP','passed'=>false];
            $messages[] = 'PHP не разобрал код: '.$error->getMessage();
        }
    }

    if ($type === 'sql') {
        $sql = preg_replace('/--.*$/m','',$answer) ?? $answer;
        $startsCorrectly = (bool)preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|CREATE|ALTER|WITH)\b/i',$sql);
        $checks[] = ['name'=>'Начало SQL-запроса','passed'=>$startsCorrectly];
        if (!$startsCorrectly) {
            $messages[] = 'Запрос должен начинаться с SQL-команды SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER или WITH.';
        }
        if (preg_match('/\b(UPDATE|DELETE)\b/i',$sql) && !preg_match('/\bWHERE\b/i',$sql)) {
            $checks[] = ['name'=>'Безопасный WHERE','passed'=>false];
            $messages[] = 'Для UPDATE или DELETE добавьте WHERE, чтобы не изменить всю таблицу.';
        }
    }

    foreach (($config['checks'] ?? []) as $criterion) {
        $needle = (string)($criterion['needle'] ?? '');
        $present = $needle !== '' && str_contains($folded,course_fold($needle));
        $checks[] = ['name'=>(string)($criterion['label'] ?? $needle),'passed'=>$present];
        if (!$present) {
            $messages[] = 'Не найден обязательный элемент: '.(string)($criterion['label'] ?? $needle).'.';
        }
    }

    if (str_contains($folded,'todo') || str_contains($folded,'готовое решение')) {
        $checks[] = ['name'=>'Нет заглушек','passed'=>false];
        $messages[] = 'Удалите заглушку и завершите самостоятельное решение.';
    }

    $passed = $checks !== [] && !in_array(false,array_column($checks,'passed'),true);
    if ($passed) {
        $messages[] = 'Синтаксис и автоматические критерии пройдены. Объясните решение своими словами перед переходом дальше.';
    }
    $fingerprint = hash('sha256',implode('|',array_filter($messages,fn($m)=>!str_starts_with($m,'Синтаксис и'))));

    return ['passed'=>$passed,'messages'=>$messages,'checks'=>$checks,'fingerprint'=>$fingerprint];
}

function save_course_submission(int $userId, array $exercise, string $answer, array $result): int
{
    $status = $result['passed'] ? 'passed' : 'needs_work';
    $stmt = db()->prepare('INSERT INTO exercise_submissions
      (user_id,exercise_id,answer,status,result_json,error_fingerprint)
      VALUES (?,?,?,?,?,?)');
    $stmt->execute([
        $userId,$exercise['id'],$answer,$status,
        json_encode($result,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
        $result['passed'] ? null : $result['fingerprint']
    ]);
    $submissionId = (int)db()->lastInsertId();

    db()->prepare("INSERT INTO user_exercise_progress
      (user_id,exercise_id,best_status,attempts,last_attempt_at,accepted_at)
      VALUES (?,?,?,1,NOW(),?)
      ON DUPLICATE KEY UPDATE
        attempts=attempts+1,last_attempt_at=NOW(),
        accepted_at=IF(VALUES(best_status)='passed',COALESCE(accepted_at,VALUES(accepted_at)),accepted_at),
        best_status=IF(best_status='passed','passed',VALUES(best_status))")
      ->execute([$userId,$exercise['id'],$status,$result['passed'] ? date('Y-m-d H:i:s') : null]);

    if ($result['passed']) {
        db()->prepare('UPDATE mistake_cards SET active=0 WHERE user_id=? AND exercise_id=?')
          ->execute([$userId,$exercise['id']]);
    } else {
        $question = 'Что нужно проверить в упражнении '.$exercise['code'].' перед следующей попыткой?';
        $answerText = implode("\n",$result['messages']);
        $errorKey = hash('sha256',$userId.'|'.$exercise['id'].'|'.$result['fingerprint']);
        db()->prepare("INSERT INTO mistake_cards
          (user_id,exercise_id,error_key,question,answer,due_at,active)
          VALUES (?,?,?,?,?,NOW(),1)
          ON DUPLICATE KEY UPDATE question=VALUES(question),answer=VALUES(answer),due_at=NOW(),active=1")
          ->execute([$userId,$exercise['id'],$errorKey,$question,$answerText]);
    }
    return $submissionId;
}
