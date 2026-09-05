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
$content = $page['content'];
$metaDescription = $page['meta_description'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $metaDescription = trim($_POST['meta_description'] ?? '');

    if ($title === '') {
        $errors[] = 'Укажите название страницы.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE pages SET title = ?, content = ?, meta_description = ? WHERE id = ?');
        $stmt->execute([$title, $content, $metaDescription !== '' ? $metaDescription : null, $page['id']]);
        set_flash('success', 'Страница сохранена.');
        redirect('pages.php');
    }
}

$pageTitle = 'Редактирование страницы — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Редактирование страницы «<?= e($page['title']) ?>»</h1>

<div class="admin-form-card" style="max-width:800px;">
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <label>Название страницы
      <input type="text" name="title" required value="<?= e($title) ?>">
    </label>
    <label>Описание для поисковых систем (meta description)
      <input type="text" name="meta_description" value="<?= e($metaDescription) ?>">
    </label>
    <label>Содержимое страницы
      <textarea name="content" rows="14"><?= e($content) ?></textarea>
    </label>
    <p class="form-hint">Можно использовать простые HTML-теги: &lt;p&gt;, &lt;strong&gt;, &lt;br&gt;, &lt;a&gt;, &lt;h2&gt; и т.д.</p>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <a href="pages.php" class="btn btn-outline">Отмена</a>
      <a href="<?= e(page_url($page['slug'])) ?>" class="btn btn-outline" target="_blank" rel="noopener">Смотреть на сайте</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
