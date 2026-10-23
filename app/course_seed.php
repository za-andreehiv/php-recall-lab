<?php
declare(strict_types=1);

/**
 * Внутренняя тематическая карта PHP + MySQL.
 * Теория, сценарии и задания созданы для PHP Recall Lab.
 */
function prime_course_blueprint(): array
{
    return [
        ['BAS','Основы PHP',55,'php',[
            ['вывод и echo',['echo']],['переменные',['$']],['числа и арифметика',['+']],['приоритет операций',['(',')']],
            ['строки и конкатенация',['.']],['типы данных',['string']],['null',['null']],['преобразование типов',['int']],
            ['символы строки',['[',']']],['присваивание',['=']],['инкремент',['++']]
        ]],
        ['ARR','Массивы',30,'php',[
            ['индексные массивы',['[',']']],['ассоциативные массивы',['=>']],['ключи массива',['array_keys']],
            ['изменение элементов',['[',']','=']],['длина массива',['count']],['добавление элементов',['[]','=']]
        ]],
        ['COND','Условия',35,'php',[
            ['if и else',['if','else']],['строгое сравнение',['===']],['логическое И',['&&']],['логическое ИЛИ',['||']],
            ['isset',['isset']],['empty',['empty']],['elseif',['elseif']],['match',['match']],['тернарный оператор',['?',':']],
            ['объединение с null',['??']]
        ]],
        ['LOOP','Циклы',35,'php',[
            ['foreach',['foreach']],['foreach с ключами',['foreach','=>']],['while',['while']],['for',['for']],
            ['накопление суммы',['+=']],['формирование строки',['.=']],['break',['break']],['continue',['continue']],
            ['флаг в цикле',['bool']],['вложенные циклы',['for','for']]
        ]],
        ['MULTI','Многомерные массивы',20,'php',[
            ['двумерный массив',['[','][']],['вложенный foreach',['foreach','foreach']],['ассоциативные записи',['=>']],
            ['заполнение таблицы',['[]']],['преобразование структуры',['foreach']]
        ]],
        ['MATH','Математические функции',15,'php',[
            ['степень',['pow']],['округление',['round']],['минимум',['min']],['максимум',['max']],['модуль числа',['abs']]
        ]],
        ['STR','Строковые функции',25,'php',[
            ['длина строки',['mb_strlen']],['регистр',['mb_strtolower']],['вырезание строки',['mb_substr']],
            ['поиск подстроки',['mb_strpos']],['разбиение строки',['explode']],['соединение массива',['implode']],
            ['очистка краёв',['trim']],['формат числа',['number_format']],['замена подстроки',['str_replace']],
            ['экранирование HTML',['htmlspecialchars']]
        ]],
        ['AFN','Функции массивов',25,'php',[
            ['поиск значения',['in_array']],['поиск ключа',['array_search']],['слайсинг',['array_slice']],
            ['слияние',['array_merge']],['ключи и значения',['array_keys','array_values']],['удаление дублей',['array_unique']],
            ['подсчёт значений',['array_count_values']],['сортировка',['sort']],['фильтрация',['array_filter']],
            ['преобразование',['array_map']]
        ]],
        ['TIME','Рандом и время',20,'php',[
            ['случайное число',['random_int']],['случайный элемент',['array_rand']],['перемешивание',['shuffle']],
            ['timestamp',['time']],['форматирование даты',['date']],['разбор даты',['strtotime']],['DateTimeImmutable',['DateTimeImmutable']]
        ]],
        ['FUNC','Пользовательские функции',35,'php',[
            ['объявление функции',['function']],['параметры',['function','$']],['тип параметра',['function','int']],
            ['возвращаемый тип',['):']],['return',['return']],['необязательный параметр',['=']],['ранний return',['return']],
            ['логическая функция',['bool']],['чистая функция',['function']],['декомпозиция',['function']]
        ]],
        ['SCOPE','Область видимости и ссылки',20,'php',[
            ['локальная переменная',['function','$']],['global',['global']],['static',['static']],['параметр по ссылке',['&$']],
            ['ссылка в foreach',['foreach','&$']]
        ]],
        ['REC','Рекурсия',12,'php',[
            ['базовый случай',['function','return']],['рекурсивный вызов',['function','return']],['обход дерева',['is_array']],
            ['сумма вложенного массива',['is_array','return']]
        ]],
        ['REGEX','Регулярные выражения',30,'php',[
            ['проверка строки',['preg_match']],['повторение символов',['+']],['группы',['(',')']],['набор символов',['[',']']],
            ['начало и конец строки',['^','$']],['поиск всех совпадений',['preg_match_all']],['замена',['preg_replace']],
            ['разбиение',['preg_split']],['Unicode',['u']],['экранирование',['preg_quote']]
        ]],
        ['HTML','PHP и формирование HTML',20,'php',[
            ['вставка переменной',['<?=']],['генерация тега',['<','>']],['атрибуты',['class=']],['HTML-таблица',['<table']],
            ['условие в шаблоне',['if',':','endif']],['цикл в шаблоне',['foreach',':','endforeach']]
        ]],
        ['FORM','Формы и ввод',20,'php',[
            ['метод POST',['$_POST']],['метод GET',['$_GET']],['валидация строки',['trim']],['сохранение значения',['htmlspecialchars']],
            ['textarea',['textarea']],['checkbox',['isset']],['radio',['value']],['select',['option']]
        ]],
        ['STATE','Сессии, cookies и файлы',20,'php',[
            ['запуск сессии',['session_start']],['данные сессии',['$_SESSION']],['завершение сессии',['session_destroy']],
            ['установка cookie',['setcookie']],['чтение файла',['file_get_contents']],['запись файла',['file_put_contents']],
            ['проверка файла',['is_file']],['абсолютный путь',['__DIR__']],['подключение файла',['require_once']]
        ]],
        ['DB','MySQL и PDO',20,'sql',[
            ['SELECT',['SELECT','FROM']],['WHERE',['SELECT','WHERE']],['INSERT',['INSERT INTO','VALUES']],['UPDATE',['UPDATE','SET','WHERE']],
            ['DELETE',['DELETE FROM','WHERE']],['ORDER BY',['ORDER BY']],['LIMIT',['LIMIT']],['COUNT',['COUNT']],
            ['CREATE TABLE',['CREATE TABLE']],['PRIMARY KEY',['PRIMARY KEY']],['FOREIGN KEY',['FOREIGN KEY','REFERENCES']],
            ['JOIN',['JOIN','ON']],['GROUP BY',['GROUP BY']],['подготовленный PDO-запрос',['prepare','execute']]
        ]],
        ['WEB','HTTP, редиректы и авторизация',10,'project',[
            ['HTTP-метод',['REQUEST_METHOD']],['заголовок ответа',['header']],['редирект',['Location:']],
            ['флеш-сообщение',['$_SESSION']],['регистрация',['password_hash']],['проверка пароля',['password_verify']],
            ['выход',['session_destroy']],['права доступа',['role']]
        ]],
    ];
}

