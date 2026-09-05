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

<section class="section section-soft">
  <div class="container">
    <div class="grid grid-4">
      <div class="card stat-card"><div class="stat-number">2003</div><div class="stat-label">год основания</div></div>
      <div class="card stat-card"><div class="stat-number">20+</div><div class="stat-label">лет работы</div></div>
      <div class="card stat-card"><div class="stat-number">40+</div><div class="stat-label">наград и дипломов</div></div>
      <div class="card stat-card"><div class="stat-number">95%</div><div class="stat-label">поступают в вузы</div></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
