<?php
declare(strict_types=1);

function answer_fold(string $value): string
{
    $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function check_task_answer(array $task, string $answer): array
{
    $config = json_decode((string)$task['checker_config'], true);
    if (!is_array($config)) {
        return ['passed' => false, 'messages' => ['Настройки проверки повреждены.'], 'passed_count' => 0, 'total' => 1];
    }

    $folded = answer_fold($answer);
    $messages = [];
    $passedCount = 0;
    $total = 0;

    foreach (($config['forbidden'] ?? []) as $forbidden) {
        $total++;
        if (str_contains($folded, answer_fold((string)$forbidden))) {
            $messages[] = 'Найдена запрещённая или опасная конструкция.';
        } else {
            $passedCount++;
        }
    }

    if (($task['checker_type'] ?? '') === 'exact') {
        $total++;
        $expected = array_map('answer_fold', $config['expected'] ?? []);
        if (in_array($folded, $expected, true)) {
            $passedCount++;
        } else {
            $messages[] = 'Ответ не совпал с допустимым вариантом.';
        }
    } else {
        foreach (($config['checks'] ?? []) as $check) {
            $total++;
            $needle = answer_fold((string)($check['needle'] ?? ''));
            if ($needle !== '' && str_contains($folded, $needle)) {
                $passedCount++;
            } else {
                $messages[] = 'Не выполнен критерий: ' . (string)($check['label'] ?? 'обязательный элемент');
            }
        }
    }

    $passed = $total > 0 && $passedCount === $total;
    if ($passed) {
        $messages[] = 'Все автоматические критерии выполнены.';
    }

    return [
        'passed' => $passed,
        'messages' => $messages,
        'passed_count' => $passedCount,
        'total' => $total,
    ];
}
