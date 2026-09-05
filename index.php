<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'home');
$pageTitle = SITE_NAME . ' — официальный сайт';
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'home';

require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="page-content"><?= render_page_blocks($page['content'] ?? '') ?></div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
