<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_admin()) {
    redirect('dashboard.php');
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Введите e-mail и пароль.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash']) || $admin['status'] !== 'approved') {
            $errors[] = 'Неверный e-mail или пароль, либо доступ ещё не одобрен администратором.';
        } else {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_email'] = $admin['email'];
            redirect('dashboard.php');
        }
    }
}

$pageTitle = 'Вход в админ-панель — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_auth_header.php';
?>
<div class="auth-card">
  <div class="auth-logo"><span class="logo-badge">79</span></div>
  <h1>Вход в админ-панель</h1>
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <label>E-mail
      <input type="email" name="email" required autofocus value="<?= e($email) ?>">
    </label>
    <label>Пароль
      <input type="password" name="password" required>
    </label>
    <button type="submit" class="btn btn-primary btn-block">Войти</button>
  </form>
  <p class="auth-switch">Нет доступа? <a href="register.php">Подать заявку на регистрацию</a></p>
  <p class="auth-switch"><a href="/index.php">← Вернуться на сайт</a></p>
</div>
<?php require __DIR__ . '/../includes/admin_auth_footer.php'; ?>
