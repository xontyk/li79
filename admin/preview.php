<?php
/**
 * Предпросмотр несохранённых изменений страницы: рендерит блоки в реальном
 * оформлении сайта, но ничего не пишет в базу данных. Открывается из
 * редактора страницы в новой вкладке кнопкой «Предпросмотр».
 */

require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Метод не поддерживается.');
}

check_csrf();

$title = trim($_POST['title'] ?? '') ?: 'Страница без названия';
$decodedBlocks = json_decode($_POST['blocks_json'] ?? '[]', true);

if (!is_array($decodedBlocks)) {
    $decodedBlocks = [];
}

$contentJson = sanitize_page_blocks($decodedBlocks);

$pageTitle = $title . ' — предпросмотр — ' . SITE_NAME;
$pageDescription = '';
$activeSlug = null;

require __DIR__ . '/../includes/header.php';
?>
<div class="preview-banner">Это предпросмотр несохранённых изменений — страница ещё не опубликована.</div>

<section class="page-hero">
  <div class="container">
    <h1><?= e($title) ?></h1>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="page-content"><?= render_page_blocks($contentJson) ?></div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
