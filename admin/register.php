<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_admin()) {
    redirect('dashboard.php');
}

$errors = [];
$fullName = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($fullName === '' || $email === '' || $password === '' || $passwordConfirm === '') {
        $errors[] = 'Заполните все поля формы.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный e-mail.';
    } elseif (mb_strlen($password) < 8) {
        $errors[] = 'Пароль должен содержать не менее 8 символов.';
    } elseif ($password !== $passwordConfirm) {
        $errors[] = 'Пароли не совпадают.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким e-mail уже зарегистрирован.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO admins (full_name, email, password_hash, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$fullName, $email, password_hash($password, PASSWORD_DEFAULT), 'pending']);
            redirect('pending.php');
        }
    }
}

$pageTitle = 'Регистрация — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_auth_header.php';
?>
<div class="auth-card">
  <div class="auth-logo"><span class="logo-badge">79</span></div>
  <h1>Заявка на доступ к админ-панели</h1>
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>
  <form method="post" id="registerForm" novalidate>
    <?= csrf_field() ?>
    <label>ФИО
      <input type="text" name="full_name" required autofocus value="<?= e($fullName) ?>">
    </label>
    <label>E-mail
      <input type="email" name="email" required value="<?= e($email) ?>">
    </label>
    <label>Пароль
      <input type="password" name="password" id="password" required minlength="8">
    </label>
    <label>Повторите пароль
      <input type="password" name="password_confirm" id="passwordConfirm" required minlength="8">
    </label>
    <p class="form-error" id="passwordError" hidden>Пароли не совпадают.</p>
    <button type="submit" class="btn btn-primary btn-block">Отправить заявку</button>
  </form>
  <p class="auth-switch">Уже есть доступ? <a href="login.php">Войти</a></p>
  <p class="auth-switch"><a href="/index.php">← Вернуться на сайт</a></p>
</div>
<?php require __DIR__ . '/../includes/admin_auth_footer.php'; ?>
