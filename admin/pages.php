<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$errors = [];
$newTitle = '';
$newSlug = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT * FROM pages WHERE id = ?');
        $stmt->execute([$id]);
        $page = $stmt->fetch();

        if (!$page) {
            set_flash('error', 'Страница не найдена.');
        } elseif ((int) $page['protected'] === 1) {
            set_flash('error', 'Служебную страницу «' . $page['title'] . '» нельзя удалить.');
        } else {
            $pdo->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]);
            set_flash('success', 'Страница удалена.');
        }

        redirect('pages.php');
    }

    if ($action === 'create') {
        $newTitle = trim($_POST['title'] ?? '');
        $requestedSlug = trim($_POST['slug'] ?? '');

        if ($newTitle === '') {
            $errors[] = 'Укажите название страницы.';
        } else {
            $slug = $requestedSlug !== '' ? slugify($requestedSlug) : slugify($newTitle);
            $newSlug = $slug;

            $core = core_page_files();
            $stmt = $pdo->prepare('SELECT id FROM pages WHERE slug = ?');
            $stmt->execute([$slug]);

            if (isset($core[$slug]) || $stmt->fetch()) {
                $errors[] = 'Страница с таким адресом (slug) уже существует. Выберите другой адрес.';
            } else {
                $maxOrder = (int) $pdo->query('SELECT COALESCE(MAX(nav_order), 0) FROM pages')->fetchColumn();
                $initialContent = sanitize_page_blocks([
                    ['type' => 'paragraph', 'html' => '<p>Новая страница. Отредактируйте текст и добавьте блоки ниже.</p>'],
                ]);
                $stmt = $pdo->prepare('INSERT INTO pages (slug, title, content, nav_order) VALUES (?, ?, ?, ?)');
                $stmt->execute([$slug, $newTitle, $initialContent, $maxOrder + 10]);
                set_flash('success', 'Страница создана. Теперь можно наполнить её содержимым.');
                redirect('pages.php');
            }
        }
    }
}

$pages = $pdo->query('SELECT * FROM pages ORDER BY nav_order ASC, id ASC')->fetchAll();

$pageTitle = 'Страницы сайта — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Страницы сайта</h1>

<div class="table-wrap" style="margin-bottom:28px;">
  <table class="admin-table">
    <thead>
      <tr><th>Название</th><th>Адрес (slug)</th><th>Обновлено</th><th>Действия</th></tr>
    </thead>
    <tbody>
      <?php foreach ($pages as $page): ?>
        <tr>
          <td><?= e($page['title']) ?></td>
          <td><code><?= e($page['slug']) ?></code></td>
          <td><?= e(date('d.m.Y H:i', strtotime($page['updated_at']))) ?></td>
          <td class="actions">
            <a href="page-edit.php?id=<?= (int) $page['id'] ?>" class="btn btn-sm btn-outline">Редактировать</a>
            <a href="<?= e(page_url($page['slug'])) ?>" class="btn btn-sm btn-outline" target="_blank" rel="noopener">Смотреть</a>
            <?php if ((int) $page['protected'] === 0): ?>
              <form method="post" class="inline-form" onsubmit="return confirm('Удалить страницу «<?= e(addslashes($page['title'])) ?>»?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $page['id'] ?>">
                <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger">Удалить</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<h2>Добавить новую страницу</h2>
<div class="admin-form-card">
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="create">
    <label>Название страницы
      <input type="text" name="title" required value="<?= e($newTitle) ?>">
    </label>
    <label>Адрес страницы (slug, необязательно)
      <input type="text" name="slug" placeholder="например: partners" value="<?= e($newSlug) ?>">
    </label>
    <p class="form-hint">Если не указать адрес, он будет сформирован автоматически из названия. Страница появится в меню сайта.</p>
    <button type="submit" class="btn btn-primary">Создать страницу</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
