<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'admission');
$pageTitle = ($page['title'] ?? 'Поступающим') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'admission';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Поступающим') ?></h1>
    <p>Условия и этапы поступления, необходимые документы, актуальные даты.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="page-content">
      <?= render_page_blocks($page['content'] ?? '') ?>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Этапы поступления</span>
      <h2>Как поступить в лицей</h2>
    </div>
    <div class="grid grid-4">
      <div class="card"><h3>1. Заявление</h3><p>Подача заявления и документов в приёмную комиссию.</p></div>
      <div class="card"><h3>2. Тестирование</h3><p>Вступительное тестирование по математике и русскому языку.</p></div>
      <div class="card"><h3>3. Собеседование</h3><p>Встреча с кандидатом и родителями.</p></div>
      <div class="card"><h3>4. Зачисление</h3><p>Публикация результатов и оформление документов.</p></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