function prime_course_total(): int
{
    return array_sum(array_column(prime_course_blueprint(), 2));
}

function prime_internal_theory(string $concept, array $tokens, string $type): string
{
    $syntax = implode(', ', $tokens);
    if ($type === 'sql') {
        return "Что изучаем: {$concept}.\n\n".
            "Назначение: конструкция применяется для чтения или изменения данных в реляционной базе. ".
            "Перед запросом определите таблицу, нужные поля и условие отбора.\n\n".
            "Синтаксический ориентир: {$syntax}. Это не готовое решение — составьте запрос под условие задачи.\n\n".
            "Типичные ошибки: пропущенное условие WHERE в изменяющем запросе, неверное имя поля, смешивание строк и чисел, выполнение опасной команды не в той базе.\n\n".
            "Самопроверка: назовите ожидаемые строки, проверьте условие отбора и объясните, какие данные запрос не должен затронуть.";
    }

    if ($type === 'project') {
        return "Что изучаем: {$concept}.\n\n".
            "Назначение: этот приём связывает PHP-код с HTTP-запросом, сессией или механизмом авторизации. ".
            "Сначала определите входные данные и допустимый результат, затем предусмотрите отказ и повторный запрос.\n\n".
            "Синтаксический ориентир: {$syntax}. Используйте его в собственном фрагменте, а не как отдельное слово.\n\n".
            "Типичные ошибки: вывод до отправки заголовков, доверие пользовательскому вводу, хранение открытого пароля, отсутствие проверки прав.\n\n".
            "Самопроверка: пройдите успешный, ошибочный и граничный сценарии и убедитесь, что секреты не попадают в HTML.";
    }

    return "Что изучаем: {$concept}.\n\n".
        "Назначение: конструкция помогает выразить отдельный шаг алгоритма и сделать поведение программы явным. ".
        "До написания кода определите входные значения, ожидаемый результат и хотя бы один граничный случай.\n\n".
        "Синтаксический ориентир: {$syntax}. Это опорные элементы, а не готовый ответ.\n\n".
        "Типичные ошибки: неверный тип данных, неправильный порядок операций, пропущенная ветка, изменение исходных данных без необходимости.\n\n".
        "Самопроверка: прочитайте код построчно, назовите значение каждой переменной и вручную проверьте обычный, пустой и граничный ввод.";
}

