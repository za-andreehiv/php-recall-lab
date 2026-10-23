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


function prime_tokens_for(string $sectionCode, string $concept): array
{
    foreach (prime_course_blueprint() as [$code,,$count,$type,$concepts]) {
        if ($code !== $sectionCode) {
            continue;
        }
        foreach ($concepts as [$name,$tokens]) {
            if ($name === $concept) {
                return $tokens;
            }
        }
    }
    return [];
}

function prime_task_spec(
    string $sectionCode,
    string $sectionTitle,
    string $concept,
    string $exerciseType,
    string $exerciseCode,
    int $sortOrder,
    int $difficulty
): array {
    $variant = (($sortOrder - 1) % 5) + 1;
    $goal = 'Закрепить тему «'.$concept.'» на небольшом, полностью определённом примере.';
    $input = '';
    $steps = '';
    $expected = '';
    $constraints = "Не меняйте исходные данные.\nНе подставляйте ожидаемый результат готовой строкой вместо вычисления.\nПосле кода кратко объясните решение комментарием.";
    $selfCheck = "1. Сверьте имена и значения исходных переменных.\n2. Пройдите код построчно вручную.\n3. Сравните результат с блоком «Ожидаемый результат».\n4. Проверьте пустой или граничный случай, если он указан.";
    $hint1 = 'Сначала выпишите входные данные и промежуточный результат на бумаге, затем выберите конструкцию из теории.';
    $hint2 = 'Разбейте решение на три части: получение данных, вычисление, вывод результата.';
    $starter = "<?php\n\n// Исходные данные\n\n// Ваше решение\n";
    $solutionChecks = [];

    switch ($sectionCode) {
        case 'BAS':
            $course = 'PHP Start '.$variant;
            $lessons = 18 + $variant;
            $price = 3900 + $variant * 100;
            $input = "\$courseName = ".var_export($course,true).";\n".
                "\$teacherName = 'Антон';\n".
                "\$lessonsCount = {$lessons};\n".
                "\$coursePrice = {$price};";
            $steps = "1. Объявите все четыре переменные с указанными значениями.\n".
                "2. Примените конструкцию темы «{$concept}» в вычислении или выводе.\n".
                "3. Выведите одну строку карточки курса строго в указанном формате.\n".
                "4. Добавьте перевод строки после результата.";
            $expected = "Курс: {$course}; преподаватель: Антон; уроков: {$lessons}; цена: {$price} руб.";
            $starter = "<?php\n\n\$courseName = ".var_export($course,true).";\n\$teacherName = 'Антон';\n\$lessonsCount = {$lessons};\n\$coursePrice = {$price};\n\n// Сформируйте и выведите карточку курса\n";
            $solutionChecks = [['needle'=>'echo','label'=>'вывод результата через echo']];
            break;

        case 'ARR':
            $input = "\$courses = [\n".
                "    ['name' => 'PHP Start', 'price' => ".(3900 + $variant * 100)."],\n".
                "    ['name' => 'MySQL Base', 'price' => ".(4500 + $variant * 100)."],\n".
                "    ['name' => 'Web Practice', 'price' => ".(5200 + $variant * 100)."],\n".
                "];";
            $steps = "1. Создайте массив courses точно по исходным данным.\n".
                "2. Используйте приём «{$concept}».\n".
                "3. Получите количество курсов и название второго курса.\n".
                "4. Выведите: Курсов: 3; второй: MySQL Base.";
            $expected = 'Курсов: 3; второй: MySQL Base';
            $starter = "<?php\n\n\$courses = [\n    ['name' => 'PHP Start', 'price' => ".(3900 + $variant * 100)."],\n    ['name' => 'MySQL Base', 'price' => ".(4500 + $variant * 100)."],\n    ['name' => 'Web Practice', 'price' => ".(5200 + $variant * 100)."],\n];\n\n// Получите данные из массива и выведите результат\n";
            $solutionChecks = [['needle'=>'courses','label'=>'использование массива courses'],['needle'=>'echo','label'=>'вывод результата']];
            break;

        case 'COND':
            $price = 4000 + $variant * 250;
            $budget = $variant % 2 === 0 ? $price - 100 : $price + 500;
            $available = $variant !== 5;
            $allowed = $budget >= $price && $available;
            $input = "\$coursePrice = {$price};\n\$studentBudget = {$budget};\n\$hasPlaces = ".($available ? 'true' : 'false').";";
            $steps = "1. Сравните бюджет со стоимостью курса.\n".
                "2. Проверьте наличие мест, используя тему «{$concept}».\n".
                "3. Если оба условия выполнены, выведите «Можно записаться».\n".
                "4. Иначе выведите «Запись недоступна».";
            $expected = $allowed ? 'Можно записаться' : 'Запись недоступна';
            $starter = "<?php\n\n\$coursePrice = {$price};\n\$studentBudget = {$budget};\n\$hasPlaces = ".($available ? 'true' : 'false').";\n\n// Проверьте условия и выведите итог\n";
            $solutionChecks = [['needle'=>'echo','label'=>'вывод одной из двух фраз']];
            break;

        case 'LOOP':
            $a = 900 + $variant * 100;
            $b = 1200 + $variant * 100;
            $c = 1500 + $variant * 100;
            $total = $a + $b + $c;
            $input = "\$lessonPrices = [{$a}, {$b}, {$c}];";
            $steps = "1. Переберите массив цен с помощью конструкции по теме «{$concept}».\n".
                "2. Рассчитайте сумму без готового вызова array_sum.\n".
                "3. Сохраните сумму в переменную total.\n".
                "4. Выведите: Итого: {$total} руб.";
            $expected = "Итого: {$total} руб.";
            $starter = "<?php\n\n\$lessonPrices = [{$a}, {$b}, {$c}];\n\$total = 0;\n\n// Переберите цены, накопите сумму и выведите её\n";
            $constraints .= "\nВ этом задании не используйте array_sum().";
            $solutionChecks = [['needle'=>'total','label'=>'переменная total'],['needle'=>'echo','label'=>'вывод итога']];
            break;

        case 'MULTI':
            $input = "\$groups = [\n".
                "    'A' => [['name' => 'Ирина', 'score' => 8], ['name' => 'Олег', 'score' => 6]],\n".
                "    'B' => [['name' => 'Анна', 'score' => 9], ['name' => 'Павел', 'score' => ".(5 + $variant)."]],\n".
                "];";
            $target = 8 + ($variant === 5 ? 1 : 0);
            $steps = "1. Создайте вложенный массив groups.\n".
                "2. Обойдите все группы и записи, применив тему «{$concept}».\n".
                "3. Соберите имена учеников с результатом не ниже {$target}.\n".
                "4. Выведите имена через запятую в порядке исходного массива.";
            $pavelScore = 5 + $variant;
            $selectedNames = [];
            if (8 >= $target) { $selectedNames[] = 'Ирина'; }
            if (9 >= $target) { $selectedNames[] = 'Анна'; }
            if ($pavelScore >= $target) { $selectedNames[] = 'Павел'; }
            $expected = implode(', ', $selectedNames);
            $starter = "<?php\n\n\$groups = [\n    'A' => [['name' => 'Ирина', 'score' => 8], ['name' => 'Олег', 'score' => 6]],\n    'B' => [['name' => 'Анна', 'score' => 9], ['name' => 'Павел', 'score' => ".(5 + $variant)."]],\n];\n\n\$selected = [];\n// Найдите учеников с баллом не ниже {$target}\n";
            $solutionChecks = [['needle'=>'selected','label'=>'массив selected'],['needle'=>'foreach','label'=>'обход вложенных данных']];
            break;

        case 'MATH':
            $number = 12.345 + $variant;
            $input = "\$value = {$number};\n\$precision = 2;";
            $rounded = round($number,2);
            $steps = "1. Возьмите число value и точность precision.\n".
                "2. Используйте функцию по теме «{$concept}» как часть вычисления.\n".
                "3. Округлите итог до двух знаков после запятой.\n".
                "4. Выведите число без дополнительного текста.";
            $expected = number_format($rounded,2,'.','');
            $starter = "<?php\n\n\$value = {$number};\n\$precision = 2;\n\n// Выполните вычисление и выведите число\n";
            $solutionChecks = [['needle'=>'echo','label'=>'вывод вычисленного числа']];
            break;

        case 'STR':
            $input = "\$title = '  PHP и MySQL: практика {$variant}  ';";
            $clean = "PHP и MySQL: практика {$variant}";
            $steps = "1. Используйте строку title без изменения исходного значения.\n".
                "2. Удалите пробелы по краям.\n".
                "3. Примените функцию темы «{$concept}» к очищенной строке.\n".
                "4. Выведите очищенную строку и её длину в формате: {$clean} | длина: ".23.".";
            $expected = $clean.' | длина: '.23;
            $starter = "<?php\n\n\$title = '  PHP и MySQL: практика {$variant}  ';\n\n// Очистите строку, обработайте её и выведите результат\n";
            $solutionChecks = [['needle'=>'title','label'=>'обработка переменной title'],['needle'=>'echo','label'=>'вывод результата']];
            break;

        case 'AFN':
            $input = "\$scores = [7, ".(8 + $variant % 2).", 5, 9, 7, 10];";
            $steps = "1. Работайте с массивом scores.\n".
                "2. Примените функцию темы «{$concept}».\n".
                "3. Оставьте уникальные значения не ниже 7.\n".
                "4. Отсортируйте их по возрастанию и выведите через запятую.";
            $expected = $variant % 2 === 0 ? '7, 8, 9, 10' : '7, 9, 10';
            $starter = "<?php\n\n\$scores = [7, ".(8 + $variant % 2).", 5, 9, 7, 10];\n\n// Получите уникальные значения >= 7, отсортируйте и выведите\n";
            $solutionChecks = [['needle'=>'scores','label'=>'использование массива scores'],['needle'=>'echo','label'=>'вывод списка']];
            break;

        case 'TIME':
            $day = 10 + $variant;
            $input = "\$start = new DateTimeImmutable('2026-10-{$day}');\n\$daysToAdd = 14;";
            $end = (new DateTimeImmutable("2026-10-{$day}"))->modify('+14 days')->format('d.m.Y');
            $steps = "1. Создайте дату start из точной строки.\n".
                "2. Примените приём «{$concept}» и прибавьте 14 дней, не изменяя исходный объект.\n".
                "3. Отформатируйте новую дату как d.m.Y.\n".
                "4. Выведите только дату.";
            $expected = $end;
            $starter = "<?php\n\n\$start = new DateTimeImmutable('2026-10-{$day}');\n\$daysToAdd = 14;\n\n// Получите и выведите новую дату в формате d.m.Y\n";
            $solutionChecks = [['needle'=>'start','label'=>'исходная дата start'],['needle'=>'echo','label'=>'вывод даты']];
            break;

        case 'FUNC':
            $price = 3000 + $variant * 200;
            $discount = 10 + $variant;
            $final = (int)round($price * (100 - $discount) / 100);
            $input = "\$price = {$price};\n\$discountPercent = {$discount};";
            $steps = "1. Объявите функцию calculateCoursePrice.\n".
                "2. Передайте в неё цену и процент скидки.\n".
                "3. Используйте приём темы «{$concept}» и верните цену после скидки.\n".
                "4. Вызовите функцию с исходными данными и выведите результат.";
            $expected = (string)$final;
            $starter = "<?php\n\n\$price = {$price};\n\$discountPercent = {$discount};\n\n// Объявите calculateCoursePrice(), вызовите её и выведите результат\n";
            $solutionChecks = [['needle'=>'calculateCoursePrice','label'=>'функция calculateCoursePrice'],['needle'=>'return','label'=>'возврат результата']];
            break;

        case 'SCOPE':
            $input = "\$attempts = {$variant};";
            $steps = "1. Создайте переменную attempts с указанным значением.\n".
                "2. Объявите функцию registerAttempt.\n".
                "3. С помощью приёма «{$concept}» увеличьте счётчик ровно на 1.\n".
                "4. Вызовите функцию один раз и выведите счётчик.";
            $expected = (string)($variant + 1);
            $starter = "<?php\n\n\$attempts = {$variant};\n\n// Объявите registerAttempt(), увеличьте счётчик и выведите итог\n";
            $solutionChecks = [['needle'=>'registerAttempt','label'=>'функция registerAttempt'],['needle'=>'attempts','label'=>'счётчик attempts']];
            break;

        case 'REC':
            $last = 4 + $variant;
            $sum = 1 + 2 + 3 + $last;
            $input = "\$tree = [1, [2, [3]], {$last}];";
            $steps = "1. Объявите функцию sumNested.\n".
                "2. Добавьте базовый случай для одного числа.\n".
                "3. Рекурсивно обойдите вложенные массивы.\n".
                "4. Выведите сумму всех чисел.";
            $expected = (string)$sum;
            $starter = "<?php\n\n\$tree = [1, [2, [3]], {$last}];\n\n// Объявите sumNested() и выведите сумму\n";
            $solutionChecks = [['needle'=>'sumNested','label'=>'рекурсивная функция sumNested'],['needle'=>'return','label'=>'базовый или рекурсивный return']];
            break;

        case 'REGEX':
            $input = "\$courseCode = 'PHP-".(100 + $variant)."';\n\$patternRequirement = 'три заглавные латинские буквы, дефис и три цифры';";
            $steps = "1. Составьте регулярное выражение по patternRequirement.\n".
                "2. Используйте приём «{$concept}».\n".
                "3. Проверьте всю строку courseCode, а не её фрагмент.\n".
                "4. Выведите «Код корректен» или «Ошибка кода».";
            $expected = 'Код корректен';
            $starter = "<?php\n\n\$courseCode = 'PHP-".(100 + $variant)."';\n\n// Составьте шаблон, проверьте код и выведите итог\n";
            $solutionChecks = [['needle'=>'courseCode','label'=>'проверка courseCode'],['needle'=>'echo','label'=>'вывод результата']];
            break;

        case 'HTML':
            $input = "\$course = ['title' => 'PHP & MySQL {$variant}', 'price' => ".(4000 + $variant * 100)."];";
            $steps = "1. Используйте массив course.\n".
                "2. Сформируйте HTML-карточку с h2 и p.\n".
                "3. Примените приём темы «{$concept}».\n".
                "4. Экранируйте динамический заголовок через htmlspecialchars.";
            $expected = "<article class=\"course\"><h2>PHP &amp; MySQL {$variant}</h2><p>Цена: ".(4000 + $variant * 100)." руб.</p></article>";
            $starter = "<?php\n\n\$course = ['title' => 'PHP & MySQL {$variant}', 'price' => ".(4000 + $variant * 100)."];\n\n// Сформируйте безопасную HTML-карточку\n";
            $solutionChecks = [['needle'=>'htmlspecialchars','label'=>'экранирование заголовка'],['needle'=>'course','label'=>'данные course']];
            break;

        case 'FORM':
            $email = "student{$variant}@example.test";
            $input = "\$_POST = ['name' => '  Анна  ', 'email' => '{$email}'];";
            $steps = "1. Получите name и email из POST.\n".
                "2. Примените приём темы «{$concept}».\n".
                "3. Удалите пробелы по краям имени и проверьте непустое значение.\n".
                "4. Выведите безопасную строку: Анна | {$email}.";
            $expected = "Анна | {$email}";
            $starter = "<?php\n\n\$_POST = ['name' => '  Анна  ', 'email' => '{$email}'];\n\n// Нормализуйте и безопасно выведите данные формы\n";
            $solutionChecks = [['needle'=>'$_POST','label'=>'чтение данных POST'],['needle'=>'htmlspecialchars','label'=>'безопасный вывод']];
            break;

        case 'STATE':
            $input = "\$userId = ".(100 + $variant).";\n\$progress = ['passed' => ".(10 + $variant).", 'total' => 20];";
            $steps = "1. Используйте userId и progress.\n".
                "2. Примените приём темы «{$concept}» для сохранения или чтения состояния.\n".
                "3. Не используйте относительный путь для файла.\n".
                "4. Выведите userId и значение passed в формате из ожидаемого результата.";
            $expected = 'Пользователь '.(100 + $variant).'; пройдено: '.(10 + $variant).'/20';
            $starter = "<?php\n\n\$userId = ".(100 + $variant).";\n\$progress = ['passed' => ".(10 + $variant).", 'total' => 20];\n\n// Сохраните или прочитайте состояние и выведите сводку\n";
            $solutionChecks = [['needle'=>'progress','label'=>'данные progress'],['needle'=>'echo','label'=>'вывод сводки']];
            break;

        case 'DB':
            $input = "Таблица courses:\n".
                "id | title       | price | active | category_id\n".
                "1  | PHP Start   | 4200  | 1      | 1\n".
                "2  | MySQL Base  | 5100  | 1      | 1\n".
                "3  | HTML Start  | 3500  | 0      | 2\n".
                "4  | PHP Pro     | 6900  | 1      | 1\n\n".
                "Таблица categories:\nid | name\n1  | Backend\n2  | Frontend";
            $steps = "1. Напишите один SQL-запрос с использованием темы «{$concept}».\n".
                "2. Работайте только с таблицами и полями из исходных данных.\n".
                "3. Для выборки активных курсов используйте active = 1.\n".
                "4. Верните title и price, отсортировав price по возрастанию; если конструкция темы меняет данные, ограничьте действие точным WHERE.";
            $expected = "Для выборки:\nPHP Start | 4200\nMySQL Base | 5100\nPHP Pro | 6900\n\n".
                "Для изменяющего запроса: одна точная команда с безопасным WHERE и без затрагивания остальных строк.";
            $starter = "-- Таблицы courses и categories описаны в исходных данных\n-- Напишите один самостоятельный запрос\n";
            $constraints = "Не выполняйте запрос на боевой базе.\nНе используйте DROP, TRUNCATE и INTO OUTFILE.\nВ UPDATE и DELETE обязателен точный WHERE.\nИспользуйте только перечисленные таблицы и поля.";
            $solutionChecks = [];
            break;

        case 'WEB':
            $input = "\$_SERVER['REQUEST_METHOD'] = 'POST';\n".
                "\$_POST = ['email' => 'student{$variant}@example.test', 'password' => 'secret-{$variant}'];\n".
                "\$storedHash = password_hash('secret-{$variant}', PASSWORD_DEFAULT);";
            $steps = "1. Обработайте только POST-запрос.\n".
                "2. Примените приём темы «{$concept}».\n".
                "3. Проверьте пароль безопасной функцией; открытый пароль не сохраняйте и не выводите.\n".
                "4. Для успеха подготовьте редирект в cabinet.php, для ошибки — текст «Неверные данные».";
            $expected = "Успешный сценарий: переход в cabinet.php.\nОшибочный сценарий: Неверные данные.\nПароль и хеш не попадают в HTML.";
            $starter = "<?php\n\n\$_SERVER['REQUEST_METHOD'] = 'POST';\n\$_POST = ['email' => 'student{$variant}@example.test', 'password' => 'secret-{$variant}'];\n\$storedHash = password_hash('secret-{$variant}', PASSWORD_DEFAULT);\n\n// Обработайте запрос и предусмотрите оба сценария\n";
            $solutionChecks = [['needle'=>'REQUEST_METHOD','label'=>'проверка HTTP-метода'],['needle'=>'password','label'=>'безопасная работа с паролем']];
            break;
    }

    $tokens = prime_tokens_for($sectionCode,$concept);
    $syntax = $tokens === [] ? 'конструкция из названия темы' : implode(', ',$tokens);
    $theory = prime_internal_theory($concept,$tokens,$exerciseType === 'theory' ? ($sectionCode === 'DB' ? 'sql' : 'php') : $exerciseType).
        "\n\nЧто нужно знать для этого задания:\n".
        "• Входные данные уже заданы ниже — придумывать другие значения не нужно.\n".
        "• Синтаксические опоры: {$syntax}.\n".
        "• Результат нужно получить вычислением или обработкой данных, а не напечатать заранее.\n".
        "• Проверка ищет обязательные конструкции, опасные операции и синтаксические ошибки; PHP-код и SQL на сервере не выполняются.";

    if ($exerciseType === 'theory') {
        $goal = 'Научиться объяснять тему «'.$concept.'» своими словами и распознавать её применение в коде.';
        $input = "Тема: {$concept}\nСинтаксические опоры: {$syntax}\nКонтекст: {$sectionTitle}";
        $steps = "Напишите ответ из четырёх частей:\n".
            "1. «Определение» — что означает тема своими словами.\n".
            "2. «Когда применять» — один конкретный сценарий.\n".
            "3. «Мини-пример» — 3–8 строк кода или один SQL-запрос с указанными входными данными.\n".
            "4. «Граничный случай» — возможная ошибка и способ её обнаружить.";
        $expected = "В ответе есть все четыре заголовка: Определение, Когда применять, Мини-пример, Граничный случай. ".
            "Использована синтаксическая опора «{$syntax}», а пример относится к теме, а не является общим рассуждением.";
        $constraints = "Не копируйте формулировку теории дословно.\nНе отвечайте одним определением.\nВ мини-примере обязательно укажите конкретные входные данные и ожидаемый результат.";
        $selfCheck = "Проверьте наличие четырёх частей. Закройте теорию и перескажите определение вслух. Затем вручную пройдите мини-пример и назовите его результат.";
        $hint1 = 'Начните с фразы: «Эта конструкция нужна, когда…», затем назовите данные до и после обработки.';
        $hint2 = 'Шаблон: Определение → Когда применять → Мини-пример → Граничный случай.';
        $starter = "Определение:\n\nКогда применять:\n\nМини-пример:\n\nГраничный случай:\n";
        $solutionChecks = [
            ['needle'=>'Определение','label'=>'раздел «Определение»'],
            ['needle'=>'Когда применять','label'=>'раздел «Когда применять»'],
            ['needle'=>'Мини-пример','label'=>'раздел «Мини-пример»'],
            ['needle'=>'Граничный случай','label'=>'раздел «Граничный случай»'],
        ];
    }

    return [
        'goal'=>$goal,
        'theory'=>$theory,
        'input'=>$input,
        'steps'=>$steps,
        'expected'=>$expected,
        'constraints'=>$constraints,
        'self_check'=>$selfCheck,
        'hint1'=>$hint1,
        'hint2'=>$hint2,
        'starter'=>$starter,
        'solution_checks'=>$solutionChecks,
    ];
}

