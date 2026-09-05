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

<?php require __DIR__ . '/includes/footer.php'; ?>
