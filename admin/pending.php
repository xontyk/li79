<?php
require_once __DIR__ . '/../includes/auth.php';

if (current_admin()) {
    redirect('dashboard.php');
}

$pageTitle = 'Заявка отправлена — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_auth_header.php';
?>
<div class="auth-card">
  <div class="auth-logo"><span class="logo-badge">79</span></div>
  <h1>Заявка отправлена</h1>
  <p class="alert alert-info">Ваша заявка на доступ к админ-панели отправлена и ожидает одобрения администратора. Как только заявку одобрят, вы сможете войти по указанным e-mail и паролю.</p>
  <p class="auth-switch"><a href="login.php">Перейти на страницу входа</a></p>
  <p class="auth-switch"><a href="/index.php">← Вернуться на сайт</a></p>
</div>
<?php require __DIR__ . '/../includes/admin_auth_footer.php'; ?>
