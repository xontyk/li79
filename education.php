<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'education');
$pageTitle = ($page['title'] ?? 'Обучение') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'education';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Обучение') ?></h1>
    <p>Программы обучения, профили, углублённые предметы и кружки лицея.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="page-content">
      <?= render_page_blocks($page['content'] ?? '') ?>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Профили обучения</span>
      <h2>Углублённое изучение профильных предметов</h2>
    </div>
    <div class="grid grid-3">
      <div class="card feature-card"><h3>Математика и информатика</h3><p>Подготовка к олимпиадам и профильному ЕГЭ, программирование, алгоритмы.</p></div>
      <div class="card feature-card"><h3>Физико-математический</h3><p>Углублённая физика и математика для будущих инженеров.</p></div>
      <div class="card feature-card"><h3>Гуманитарный</h3><p>Русский язык, литература, история и обществознание на профильном уровне.</p></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
