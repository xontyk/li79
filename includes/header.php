<?php
/**
 * Общая шапка публичных страниц.
 * Ожидает (необязательно): $pageTitle, $pageDescription, $activeSlug.
 * Требует, чтобы вызывающий файл уже подключил includes/functions.php.
 */

/** @var PDO $pdo */
$navPages = get_nav_pages($pdo);
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? SITE_NAME) ?></title>
<meta name="description" content="<?= e($pageDescription ?? '') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
<link rel="stylesheet" href="/css/style.css">
</head>
<body>
<a class="skip-link" href="#main">Перейти к содержимому</a>
<header class="site-header">
  <div class="container header-inner">
    <a href="/index.php" class="logo">
      <span class="logo-badge">79</span>
      <span class="logo-text">Лицей-интернат №79<br><small><?= e(SITE_CITY) ?></small></span>
    </a>
    <button class="burger" id="burgerBtn" aria-label="Открыть меню" aria-expanded="false" aria-controls="mainNav">
      <span></span><span></span><span></span>
    </button>
    <nav class="main-nav" id="mainNav">
      <ul>
        <?php foreach ($navPages as $navPage): ?>
          <?php
            $slug = $navPage['slug'];
            $href = page_url($slug);
            $isActive = ($activeSlug ?? '') === $slug;
          ?>
          <li><a href="<?= e($href) ?>"<?= $isActive ? ' class="active" aria-current="page"' : '' ?>><?= e($navPage['title']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <a href="/admin/login.php" class="btn btn-outline nav-admin-btn">Войти как администратор</a>
    </nav>
  </div>
</header>
<main id="main">
<?php foreach (get_flashes() as $flash): ?>
  <div class="container"><p class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></p></div>
<?php endforeach; ?>
