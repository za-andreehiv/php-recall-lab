<?php
declare(strict_types=1);

/**
 * Учебный набор v1: 14 тем × 10 самостоятельных заданий.
 * Проверка намеренно основана на видимых признаках ответа и не запускает код пользователя.
 */
function seed_curriculum(PDO $pdo): void
{
    $curriculum = curriculum_data();
    $topicInsert = $pdo->prepare('INSERT INTO topics (code, title, sort_order, required_solutions, prerequisite_id) VALUES (?, ?, ?, 10, ?)');
    $taskInsert = $pdo->prepare('INSERT INTO tasks
        (topic_id, title, prompt, starter_text, checker_type, checker_config, hint1, hint2, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $cardInsert = $pdo->prepare('INSERT INTO cards (topic_id, question, answer) VALUES (?, ?, ?)');

    $previousId = null;
    foreach ($curriculum as $position => $topic) {
        $find = $pdo->prepare('SELECT id FROM topics WHERE code = ?');
        $find->execute([$topic['code']]);
        $topicId = (int) ($find->fetchColumn() ?: 0);

        if ($topicId === 0) {
            $topicInsert->execute([$topic['code'], $topic['title'], $position + 1, $previousId]);
            $topicId = (int) $pdo->lastInsertId();
        }
        $previousId = $topicId;

        $count = $pdo->prepare('SELECT COUNT(*) FROM tasks WHERE topic_id = ?');
        $count->execute([$topicId]);
        if ((int) $count->fetchColumn() === 0) {
            foreach ($topic['tasks'] as $i => $task) {
                $required = [];
                $forbidden = [];
                foreach (array_slice($task, 3) as $token) {
                    if (is_array($token)) {
                        $forbidden = array_merge($forbidden, $token);
                    } else {
                        $required[] = (string) $token;
                    }
                }
                $checks = [];
                foreach ($required as $needle) {
                    $checks[] = ['needle' => $needle, 'label' => 'используйте «' . $needle . '»'];
                }
                $config = [
                    'checks' => $checks,
                    'forbidden' => $forbidden,
                    'min_length' => 3,
                ];
                $taskInsert->execute([
                    $topicId, $task[0], $task[1], $task[2] ?? '',
                    'contains_all', json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    null, null, $i + 1,
                ]);
            }
        }

        $count = $pdo->prepare('SELECT COUNT(*) FROM cards WHERE topic_id = ?');
        $count->execute([$topicId]);
        if ((int) $count->fetchColumn() === 0) {
            foreach ($topic['cards'] as $card) {
                $cardInsert->execute([$topicId, $card[0], $card[1]]);
            }
        }
    }
}

function curriculum_data(): array
{
    return [
        [
            'code' => '11.01', 'title' => 'Базы данных и СУБД',
            'cards' => [
                ['Что такое база данных?', 'Организованное хранилище связанных данных.'],
                ['Что такое СУБД?', 'Программа, которая создаёт, хранит, изменяет и защищает базы данных.'],
                ['Пример реляционной СУБД', 'MySQL, PostgreSQL, MariaDB или SQLite.'],
                ['Зачем приложению СУБД?', 'Для надёжного хранения, поиска, изменения и совместной обработки данных.'],
            ],
            'tasks' => [
                ['БД своими словами','Объясните одним предложением, что такое база данных.','','данн'],
                ['Роль СУБД','Назовите две задачи СУБД.','','хран','измен'],
                ['Реляционная модель','Объясните, как таблица, строка и столбец соотносятся между собой.','','таблиц','строк','столб'],
                ['Выбор хранилища','Почему список товаров удобнее хранить в MySQL, а не в одном PHP-массиве?','','сохран','поиск'],
                ['Пример сущности','Предложите таблицу для сущности «товар» и назовите три поля.','','товар','name'],
                ['Связанные данные','Приведите пример связи двух таблиц интернет-магазина.','','товар','заказ'],
                ['СУБД или БД','Коротко опишите разницу между базой данных и СУБД.','','баз','программ'],
                ['CRUD','Расшифруйте смысл операций CRUD своими словами.','','создан','чтен','измен','удален'],
                ['Надёжность','Назовите два преимущества СУБД для постоянных данных.','','целост','резерв'],
                ['Мини-проект','Опишите, какие данные вашего тренажёра должны жить в базе.','','пользоват','задан','прогресс'],
            ],
        ],
        [
            'code' => '11.02', 'title' => 'Создание базы и кодировка',
            'cards' => [
                ['Как создать базу?', 'CREATE DATABASE имя CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'],
                ['Почему utf8mb4?', 'Он хранит полный Unicode, включая эмодзи и дополнительные символы.'],
                ['Что делает COLLATE?', 'Задаёт правила сравнения и сортировки строк.'],
                ['Где выбрать БД?', 'Командой USE имя_базы; или указать имя в DSN.'],
            ],
            'tasks' => [
                ['Создайте БД','Напишите SQL создания базы php_recall в utf8mb4.','CREATE DATABASE ','CREATE DATABASE','php_recall','utf8mb4'],
                ['Добавьте COLLATE','Создайте базу lab с utf8mb4_unicode_ci.','CREATE DATABASE ','CREATE DATABASE','lab','utf8mb4_unicode_ci'],
                ['Выберите БД','Напишите команду выбора базы php_recall.','USE ','USE','php_recall'],
                ['Удалять нельзя','Напишите безопасную команду CREATE DATABASE IF NOT EXISTS для lab.','','CREATE DATABASE','IF NOT EXISTS','lab',['DROP']],
                ['Кодировка таблицы','Напишите фрагмент настроек таблицы для utf8mb4.','','CHARSET=utf8mb4'],
                ['Сравнение строк','Укажите COLLATE utf8mb4_unicode_ci в SQL-фрагменте.','','COLLATE','utf8mb4_unicode_ci'],
                ['Пояснение utf8mb4','Почему для русского текста и эмодзи выбирают utf8mb4?','','Unicode','эмодзи'],
                ['phpMyAdmin','Опишите два действия в phpMyAdmin для создания БД с utf8mb4.','','создать','utf8mb4'],
                ['Имя базы','Предложите безопасное имя базы для курса без пробелов.','','course','_'],
                ['Полная команда','Напишите полную идемпотентную команду создания course_lab.','','CREATE DATABASE','IF NOT EXISTS','course_lab','utf8mb4'],
            ],
        ],
        [
            'code' => '11.03', 'title' => 'Типы данных MySQL',
            'cards' => [
                ['Тип целого идентификатора', 'INT UNSIGNED.'],
                ['Тип цены', 'DECIMAL(10,2), а не FLOAT.'],
                ['Тип короткой строки', 'VARCHAR(n).'],
                ['Тип даты создания', 'TIMESTAMP или DATETIME.'],
            ],
            'tasks' => [
                ['ID товара','Выберите SQL-тип для положительного id.','','INT','UNSIGNED'],
                ['Цена','Запишите тип для точной цены до 99 999 999,99.','','DECIMAL(10,2)'],
                ['Название','Запишите тип для названия длиной до 150 символов.','','VARCHAR(150)'],
                ['Большое описание','Какой тип взять для длинного описания?','','TEXT'],
                ['Флаг покупки','Запишите тип и допустимое значение по умолчанию для флага purchased.','','TINYINT(1)','DEFAULT 0'],
                ['Количество','Тип для неотрицательного количества товара.','','INT','UNSIGNED'],
                ['Дата создания','Фрагмент поля created_at с автоматическим текущим временем.','','TIMESTAMP','CURRENT_TIMESTAMP'],
                ['Дата изменения','Фрагмент updated_at, обновляющийся автоматически.','','TIMESTAMP','ON UPDATE CURRENT_TIMESTAMP'],
                ['Деньги без FLOAT','Объясните, почему цена хранится в DECIMAL, а не FLOAT.','','DECIMAL','точн'],
                ['Подберите типы','Запишите поля name, price и created_at с подходящими типами.','','name','VARCHAR','price','DECIMAL','created_at','TIMESTAMP'],
            ],
        ],
        [
            'code' => '11.04', 'title' => 'PRIMARY KEY и AUTO_INCREMENT',
            'cards' => [
                ['PRIMARY KEY', 'Уникально определяет строку и не допускает NULL.'],
                ['AUTO_INCREMENT', 'Автоматически выдаёт следующее числовое значение.'],
                ['Сколько PK в таблице?', 'Один первичный ключ, при необходимости составной.'],
                ['Зачем UNSIGNED для id?', 'Идентификатор не бывает отрицательным.'],
            ],
            'tasks' => [
                ['Поле id','Создайте поле id как положительный автоинкрементный первичный ключ.','','id','INT','UNSIGNED','AUTO_INCREMENT','PRIMARY KEY'],
                ['Отдельный ключ','Объявите id, затем отдельной строкой PRIMARY KEY.','','id','AUTO_INCREMENT','PRIMARY KEY (id)'],
                ['Уникальность','Объясните, что гарантирует PRIMARY KEY.','','уник','NULL'],
                ['Автонумерация','Объясните назначение AUTO_INCREMENT.','','автомат','id'],
                ['Вставка без id','Напишите INSERT товара без явного id.','','INSERT INTO','name','VALUES'],
                ['Последний id','Каким методом PDO получить созданный автоинкрементный id?','','lastInsertId'],
                ['Ошибка дубля','Что произойдёт при вставке уже существующего PRIMARY KEY?','','ошиб','дублик'],
                ['Составной ключ','Напишите ключ из user_id и card_id.','','PRIMARY KEY','user_id','card_id'],
                ['Не менять id','Почему id обычно не используют как изменяемое бизнес-значение?','','стабил','связ'],
                ['Полная таблица','Создайте мини-таблицу categories с id AUTO_INCREMENT и name.','','CREATE TABLE','categories','PRIMARY KEY','AUTO_INCREMENT'],
            ],
        ],
        [
            'code' => '11.05', 'title' => 'Ограничения и значения по умолчанию',
            'cards' => [
                ['NOT NULL', 'Запрещает отсутствие значения в столбце.'],
                ['DEFAULT', 'Подставляет значение, если столбец не указан в INSERT.'],
                ['UNIQUE', 'Запрещает повторяющиеся значения.'],
                ['FOREIGN KEY', 'Поддерживает ссылочную целостность между таблицами.'],
            ],
            'tasks' => [
                ['Обязательное имя','Опишите поле name VARCHAR(150), запрещающее NULL.','','name','VARCHAR(150)','NOT NULL'],
                ['Количество по умолчанию','Опишите quantity с DEFAULT 1.','','quantity','INT','DEFAULT 1'],
                ['Флаг по умолчанию','Опишите purchased, по умолчанию 0.','','purchased','TINYINT(1)','DEFAULT 0'],
                ['Уникальный email','Создайте поле email с ограничением UNIQUE.','','email','VARCHAR','UNIQUE'],
                ['Ссылка на категорию','Напишите FOREIGN KEY category_id → categories(id).','','FOREIGN KEY','category_id','REFERENCES categories','id'],
                ['Удаление связи','Добавьте ON DELETE SET NULL к внешнему ключу.','','ON DELETE SET NULL'],
                ['NOT NULL против DEFAULT','Объясните разницу между NOT NULL и DEFAULT.','','NOT NULL','DEFAULT'],
                ['Проверка цены','Напишите CHECK, запрещающий отрицательную цену.','','CHECK','price','>= 0'],
                ['Именованное ограничение','Назовите внешний ключ fk_product_category.','','CONSTRAINT','fk_product_category','FOREIGN KEY'],
                ['Комбинация','Создайте status VARCHAR(20) NOT NULL DEFAULT draft.','','status','VARCHAR(20)','NOT NULL','DEFAULT'],
            ],
        ],
        [
            'code' => '11.06', 'title' => 'INSERT',
            'cards' => [
                ['Базовый INSERT', 'INSERT INTO table (a, b) VALUES (:a, :b);'],
                ['Зачем перечислять столбцы?', 'Чтобы запрос не зависел от физического порядка полей.'],
                ['Несколько строк', 'После VALUES перечисляют группы значений через запятую.'],
                ['Безопасные данные', 'Передают через подготовленный запрос, а не склейкой SQL.'],
            ],
            'tasks' => [
                ['Один товар','Добавьте товар «Чай», цену 120.50 и quantity 2.','','INSERT INTO products','name','price','quantity','VALUES'],
                ['Только обязательные','Добавьте name и price, оставив значения DEFAULT остальным полям.','','INSERT INTO products','name','price','VALUES'],
                ['Именованные параметры','Напишите INSERT с :name и :price.','','INSERT INTO products',':name',':price'],
                ['Две строки','Одним INSERT добавьте два товара.','','INSERT INTO products','VALUES',')',','],
                ['Дата автоматически','Напишите INSERT без created_at и поясните, почему дата заполнится.','','INSERT INTO products','CURRENT_TIMESTAMP'],
                ['PDO prepare','Подготовьте INSERT через $pdo->prepare.','','prepare','INSERT INTO products'],
                ['PDO execute','Выполните запрос, передав name и price массивом.','','execute','name','price'],
                ['Без SQL-инъекции','Объясните, почему нельзя подставлять $_POST прямо в SQL.','','инъек','prepare'],
                ['Получить id','После INSERT получите новый id.','','lastInsertId'],
                ['Проверка результата','Напишите INSERT и затем SELECT добавленной строки по id.','','INSERT INTO','SELECT','WHERE id'],
            ],
        ],
        [
            'code' => '11.07', 'title' => 'SELECT',
            'cards' => [
                ['Все столбцы', 'SELECT * FROM products;'],
                ['Фильтрация', 'WHERE задаёт условие отбора.'],
                ['Сортировка', 'ORDER BY поле ASC или DESC.'],
                ['Ограничение', 'LIMIT ограничивает число строк.'],
            ],
            'tasks' => [
                ['Все товары','Выберите все товары.','','SELECT *','FROM products'],
                ['Нужные поля','Выберите id, name и price.','','SELECT','id','name','price','FROM products'],
                ['Цена выше','Выберите товары дороже 100.','','SELECT','FROM products','WHERE price > 100'],
                ['Не купленные','Выберите purchased = 0.','','SELECT','FROM products','WHERE purchased = 0'],
                ['Сортировка цены','Отсортируйте по цене от дорогих к дешёвым.','','ORDER BY price DESC'],
                ['Первые пять','Верните первые 5 строк.','','SELECT','FROM products','LIMIT 5'],
                ['Категория и цена','Фильтр category = tea и price <= 500.','','WHERE','category','price <= 500'],
                ['Поиск строки','Найдите названия, содержащие «кофе».','','LIKE','%кофе%'],
                ['Пустая категория','Выберите строки, где category неизвестна.','','category IS NULL'],
                ['Комплексный SELECT','Не купленные товары: name/price, цена по возрастанию, максимум 10.','','SELECT','name','price','FROM products','WHERE purchased = 0','ORDER BY price ASC','LIMIT 10'],
            ],
        ],
        [
            'code' => '11.08', 'title' => 'NULL и пустая строка',
            'cards' => [
                ['Что означает NULL?', 'Значение неизвестно или отсутствует.'],
                ['Что означает пустая строка?', 'Известное строковое значение длиной 0.'],
                ['Как искать NULL?', 'IS NULL или IS NOT NULL, не = NULL.'],
                ['COUNT(column) и NULL', 'COUNT(column) не считает NULL.'],
            ],
            'tasks' => [
                ['Смысл NULL','Объясните NULL одним предложением.','','неизвест','отсутств'],
                ['Смысл пустой строки','Объясните значение пустой строки.','','строк','нулев'],
                ['Найти NULL','Выберите товары без категории.','','WHERE category IS NULL'],
                ['Найти не NULL','Выберите товары с известной категорией.','','WHERE category IS NOT NULL'],
                ['Найти пустую строку','Выберите товары с category = пустой строке.','','WHERE category =','\'\''],
                ['Исправьте сравнение','Исправьте WHERE category = NULL.','','category IS NULL'],
                ['COUNT различие','Объясните разницу COUNT(*) и COUNT(category) при NULL.','','COUNT(*)','COUNT(category)','NULL'],
                ['Практический пример','Приведите пример, где NULL и пустая строка имеют разный смысл.','','NULL','пуст'],
                ['Нормализация ввода','Как превратить необязательное пустое поле формы в NULL в PHP/SQL?','','trim','NULL'],
                ['Три состояния','Опишите различие: NULL, пустая строка, строка «0».','','NULL','пуст','0'],
            ],
        ],
        [
            'code' => '11.09', 'title' => 'schema.sql',
            'cards' => [
                ['Зачем schema.sql?', 'Чтобы воспроизводимо создать структуру базы на новом окружении.'],
                ['Что хранить в schema.sql?', 'DDL: CREATE TABLE, индексы, ограничения и при необходимости начальные данные.'],
                ['Чего не хранить?', 'Пароли, секреты и пользовательские данные.'],
                ['Преимущество версии', 'Изменения схемы можно хранить вместе с кодом и проверять.'],
            ],
            'tasks' => [
                ['Назначение','Объясните, для чего проекту schema.sql.','','структур','баз','созда'],
                ['Мини-схема','Напишите CREATE TABLE notes с id и text.','','CREATE TABLE','notes','id','text'],
                ['Идемпотентность','Добавьте IF NOT EXISTS к CREATE TABLE.','','CREATE TABLE IF NOT EXISTS'],
                ['Движок','Укажите ENGINE=InnoDB.','','ENGINE=InnoDB'],
                ['Кодировка','Укажите DEFAULT CHARSET=utf8mb4.','','CHARSET=utf8mb4'],
                ['Первичный ключ','Добавьте PRIMARY KEY для id.','','PRIMARY KEY','id'],
                ['Секреты','Объясните, почему пароль БД нельзя хранить в schema.sql.','','парол','секрет'],
                ['Импорт','Опишите импорт schema.sql через phpMyAdmin.','','импорт','schema.sql'],
                ['Порядок таблиц','Почему родительскую таблицу создают до таблицы с FOREIGN KEY?','','FOREIGN KEY','существ'],
                ['Полный файл','Создайте schema.sql для categories и products со связью.','','CREATE TABLE','categories','products','FOREIGN KEY','InnoDB','utf8mb4'],
            ],
        ],
        [
            'code' => '11.10', 'title' => 'Таблица products',
            'cards' => [
                ['Поля products', 'id, name, category, price, quantity, purchased, created_at, updated_at.'],
                ['Почему price DECIMAL?', 'Чтобы десятичные деньги хранились точно.'],
                ['created_at', 'Заполняется CURRENT_TIMESTAMP при создании.'],
                ['updated_at', 'Меняется через ON UPDATE CURRENT_TIMESTAMP.'],
            ],
            'tasks' => [
                ['Каркас products','Создайте таблицу products с id и name.','','CREATE TABLE products','id','name','PRIMARY KEY'],
                ['Добавьте цену','Добавьте price DECIMAL(10,2) NOT NULL.','','price','DECIMAL(10,2)','NOT NULL'],
                ['Категория необязательна','Добавьте category VARCHAR(50) DEFAULT NULL.','','category','VARCHAR(50)','DEFAULT NULL'],
                ['Количество','Добавьте quantity INT UNSIGNED NOT NULL DEFAULT 1.','','quantity','INT UNSIGNED','NOT NULL','DEFAULT 1'],
                ['Флаг покупки','Добавьте purchased TINYINT(1) NOT NULL DEFAULT 0.','','purchased','TINYINT(1)','DEFAULT 0'],
                ['Дата создания','Добавьте created_at с CURRENT_TIMESTAMP.','','created_at','TIMESTAMP','CURRENT_TIMESTAMP'],
                ['Дата обновления','Добавьте updated_at с ON UPDATE.','','updated_at','ON UPDATE CURRENT_TIMESTAMP'],
                ['Настройки таблицы','Добавьте InnoDB, utf8mb4 и unicode_ci.','','ENGINE=InnoDB','utf8mb4','utf8mb4_unicode_ci'],
                ['Проверка структуры','Напишите команду, показывающую DDL таблицы products.','','SHOW CREATE TABLE products'],
                ['Соберите целиком','Напишите полный CREATE TABLE products со всеми полями темы.','','CREATE TABLE products','AUTO_INCREMENT','VARCHAR(150)','DECIMAL(10,2)','quantity','purchased','created_at','updated_at','InnoDB','utf8mb4'],
            ],
        ],
        [
            'code' => '12.01', 'title' => 'PDO и драйверы',
            'cards' => [
                ['Что такое PDO?', 'Единый объектный интерфейс PHP для работы с разными СУБД.'],
                ['Драйвер MySQL', 'pdo_mysql.'],
                ['Как проверить драйверы?', 'PDO::getAvailableDrivers().'],
                ['Почему PDO удобно?', 'Подготовленные запросы, исключения и единый API.'],
            ],
            'tasks' => [
                ['Расшифровка роли','Объясните, для чего нужен PDO.','','PHP','баз','интерфейс'],
                ['Драйвер','Назовите расширение PDO для MySQL.','','pdo_mysql'],
                ['Список драйверов','Напишите PHP-вызов списка доступных драйверов.','','PDO::getAvailableDrivers'],
                ['Проверка MySQL','Напишите условие проверки наличия mysql в драйверах.','','in_array','mysql','PDO::getAvailableDrivers'],
                ['Преимущество','Назовите два преимущества PDO.','','подготов','драйвер'],
                ['Исключения','Какой атрибут включает исключения при ошибках?','','PDO::ATTR_ERRMODE','PDO::ERRMODE_EXCEPTION'],
                ['Fetch mode','Как включить ассоциативные результаты по умолчанию?','','PDO::ATTR_DEFAULT_FETCH_MODE','PDO::FETCH_ASSOC'],
                ['phpinfo','Какие две строки в phpinfo подтверждают PDO MySQL?','','PDO support','pdo_mysql'],
                ['Не публиковать phpinfo','Почему info.php нужно удалить после проверки?','','информац','безопас'],
                ['Мини-проверка','Напишите скрипт, выводящий драйверы без паролей.','','PDO::getAvailableDrivers','print_r'],
            ],
        ],
        [
            'code' => '12.02', 'title' => 'Конфигурация и секреты',
            'cards' => [
                ['Где хранить пароль БД?', 'В непубличном config.php или переменной окружения.'],
                ['Что коммитить?', 'config.example.php без реальных секретов.'],
                ['Что добавить в .gitignore?', '/config.php.'],
                ['Почему тестовый вывод удалить?', 'Он может раскрыть пути, версии, DSN и другие детали.'],
            ],
            'tasks' => [
                ['Пример config','Создайте PHP-массив config с host, dbname, username, password без реальных значений.','','host','dbname','username','password'],
                ['Исключение Git','Запишите правило .gitignore для корневого config.php.','','/config.php'],
                ['Файл-пример','Объясните роль config.example.php.','','пример','секрет'],
                ['Пароль наружу','Почему нельзя выводить пароль БД в браузер?','','секрет','доступ'],
                ['Права файла','Какую идею реализуют ограниченные права на config.php?','','только','владел'],
                ['Разные окружения','Почему настройки локальной и боевой БД различаются?','','окруж','настрой'],
                ['Ошибка подключения','Что писать пользователю вместо полного PDOException?','','нейтрал','журнал'],
                ['Удалите диагностику','Назовите два временных диагностических файла/вывода, которые надо убрать.','','info.php','var_dump'],
                ['Путь подпапки','Укажите base_path для размещения в /it/courses.','','/it/courses'],
                ['Безопасная схема','Опишите цепочку config.example.php → копия config.php → .gitignore.','','config.example.php','config.php','.gitignore'],
            ],
        ],
        [
            'code' => '12.03', 'title' => 'DSN',
            'cards' => [
                ['Что такое DSN?', 'Строка с типом драйвера и параметрами подключения.'],
                ['DSN MySQL', 'mysql:host=HOST;dbname=DB;charset=utf8mb4'],
                ['Есть ли пароль в DSN PDO MySQL?', 'Обычно нет: логин и пароль передаются отдельными аргументами.'],
                ['Зачем charset?', 'Чтобы соединение корректно передавало Unicode.'],
            ],
            'tasks' => [
                ['Локальный DSN','Соберите DSN: host localhost, база recall, utf8mb4.','','mysql:','host=localhost','dbname=recall','charset=utf8mb4'],
                ['Переменные','Соберите DSN из $host и $dbname.','','mysql:host=','dbname=','charset=utf8mb4'],
                ['Без пароля','Объясните, почему пароль не включён в строку DSN.','','парол','отдель'],
                ['Смена порта','Добавьте port=3307.','','mysql:','port=3307'],
                ['Unix socket','Назовите параметр DSN для unix_socket.','','unix_socket'],
                ['Кодировка','Исправьте DSN без charset.','','charset=utf8mb4'],
                ['Опечатка','Запишите правильный параметр имени базы.','','dbname='],
                ['Из config','Соберите строку, обращаясь к $config[db][host] и name.','','mysql:host=','$config','dbname=','charset=utf8mb4'],
                ['Не URL','Объясните отличие DSN PDO от адреса веб-страницы.','','драйвер','подключ'],
                ['Полный пример','Запишите DSN MySQL с host, port, dbname и charset.','','mysql:','host=','port=','dbname=','charset=utf8mb4'],
            ],
        ],
        [
            'code' => '12.04', 'title' => 'Соединение PDO',
            'cards' => [
                ['Создание PDO', 'new PDO($dsn, $username, $password, $options).'],
                ['Режим ошибок', 'PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION.'],
                ['Fetch mode', 'PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC.'],
                ['Эмуляция prepares', 'PDO::ATTR_EMULATE_PREPARES => false для нативных prepared statements.'],
            ],
            'tasks' => [
                ['Мини-соединение','Создайте PDO из $dsn, $username и $password.','','new PDO','$dsn','$username','$password'],
                ['Исключения','Передайте опцию ERRMODE_EXCEPTION.','','PDO::ATTR_ERRMODE','PDO::ERRMODE_EXCEPTION'],
                ['Ассоциативный fetch','Передайте FETCH_ASSOC по умолчанию.','','PDO::ATTR_DEFAULT_FETCH_MODE','PDO::FETCH_ASSOC'],
                ['Нативные prepare','Отключите эмуляцию prepared statements.','','PDO::ATTR_EMULATE_PREPARES','false'],
                ['Try/catch','Оберните подключение в try/catch PDOException без вывода секрета.','','try','new PDO','catch','PDOException'],
                ['Проверка запроса','После подключения выполните SELECT 1.','','query','SELECT 1'],
                ['Конфигурация','Получите DSN, username и password из массива config.','','config','dsn','username','password','new PDO'],
                ['Функция db','Создайте функцию db(): PDO, возвращающую одно соединение static.','','function db','PDO','static'],
                ['Подготовленный SELECT','Подготовьте SELECT по id и выполните с параметром.','','prepare','WHERE id =','execute'],
                ['Полный фрагмент','Напишите безопасное подключение PDO со всеми тремя опциями темы.','','new PDO','ERRMODE_EXCEPTION','FETCH_ASSOC','EMULATE_PREPARES','false'],
            ],
        ],
    ];
}
