<?php
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$core = core_page_files();

if ($slug === '' || isset($core[$slug])) {
    http_response_code(404);
    $pageTitle = 'Страница не найдена — ' . SITE_NAME;
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><h1>Страница не найдена</h1><p><a href="/index.php">Вернуться на главную</a></p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page = get_page($pdo, $slug);

if (!$page) {
    http_response_code(404);
    $pageTitle = 'Страница не найдена — ' . SITE_NAME;
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><h1>Страница не найдена</h1><p><a href="/index.php">Вернуться на главную</a></p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $page['title'] . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = $slug;

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title']) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="page-content"><?= render_page_blocks($page['content'] ?? '') ?></div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
