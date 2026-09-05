<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int) ($_POST['id'] ?? 0);

    if ($id && ($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare('DELETE FROM olympiad_winners WHERE id = ?')->execute([$id]);
        set_flash('success', 'Запись удалена.');
    }

    redirect('olympiad.php');
}

$winners = $pdo->query('SELECT * FROM olympiad_winners ORDER BY year DESC, full_name ASC')->fetchAll();

$pageTitle = 'Олимпиадники — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="admin-toolbar">
  <h1 style="margin:0;">Олимпиадники</h1>
  <a href="olympiad-form.php" class="btn btn-primary">+ Добавить олимпиадника</a>
</div>

<?php if (!$winners): ?>
  <p class="empty-state">Список пуст. Добавьте первую запись.</p>
<?php else: ?>
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>Год</th><th>ФИО</th><th>Предмет</th><th>Фраза</th><th>Действия</th></tr>
      </thead>
      <tbody>
        <?php foreach ($winners as $winner): ?>
          <tr>
            <td><?= (int) $winner['year'] ?></td>
            <td><?= e($winner['full_name']) ?></td>
            <td><?= e($winner['subject']) ?></td>
            <td><?= e($winner['quote'] ?? '') ?></td>
            <td class="actions">
              <a href="olympiad-form.php?id=<?= (int) $winner['id'] ?>" class="btn btn-sm btn-outline">Редактировать</a>
              <form method="post" class="inline-form" onsubmit="return confirm('Удалить запись «<?= e(addslashes($winner['full_name'])) ?>»?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $winner['id'] ?>">
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