function seed_prime_course(PDO $pdo): void
{
    $source = null;
    $sectionInsert = $pdo->prepare('INSERT INTO course_sections
        (code,title,description,sort_order,source_url) VALUES (?,?,?,?,?)');
    $lessonInsert = $pdo->prepare('INSERT INTO course_lessons
        (section_id,slug,title,concept_summary,sort_order,source_url) VALUES (?,?,?,?,?,?)');
    $exerciseInsert = $pdo->prepare('INSERT INTO course_exercises
        (lesson_id,code,exercise_type,title,prompt,starter_code,checker_config,difficulty,sort_order,source_url)
        VALUES (?,?,?,?,?,?,?,?,?,?)');

    $pdo->exec('UPDATE course_sections SET source_url=NULL');
    $pdo->exec('UPDATE course_lessons SET source_url=NULL');
    $pdo->exec('UPDATE course_exercises SET source_url=NULL');

    $scenarios = ['список покупок','каталог курсов','учёт учеников','заказы магазина','журнал попыток','расписание занятий','личный бюджет'];
    $modes = ['Создайте решение','Найдите и исправьте ошибку','Объясните выбор конструкции','Решите с граничными случаями','Перепишите решение понятнее'];

    foreach (prime_course_blueprint() as $sectionIndex => [$code,$title,$count,$baseType,$concepts]) {
        $find = $pdo->prepare('SELECT id FROM course_sections WHERE code=?');
        $find->execute([$code]);
        $sectionId = (int)($find->fetchColumn() ?: 0);
        if (!$sectionId) {
            $sectionInsert->execute([$code,$title,'Самостоятельный модуль: сначала попытка, затем проверка и повторение ошибок.',$sectionIndex+1,$source]);
            $sectionId = (int)$pdo->lastInsertId();
        }

        $lessonIds = [];
        foreach ($concepts as $conceptIndex => [$concept,$tokens]) {
            $slug = strtolower($code) . '-' . ($conceptIndex + 1);
            $find = $pdo->prepare('SELECT id FROM course_lessons WHERE slug=?');
            $find->execute([$slug]);
            $lessonId = (int)($find->fetchColumn() ?: 0);
            if (!$lessonId) {
                $lessonInsert->execute([
                    $sectionId,$slug,$concept,
                    prime_internal_theory($concept,$tokens,$baseType),
                    $conceptIndex+1,$source
                ]);
                $lessonId = (int)$pdo->lastInsertId();
            } else {
                $pdo->prepare('UPDATE course_lessons SET concept_summary=?,source_url=NULL WHERE id=?')
                    ->execute([prime_internal_theory($concept,$tokens,$baseType),$lessonId]);
            }
            $lessonIds[] = $lessonId;
        }

        $existing = $pdo->prepare('SELECT COUNT(*) FROM course_exercises e JOIN course_lessons l ON l.id=e.lesson_id WHERE l.section_id=?');
        $existing->execute([$sectionId]);
        if ((int)$existing->fetchColumn() >= $count) {
            continue;
        }

        for ($i=1; $i<=$count; $i++) {
            $conceptIndex = ($i - 1) % count($concepts);
            [$concept,$tokens] = $concepts[$conceptIndex];
            $variant = intdiv($i - 1, count($concepts)) + 1;
            $scenario = $scenarios[($i + $sectionIndex) % count($scenarios)];
            $mode = $modes[($i - 1) % count($modes)];
            $exerciseCode = sprintf('%s.%03d',$code,$i);
            $exerciseType = $baseType;
            if ($mode === 'Объясните выбор конструкции' && $baseType !== 'sql') {
                $exerciseType = 'theory';
            }

            $prompt = $mode.' по теме «'.$concept.'» для сценария «'.$scenario.'». '.
                'Вариант '.$variant.'. Не копируйте пример: назовите входные данные, покажите результат и кратко объясните решение.';
            $starter = $exerciseType === 'sql'
                ? "-- Самостоятельный SQL-запрос\n"
                : ($exerciseType === 'theory' ? '' : "<?php\n\n// Самостоятельное решение\n");

            $checks = [];
            foreach ($tokens as $token) {
                $checks[] = ['needle'=>$token,'label'=>'используйте или объясните «'.$token.'»'];
            }
            $config = [
                'checks'=>$checks,
                'forbidden'=>$exerciseType === 'sql'
                    ? ['DROP DATABASE','TRUNCATE','INTO OUTFILE']
                    : ['eval(','shell_exec(','exec(','system(','passthru('],
                'min_length'=>$exerciseType === 'theory' ? 80 : 35,
                'syntax'=>$exerciseType === 'php' || $exerciseType === 'project',
            ];
            $difficulty = min(5, 1 + intdiv($i - 1, max(1, (int)ceil($count / 5))));
            $exerciseInsert->execute([
                $lessonIds[$conceptIndex],$exerciseCode,$exerciseType,$concept.' — вариант '.$variant,
                $prompt,$starter,json_encode($config,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                $difficulty,$i,$source
            ]);
        }
    }
}
