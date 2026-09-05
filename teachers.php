<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'teachers');
$pageTitle = ($page['title'] ?? 'Наши учителя') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'teachers';

$teachers = $pdo->query('SELECT * FROM teachers ORDER BY full_name ASC')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Наши учителя') ?></h1>
    <p>Педагогический коллектив лицея-интерната №79.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!empty($page['content'])): ?>
      <div class="page-content" style="margin-bottom:32px;"><?= $page['content'] ?></div>
    <?php endif; ?>

    <?php if (!$teachers): ?>
      <p class="empty-state">Список учителей пока не заполнен.</p>
    <?php else: ?>
      <div class="grid grid-4">
        <?php foreach ($teachers as $teacher): ?>
          <div class="person-card">
            <div class="person-photo">
              <?php if (!empty($teacher['photo_path'])): ?>
                <img src="<?= e(UPLOAD_URL . '/' . $teacher['photo_path']) ?>" alt="Фото учителя: <?= e($teacher['full_name']) ?>" loading="lazy">
              <?php else: ?>
                <?= e(mb_substr($teacher['full_name'], 0, 1, 'UTF-8')) ?>
              <?php endif; ?>
            </div>
            <div class="person-body">
              <h3><?= e($teacher['full_name']) ?></h3>
              <p class="subject"><?= e($teacher['subject']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
