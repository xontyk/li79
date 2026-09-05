<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id && ($_POST['action'] ?? '') === 'delete') {
        $stmt = $pdo->prepare('SELECT photo_path FROM teachers WHERE id = ?');
        $stmt->execute([$id]);
        $teacher = $stmt->fetch();

        if ($teacher) {
            $pdo->prepare('DELETE FROM teachers WHERE id = ?')->execute([$id]);
            delete_uploaded_file($teacher['photo_path']);
            set_flash('success', 'Учитель удалён.');
        }
    }

    redirect('teachers.php');
}

$teachers = $pdo->query('SELECT * FROM teachers ORDER BY full_name ASC')->fetchAll();

$pageTitle = 'Учителя — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="admin-toolbar">
  <h1 style="margin:0;">Учителя</h1>
  <a href="teachers-form.php" class="btn btn-primary">+ Добавить учителя</a>
</div>

<?php if (!$teachers): ?>
  <p class="empty-state">Список учителей пуст. Добавьте первую запись.</p>
<?php else: ?>
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Фото</th><th>ФИО</th><th>Предмет</th><th>Действия</th></tr>
      </thead>
      <tbody>
        <?php foreach ($teachers as $teacher): ?>
          <tr>
            <td>
              <?php if (!empty($teacher['photo_path'])): ?>
                <img class="thumb" src="<?= e(UPLOAD_URL . '/' . $teacher['photo_path']) ?>" alt="Фото <?= e($teacher['full_name']) ?>">
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td><?= e($teacher['full_name']) ?></td>
            <td><?= e($teacher['subject']) ?></td>
            <td class="actions">
              <a href="teachers-form.php?id=<?= (int) $teacher['id'] ?>" class="btn btn-sm btn-outline">Редактировать</a>
              <form method="post" class="inline-form" onsubmit="return confirm('Удалить учителя «<?= e(addslashes($teacher['full_name'])) ?>»?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $teacher['id'] ?>">
                <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger">Удалить</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
