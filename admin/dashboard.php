<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$pendingCount = (int) $pdo->query("SELECT COUNT(*) FROM admins WHERE status = 'pending'")->fetchColumn();
$pagesCount = (int) $pdo->query('SELECT COUNT(*) FROM pages')->fetchColumn();
$olympiadCount = (int) $pdo->query('SELECT COUNT(*) FROM olympiad_winners')->fetchColumn();
$teachersCount = (int) $pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();

$pageTitle = 'Панель управления — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<h1>Добро пожаловать, <?= e(current_admin()['full_name']) ?>!</h1>
<div class="dashboard-cards">
  <div class="dashboard-card">
    <span>Заявки на доступ</span>
    <span class="count"><?= $pendingCount ?></span>
    <a href="requests.php">Перейти к заявкам →</a>
  </div>
  <div class="dashboard-card">
    <span>Страницы сайта</span>
    <span class="count"><?= $pagesCount ?></span>
    <a href="pages.php">Управление страницами →</a>
  </div>
  <div class="dashboard-card">
    <span>Олимпиадники</span>
    <span class="count"><?= $olympiadCount ?></span>
    <a href="olympiad.php">Управление списком →</a>
  </div>
  <div class="dashboard-card">
    <span>Учителя</span>
    <span class="count"><?= $teachersCount ?></span>
    <a href="teachers.php">Управление учителями →</a>
  </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
