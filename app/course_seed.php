<?php
declare(strict_types=1);

/**
 * Тематическая карта вдохновлена оглавлением публичного учебника code.mu.
 * Формулировки, сценарии и проверочные задания в этом файле созданы заново.
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

function seed_prime_course(PDO $pdo): void
{
    $source = 'https://code.mu/ru/php/book/prime/';
    $sectionInsert = $pdo->prepare('INSERT INTO course_sections
        (code,title,description,sort_order,source_url) VALUES (?,?,?,?,?)');
    $lessonInsert = $pdo->prepare('INSERT INTO course_lessons
        (section_id,slug,title,concept_summary,sort_order,source_url) VALUES (?,?,?,?,?,?)');
    $exerciseInsert = $pdo->prepare('INSERT INTO course_exercises
        (lesson_id,code,exercise_type,title,prompt,starter_code,checker_config,difficulty,sort_order,source_url)
        VALUES (?,?,?,?,?,?,?,?,?,?)');

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
                    'Разберите назначение конструкции «'.$concept.'», напишите собственный пример и проверьте граничные случаи.',
                    $conceptIndex+1,$source
                ]);
                $lessonId = (int)$pdo->lastInsertId();
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
