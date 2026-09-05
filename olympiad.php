<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'olympiad');
$pageTitle = ($page['title'] ?? 'Олимпиадники') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'olympiad';

$winners = $pdo->query('SELECT * FROM olympiad_winners ORDER BY year DESC, full_name ASC')->fetchAll();

$byYear = [];
foreach ($winners as $winner) {
    $byYear[$winner['year']][] = $winner;
}
krsort($byYear);

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Олимпиадники') ?></h1>
    <p>Ученики лицея — призёры олимпиад разных лет.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!empty($page['content'])): ?>
      <div class="page-content" style="margin-bottom:32px;"><?= render_page_blocks($page['content']) ?></div>
    <?php endif; ?>

    <?php if (!$byYear): ?>
      <p class="empty-state">Список призёров пока не заполнен.</p>
    <?php else: ?>
      <?php foreach ($byYear as $year => $items): ?>
        <div class="year-group">
          <h2><span class="year-badge"><?= (int) $year ?></span></h2>
          <ul class="winner-list">
            <?php foreach ($items as $winner): ?>
              <li class="winner-item">
                <span class="name"><?= e($winner['full_name']) ?></span>
                <span class="subject"><?= e($winner['subject']) ?></span>
                <?php if (!empty($winner['quote'])): ?>
                  <span class="quote">«<?= e($winner['quote']) ?>»</span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
