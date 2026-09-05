<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'life');
$pageTitle = ($page['title'] ?? 'Жизнь лицея') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'life';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Жизнь лицея') ?></h1>
    <p>Фотогалерея, распорядок дня, питание, спорт и кружки лицея-интерната.</p>
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
