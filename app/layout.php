<?php
declare(strict_types=1);

function render_header(string $title, string $active = ''): void
{
    $user = current_user();
    $flash = take_flash();
    $items = [
        'today' => ['Сегодня', 'index.php'],
        'cards' => ['Карточки', 'cards.php'],
        'task' => ['Практика', 'task.php'],
        'progress' => ['Программа', 'progress.php'],
        'settings' => ['Настройки', 'settings.php'],
    ];
    ?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> — PHP Recall Lab</title>
    <link rel="stylesheet" href="<?= h(url('assets/app.css')) ?>">
    <script defer src="<?= h(url('assets/app.js')) ?>"></script>
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= h(url('index.php')) ?>">
        <span class="brand-mark">&lt;?php</span>
        <span>Recall Lab</span>
    </a>
    <?php if ($user): ?>
        <nav aria-label="Основная навигация">
            <?php foreach ($items as $key => [$label, $path]): ?>
                <a class="<?= $active === $key ? 'active' : '' ?>" href="<?= h(url($path)) ?>"><?= h($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <form method="post" action="<?= h(url('logout.php')) ?>" class="logout-form">
            <?= csrf_field() ?>
            <button class="button ghost small" type="submit">Выйти</button>
        </form>
    <?php endif; ?>
</header>
<main class="page">
    <?php if ($flash): ?>
        <div class="flash <?= h($flash['type']) ?>" role="status"><?= h($flash['message']) ?></div>
    <?php endif; ?>
    <div class="page-heading">
        <div>
            <p class="eyebrow">PHP + MySQL · самостоятельный тренажёр</p>
            <h1><?= h($title) ?></h1>
        </div>
        <?php if ($user): ?><span class="user-chip"><?= h($user['login']) ?></span><?php endif; ?>
    </div>
<?php
}

function render_footer(): void
{
    ?>
</main>
<footer class="footer">
    <p>PHP Recall Lab · ответы проверяются локальными правилами, произвольный PHP-код не выполняется.</p>
</footer>
</body>
</html>
<?php
}
