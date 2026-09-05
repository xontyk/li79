<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'about');
$pageTitle = ($page['title'] ?? 'О лицее') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'about';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'О лицее') ?></h1>
    <p>История, миссия и достижения лицея-интерната №79.</p>
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
