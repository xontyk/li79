<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'life');
$pageTitle = ($page['title'] ?? 'Жизнь лицея') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'life';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Жизнь лицея') ?></h1>
    <p>Фотогалерея, распорядок дня, питание, спорт и кружки лицея-интерната.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="page-content">
      <?= $page['content'] ?? '' ?>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Фотогалерея</span>
      <h2>Атмосфера лицея</h2>
    </div>
    <div class="gallery">
      <figure><img src="/img/life-1.svg" alt="Ученики на спортивном мероприятии лицея" loading="lazy"><figcaption>Спорт и активный отдых</figcaption></figure>
      <figure><img src="/img/life-2.svg" alt="Творческий кружок в лицее" loading="lazy"><figcaption>Творческие кружки</figcaption></figure>
      <figure><img src="/img/life-3.svg" alt="Ученики готовятся к олимпиаде" loading="lazy"><figcaption>Подготовка к олимпиадам</figcaption></figure>
      <figure><img src="/img/life-4.svg" alt="Обед в столовой лицея" loading="lazy"><figcaption>Пятиразовое питание</figcaption></figure>
      <figure><img src="/img/life-5.svg" alt="Вечерняя самоподготовка в пансионе" loading="lazy"><figcaption>Вечерняя самоподготовка</figcaption></figure>
      <figure><img src="/img/life-6.svg" alt="Экскурсия учеников лицея" loading="lazy"><figcaption>Экскурсии и поездки</figcaption></figure>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Распорядок дня</span>
      <h2>Школа полного дня</h2>
    </div>
    <div class="grid grid-4">
      <div class="card"><h3>08:00–14:00</h3><p>Учебные занятия по расписанию.</p></div>
      <div class="card"><h3>14:00–16:00</h3><p>Обед и свободное время, кружки и секции.</p></div>
      <div class="card"><h3>16:00–19:00</h3><p>Самоподготовка, консультации педагогов.</p></div>
      <div class="card"><h3>19:00–22:00</h3><p>Ужин, отдых, вечерние мероприятия.</p></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