function seed_prime_exercise_details(PDO $pdo): void
{
    $rows = $pdo->query("SELECT e.id,e.code,e.exercise_type,e.sort_order,e.difficulty,
      l.title concept,s.code section_code,s.title section_title
      FROM course_exercises e
      JOIN course_lessons l ON l.id=e.lesson_id
      JOIN course_sections s ON s.id=l.section_id
      ORDER BY s.sort_order,e.sort_order")->fetchAll();

    $update = $pdo->prepare('UPDATE course_exercises
      SET prompt=?,starter_code=?,checker_config=?,source_url=NULL WHERE id=?');
    $detail = $pdo->prepare("INSERT INTO exercise_details
      (exercise_id,goal,theory_text,input_data,expected_result,constraints_text,self_check,hint1,hint2)
      VALUES (?,?,?,?,?,?,?,?,?)
      ON DUPLICATE KEY UPDATE goal=VALUES(goal),theory_text=VALUES(theory_text),
      input_data=VALUES(input_data),expected_result=VALUES(expected_result),
      constraints_text=VALUES(constraints_text),self_check=VALUES(self_check),
      hint1=VALUES(hint1),hint2=VALUES(hint2)");

    foreach ($rows as $row) {
        $spec = prime_task_spec(
            (string)$row['section_code'],(string)$row['section_title'],(string)$row['concept'],
            (string)$row['exercise_type'],(string)$row['code'],(int)$row['sort_order'],(int)$row['difficulty']
        );
        $tokens = prime_tokens_for((string)$row['section_code'],(string)$row['concept']);
        $checks = [];
        foreach ($tokens as $token) {
            $checks[] = ['needle'=>$token,'label'=>'используйте или объясните «'.$token.'»'];
        }
        foreach ($spec['solution_checks'] as $check) {
            $checks[] = $check;
        }

        $unique = [];
        $deduplicated = [];
        foreach ($checks as $check) {
            $key = (string)$check['needle'];
            if ($key !== '' && !isset($unique[$key])) {
                $unique[$key] = true;
                $deduplicated[] = $check;
            }
        }

        $type = (string)$row['exercise_type'];
        $config = [
            'checks'=>$deduplicated,
            'forbidden'=>$type === 'sql'
                ? ['DROP DATABASE','TRUNCATE','INTO OUTFILE']
                : ['eval(','shell_exec(','exec(','system(','passthru('],
            'min_length'=>$type === 'theory' ? 140 : 35,
            'syntax'=>$type === 'php' || $type === 'project',
            'starter_hash'=>hash('sha256',trim((string)$spec['starter'])),
        ];
        $update->execute([
            $spec['steps'],$spec['starter'],
            json_encode($config,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            $row['id']
        ]);
        $detail->execute([
            $row['id'],$spec['goal'],$spec['theory'],$spec['input'],$spec['expected'],
            $spec['constraints'],$spec['self_check'],$spec['hint1'],$spec['hint2']
        ]);
    }
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
    seed_prime_exercise_details($pdo);
}
