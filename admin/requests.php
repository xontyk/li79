<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && in_array($action, ['approve', 'reject'], true)) {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $pdo->prepare("UPDATE admins SET status = ? WHERE id = ? AND status = 'pending'");
        $stmt->execute([$status, $id]);
        set_flash('success', $action === 'approve' ? 'Заявка одобрена, пользователь получит доступ.' : 'Заявка отклонена.');
    }

    redirect('requests.php');
}

$pending = $pdo->query("SELECT * FROM admins WHERE status = 'pending' ORDER BY created_at ASC")->fetchAll();

$pageTitle = 'Заявки на доступ — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Заявки на доступ</h1>

<?php if (!$pending): ?>
  <p class="empty-state">Новых заявок нет.</p>
<?php else: ?>
  <div class="table-wrap">
    <table class="admin-table">
      <thead>
        <tr><th>ФИО</th><th>E-mail</th><th>Дата регистрации</th><th>Действия</th></tr>
      </thead>
      <tbody>
        <?php foreach ($pending as $req): ?>
          <tr>
            <td><?= e($req['full_name']) ?></td>
            <td><?= e($req['email']) ?></td>
            <td><?= e(date('d.m.Y H:i', strtotime($req['created_at']))) ?></td>
            <td class="actions">
              <form method="post" class="inline-form">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int) $req['id'] ?>">
                <button type="submit" name="action" value="approve" class="btn btn-sm btn-primary">Принять</button>
                <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" onclick="return confirm('Отклонить заявку этого пользователя?');">Отклонить</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
