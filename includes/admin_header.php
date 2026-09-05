<?php
/**
 * Обёртка для страниц панели администратора (с сайдбаром).
 * Вызывающий файл обязан вызвать require_login() до подключения этого файла.
 */

/** @var PDO $pdo */
$admin = current_admin();
$currentFile = basename($_SERVER['SCRIPT_NAME']);

$navItems = [
    'dashboard.php' => ['label' => 'Главная', 'match' => ['dashboard.php']],
    'requests.php' => ['label' => 'Заявки на доступ', 'match' => ['requests.php']],
    'pages.php' => ['label' => 'Страницы сайта', 'match' => ['pages.php', 'page-edit.php']],
    'olympiad.php' => ['label' => 'Олимпиадники', 'match' => ['olympiad.php', 'olympiad-form.php']],
    'teachers.php' => ['label' => 'Учителя', 'match' => ['teachers.php', 'teachers-form.php']],
];
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Админ-панель — ' . SITE_NAME) ?></title>
<link rel="stylesheet" href="/css/style.css">
<link rel="stylesheet" href="/css/admin.css">
<?php foreach ($adminExtraCss ?? [] as $cssHref): ?>
<link rel="stylesheet" href="<?= e($cssHref) ?>">
<?php endforeach; ?>
</head>
<body class="admin-body">
<div class="admin-shell">
  <header class="admin-topbar">
    <button class="burger" id="adminBurgerBtn" aria-label="Открыть меню" aria-expanded="false" aria-controls="adminSidebar">
      <span></span><span></span><span></span>
    </button>
    <span class="admin-topbar-title"><?= e($pageTitle ?? 'Админ-панель') ?></span>
  </header>
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-logo">
      <span class="logo-badge">79</span>
      <span>Админ-панель</span>
    </div>
    <nav class="admin-nav">
      <?php foreach ($navItems as $href => $item): ?>
        <a href="<?= e($href) ?>"<?= in_array($currentFile, $item['match'], true) ? ' class="active"' : '' ?>><?= e($item['label']) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="admin-user">
      <p><?= e($admin['full_name']) ?></p>
      <p class="admin-user-email"><?= e($admin['email']) ?></p>
      <a href="logout.php" class="btn btn-sm btn-outline btn-block">Выйти</a>
      <a href="/index.php" class="admin-site-link">← Вернуться на сайт</a>
    </div>
  </aside>
  <main class="admin-content">
    <?php foreach (get_flashes() as $flash): ?>
      <p class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></p>
    <?php endforeach; ?>
