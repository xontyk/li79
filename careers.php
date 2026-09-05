<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'careers');
$pageTitle = ($page['title'] ?? 'Преподавателям') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'careers';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Преподавателям') ?></h1>
    <p>Вакансии и условия работы для преподавателей лицея-интерната №79.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="page-content">
      <?= $page['content'] ?? '' ?>
    </div>
    <p><a href="/contacts.php" class="btn btn-primary">Связаться с нами</a></p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
