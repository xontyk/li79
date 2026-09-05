<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM pages WHERE id = ?');
$stmt->execute([$id]);
$page = $stmt->fetch();

if (!$page) {
    set_flash('error', 'Страница не найдена.');
    redirect('pages.php');
}

$title = $page['title'];
$metaDescription = $page['meta_description'] ?? '';
$blocks = decode_page_blocks($page['content']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $title = trim($_POST['title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $blocksJson = $_POST['blocks_json'] ?? '[]';
    $decodedBlocks = json_decode($blocksJson, true);

    if ($title === '') {
        $errors[] = 'Укажите название страницы.';
    }

    if (!is_array($decodedBlocks)) {
        $decodedBlocks = [];
    }

    if (!$errors) {
        $contentJson = sanitize_page_blocks($decodedBlocks);
        $stmt = $pdo->prepare('UPDATE pages SET title = ?, content = ?, meta_description = ? WHERE id = ?');
        $stmt->execute([$title, $contentJson, $metaDescription !== '' ? $metaDescription : null, $page['id']]);
        set_flash('success', 'Страница сохранена.');
        redirect('pages.php');
    }

    // При ошибке показываем то, что администратор ввёл, а не то, что было в БД.
    $blocks = is_array($decodedBlocks) ? $decodedBlocks : [];
}

$pageTitle = 'Редактирование страницы — ' . SITE_NAME;
$adminExtraCss = ['/css/vendor/quill/quill.snow.css'];
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Редактирование страницы «<?= e($page['title']) ?>»</h1>

<div class="admin-form-card admin-form-card-wide">
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>

  <form method="post" id="pageEditForm" novalidate>
    <?= csrf_field() ?>
    <label>Название страницы
      <input type="text" name="title" required value="<?= e($title) ?>">
    </label>
    <label>Описание для поисковых систем (meta description)
      <input type="text" name="meta_description" value="<?= e($metaDescription) ?>">
    </label>

    <label>Содержимое страницы</label>
    <p class="form-hint" style="margin-top:-4px;">Страница собирается из блоков — как в конструкторе сайтов: добавляйте, перетаскивайте порядок стрелками, удаляйте. Каждый блок редактируется прямо здесь.</p>

    <div class="block-editor" id="blockEditor">
      <div class="block-list" id="blockList"></div>
      <div class="block-add">
        <span class="block-add-label">Добавить блок:</span>
        <div class="block-add-buttons">
          <button type="button" class="btn btn-sm btn-outline" data-add-block="heading">🔠 Заголовок</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="paragraph">📝 Текст</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="image">🖼 Фото</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="cover">🌄 Баннер с фоном</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="gallery">🖼️ Галерея фото</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="cards">🗂 Карточки</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="stats">📊 Статистика</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="button">🔘 Кнопка</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="quote">❝ Цитата</button>
          <button type="button" class="btn btn-sm btn-outline" data-add-block="list">📋 Список</button>
        </div>
      </div>
    </div>
    <input type="hidden" name="blocks_json" id="blocksJsonField">

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <button type="button" class="btn btn-outline" id="previewPageBtn">👁 Предпросмотр</button>
      <a href="pages.php" class="btn btn-outline">Отмена</a>
      <a href="<?= e(page_url($page['slug'])) ?>" class="btn btn-outline" target="_blank" rel="noopener">Смотреть на сайте</a>
    </div>
  </form>
</div>

<form method="post" action="preview.php" target="_blank" id="previewForm" hidden>
  <?= csrf_field() ?>
  <input type="hidden" name="title" id="previewTitleField">
  <input type="hidden" name="blocks_json" id="previewBlocksField">
</form>

<script>window.PAGE_EDITOR_CSRF = <?= json_encode(csrf_token()) ?>;</script>
<script>window.INITIAL_BLOCKS = <?= json_encode($blocks, JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="/js/vendor/quill/quill.min.js"></script>
<script src="/js/page-blocks-editor.js"></script>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
