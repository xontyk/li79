<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'admission');
$pageTitle = ($page['title'] ?? 'Поступающим') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'admission';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Поступающим') ?></h1>
    <p>Условия и этапы поступления, необходимые документы, актуальные даты.</p>
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
