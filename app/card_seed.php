<?php
declare(strict_types=1);

function course_card_total(): int
{
    $lessons = 0;
    foreach (prime_course_blueprint() as $section) {
        $lessons += count($section[4]);
    }
    return $lessons * 3;
}

function course_card_purpose(string $sectionCode): string
{
    return [
        'BAS'=>'выражать вычисления, значения и вывод программы',
        'ARR'=>'хранить и изменять наборы связанных данных',
        'COND'=>'выбирать ветку выполнения по точному условию',
        'LOOP'=>'повторять обработку элементов без копирования кода',
        'MULTI'=>'обрабатывать вложенные структуры данных',
        'MATH'=>'выполнять числовые вычисления предсказуемо',
        'STR'=>'безопасно преобразовывать и анализировать строки',
        'AFN'=>'искать, фильтровать, сортировать и преобразовывать массивы',
        'TIME'=>'работать со случайностью, датами и интервалами времени',
        'FUNC'=>'выносить повторяемую логику в функции с понятным контрактом',
        'SCOPE'=>'контролировать область жизни переменных и изменение значений',
        'REC'=>'решать задачу через базовый случай и уменьшенную копию задачи',
        'REGEX'=>'проверять и преобразовывать строки по формальному шаблону',
        'HTML'=>'формировать HTML с безопасной вставкой динамических данных',
        'FORM'=>'получать, проверять и безопасно выводить пользовательский ввод',
        'STATE'=>'сохранять состояние в сессии, cookie или файле',
        'DB'=>'читать и изменять данные MySQL предсказуемыми запросами',
        'WEB'=>'обрабатывать HTTP-запросы, авторизацию и права доступа',
    ][$sectionCode] ?? 'решать отдельный шаг программы понятным способом';
}

function course_card_mistake(string $sectionCode): string
{
    return [
        'BAS'=>'Проверьте типы, приоритет операций и точный формат вывода.',
        'ARR'=>'Проверьте существование ключа, тип значения и не изменился ли исходный массив.',
        'COND'=>'Проверьте строгость сравнения и все ветки, включая пустое или отсутствующее значение.',
        'LOOP'=>'Проверьте условие завершения, изменение счётчика и начальное значение аккумулятора.',
        'MULTI'=>'Проверьте уровень вложенности и существование каждого промежуточного ключа.',
        'MATH'=>'Проверьте порядок операций, округление и граничные значения.',
        'STR'=>'Проверьте кодировку, позицию с нуля и отличие false от 0.',
        'AFN'=>'Проверьте, изменяет ли функция массив на месте и сохраняются ли ключи.',
        'TIME'=>'Проверьте часовой пояс, формат даты и изменяемость объекта.',
        'FUNC'=>'Проверьте типы параметров, return и поведение на границе.',
        'SCOPE'=>'Проверьте, какая именно переменная изменяется и не осталась ли ссылка после foreach.',
        'REC'=>'Проверьте базовый случай и то, что каждый вызов приближает его.',
        'REGEX'=>'Проверьте якоря, экранирование разделителя и модификатор Unicode.',
        'HTML'=>'Проверьте экранирование динамических данных и корректное закрытие тегов.',
        'FORM'=>'Проверьте отсутствие поля, пустую строку, валидацию и экранирование вывода.',
        'STATE'=>'Проверьте запуск сессии до вывода, абсолютный путь и результат файловой операции.',
        'DB'=>'Проверьте имена полей, параметры запроса и обязательный WHERE для UPDATE/DELETE.',
        'WEB'=>'Проверьте метод запроса, отсутствие вывода до header и безопасную работу с паролем.',
    ][$sectionCode] ?? 'Пройдите решение построчно и сравните фактический результат с ожидаемым.';
}

function seed_course_cards(PDO $pdo): void
{
    $rows = $pdo->query("SELECT l.id,l.title,l.concept_summary,s.code section_code,s.title section_title
        FROM course_lessons l JOIN course_sections s ON s.id=l.section_id
        ORDER BY s.sort_order,l.sort_order")->fetchAll();

    $upsert = $pdo->prepare("INSERT INTO course_cards (lesson_id,card_type,question,answer)
        VALUES (?,?,?,?)
        ON DUPLICATE KEY UPDATE question=VALUES(question),answer=VALUES(answer)");

    foreach ($rows as $row) {
        $lessonId = (int)$row['id'];
        $concept = (string)$row['title'];
        $sectionCode = (string)$row['section_code'];
        $tokens = prime_tokens_for($sectionCode,$concept);
        $syntax = $tokens === [] ? 'ориентируйтесь на название темы' : implode(', ',$tokens);
        $purpose = course_card_purpose($sectionCode);
        $mistake = course_card_mistake($sectionCode);

        $cards = [
            [
                'meaning',
                "Что означает «{$concept}» и зачем это нужно?",
                "{$concept} — приём раздела «{$row['section_title']}». Он помогает {$purpose}. Перед применением назовите входные данные, ожидаемый результат и граничный случай.",
            ],
            [
                'syntax',
                "Какие синтаксические опоры нужны для темы «{$concept}»?",
                "Опорные элементы: {$syntax}. Порядок работы: объявите точные входные данные, примените конструкцию к ним, сохраните или выведите вычисленный результат.",
            ],
            [
                'check',
                "Как самостоятельно проверить решение по теме «{$concept}»?",
                "{$mistake} Затем назовите значение каждой переменной, вручную пройдите обычный и граничный сценарии и убедитесь, что результат не подставлен готовой строкой.",
            ],
        ];

        foreach ($cards as [$type,$question,$answer]) {
            $upsert->execute([$lessonId,$type,$question,$answer]);
        }
    }
}

function activate_course_cards_for_lesson(int $userId, int $lessonId): void
{
    db()->prepare("INSERT IGNORE INTO user_course_cards (user_id,card_id,due_at)
        SELECT ?,id,NOW() FROM course_cards WHERE lesson_id=?")
        ->execute([$userId,$lessonId]);
}

function activate_course_cards_for_exercise(int $userId, int $exerciseId): void
{
    $stmt = db()->prepare('SELECT lesson_id FROM course_exercises WHERE id=?');
    $stmt->execute([$exerciseId]);
    $lessonId = (int)$stmt->fetchColumn();
    if ($lessonId > 0) {
        activate_course_cards_for_lesson($userId,$lessonId);
    }
}

function activate_first_course_cards(int $userId): void
{
    $lessonId = (int)db()->query("SELECT l.id FROM course_lessons l
        JOIN course_sections s ON s.id=l.section_id
        ORDER BY s.sort_order,l.sort_order LIMIT 1")->fetchColumn();
    if ($lessonId > 0) {
        activate_course_cards_for_lesson($userId,$lessonId);
    }
}
