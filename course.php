<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';
$user = require_auth();
$userId = (int)$user['id'];

try {
    $overview = course_overview($userId);
    $sections = course_sections_with_progress($userId);
} catch (Throwable) {
    redirect('upgrade.php');
}
$next = next_course_exercise($userId);
$percent = (int)$overview['total'] > 0 ? (int)round((int)$overview['accepted'] / (int)$overview['total'] * 100) : 0;

render_header('Курс PHP + MySQL', 'course');
?>
<section class="hero-panel">
    <div>
        <p class="eyebrow">447 самостоятельных заданий</p>
        <h2>Не копировать — вспоминать, писать и исправлять</h2>
        <p>Курс построен по тематической карте PHP8+, но формулировки и проверки созданы специально для этого тренажёра. Следующая десятка открывается после 8 принятых решений.</p>
    </div>
    <?php if ($next): ?><a class="button primary large" href="<?= h(url('exercise.php?id='.(int)$next['id'])) ?>">Продолжить: <?= h($next['code']) ?></a><?php endif; ?>
</section>

<div class="metric-grid">
    <article class="metric"><strong><?= (int)$overview['accepted'] ?>/<?= (int)$overview['total'] ?></strong><span>принято заданий</span></article>
    <article class="metric"><strong><?= $percent ?>%</strong><span>общий прогресс</span></article>
    <article class="metric"><strong><?= (int)$overview['needs_work'] ?></strong><span>требуют доработки</span></article>
    <article class="metric"><strong><?= (int)$overview['due_mistakes'] ?></strong><span>ошибок к повторению</span></article>
</div>

<section class="panel">
    <div class="section-heading">
        <div><p class="eyebrow">Учебный маршрут</p><h2>Разделы курса</h2></div>
        <a href="<?= h(url('mistakes.php')) ?>">Повторить ошибки →</a>
    </div>
    <div class="topic-list">
    <?php foreach ($sections as $section):
        $unlocked = course_section_unlocked($userId,(int)$section['id']);
        $count = (int)$section['exercise_count'];
        $accepted = (int)$section['accepted_count'];
        $sectionPercent = $count ? (int)round($accepted/$count*100) : 0;
    ?>
      <article class="topic-row <?= $unlocked ? '' : 'locked' ?>">
        <span class="topic-code"><?= h($section['code']) ?></span>
        <div class="grow">
          <div class="topic-title-line">
            <strong><?= h($section['title']) ?></strong>
            <?php if (!$unlocked): ?><span class="status">Закрыто</span>
            <?php elseif ($sectionPercent >= 80): ?><span class="status mastered">Освоено</span>
            <?php elseif ($accepted): ?><span class="status active">В работе</span>
            <?php else: ?><span class="status">Доступно</span><?php endif; ?>
          </div>
          <div class="progress-track"><span style="width:<?= $sectionPercent ?>%"></span></div>
          <small><?= $accepted ?> из <?= $count ?> · для освоения нужно <?= (int)ceil($count*.8) ?></small>
        </div>
        <?php if ($unlocked): ?>
          <a class="button ghost small" href="<?= h(url('exercise.php?section='.(int)$section['id'])) ?>">Решать</a>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
    </div>
</section>

<section class="callout">
  <strong>Об источнике:</strong> на сайте хранится ссылка на тематическое оглавление, но не копии чужих уроков и заданий.
  <a href="https://code.mu/ru/php/book/prime/" target="_blank" rel="noopener noreferrer">Открыть тематический ориентир ↗</a>
</section>
<?php render_footer(); ?>
