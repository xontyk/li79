<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'home');
$pageTitle = SITE_NAME . ' — официальный сайт';
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'home';

$teachersCount = (int) $pdo->query('SELECT COUNT(*) FROM teachers')->fetchColumn();
$winnersCount = (int) $pdo->query('SELECT COUNT(*) FROM olympiad_winners')->fetchColumn();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-inner">
    <div>
      <span class="hero-eyebrow">Лицей-интернат №79 · <?= e(SITE_CITY) ?></span>
      <h1>Школа полного дня для мотивированных учеников</h1>
      <p class="hero-lead">Углублённое обучение, подготовка к олимпиадам, комфортный пансион и забота о каждом ученике — с 5 по 11 класс.</p>
      <div class="hero-actions">
        <a href="/admission.php" class="btn btn-accent">Как поступить</a>
        <a href="/about.php" class="btn btn-outline" style="border-color:#fff;color:#fff;">О лицее</a>
      </div>
    </div>
    <div class="hero-media">
      <img src="/img/hero.svg" alt="Ученики лицея-интерната №79 на занятиях" loading="lazy">
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-4">
      <div class="card stat-card">
        <div class="stat-number"><?= (int) $winnersCount ?>+</div>
        <div class="stat-label">призёров олимпиад</div>
      </div>
      <div class="card stat-card">
        <div class="stat-number">95%</div>
        <div class="stat-label">выпускников поступают в вузы</div>
      </div>
      <div class="card stat-card">
        <div class="stat-number"><?= (int) $teachersCount ?>+</div>
        <div class="stat-label">опытных преподавателей</div>
      </div>
      <div class="card stat-card">
        <div class="stat-number">20+</div>
        <div class="stat-label">лет работы лицея</div>
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="event-card">
      <div>
        <span class="hero-eyebrow">Мероприятие</span>
        <h2>День открытых дверей</h2>
        <p>Приглашаем будущих учеников и их родителей познакомиться с лицеем: расскажем об условиях поступления, покажем учебные классы и пансион, ответим на все вопросы.</p>
        <div class="hero-actions">
          <a href="/admission.php" class="btn btn-accent">Зарегистрироваться</a>
          <a href="/contacts.php" class="btn btn-outline" style="border-color:#fff;color:#fff;">Подробнее</a>
        </div>
      </div>
      <div class="event-meta">
        <dl>
          <dt>Дата</dt><dd>Ближайшая суббота месяца</dd>
          <dt>Время</dt><dd>11:00–14:00</dd>
          <dt>Телефон</dt><dd><?= e(SITE_PHONE) ?></dd>
          <dt>Адрес</dt><dd><?= e(SITE_ADDRESS) ?></dd>
        </dl>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Почему выбирают нас</span>
      <h2>Всё для успешной учёбы и комфортной жизни</h2>
    </div>
    <div class="grid grid-4">
      <div class="card feature-card">
        <div class="feature-icon">🏆</div>
        <h3>Олимпиады и конкурсы</h3>
        <p>Системная подготовка к предметным олимпиадам всех уровней под руководством опытных наставников.</p>
      </div>
      <div class="card feature-card">
        <div class="feature-icon">📘</div>
        <h3>Углублённое обучение</h3>
        <p>Профильные программы по математике, информатике, физике и другим предметам.</p>
      </div>
      <div class="card feature-card">
        <div class="feature-icon">🎓</div>
        <h3>Опытные преподаватели</h3>
        <p>Педагоги высшей категории, наставники призёров олимпиад и победители профессиональных конкурсов.</p>
      </div>
      <div class="card feature-card">
        <div class="feature-icon">🏠</div>
        <h3>Пансион и забота</h3>
        <p>Комфортное проживание, пятиразовое питание и внимательные воспитатели — школа полного дня.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Жизнь лицея</span>
      <h2>Не только учёба</h2>
    </div>
    <div class="gallery">
      <figure><img src="/img/life-1.svg" alt="Ученики на спортивном мероприятии лицея" loading="lazy"><figcaption>Спорт и активный отдых</figcaption></figure>
      <figure><img src="/img/life-2.svg" alt="Творческий кружок в лицее" loading="lazy"><figcaption>Творческие кружки</figcaption></figure>
      <figure><img src="/img/life-3.svg" alt="Ученики готовятся к олимпиаде" loading="lazy"><figcaption>Подготовка к олимпиадам</figcaption></figure>
    </div>
    <p style="text-align:center;margin-top:24px;"><a href="/life.php" class="btn btn-outline">Смотреть больше →</a></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Наши результаты</span>
      <h2>Наши выпускники поступают в ведущие вузы</h2>
    </div>
    <div class="results-logos">
      <span>МГУ</span><span>МФТИ</span><span>ВШЭ</span><span>КФУ</span><span>КНИТУ-КАИ</span><span>СПбГУ</span>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="telegram-block">
      <div>
        <span class="eyebrow">Будьте на связи</span>
        <h2>Подпишитесь на Telegram-канал лицея</h2>
        <p>Новости, объявления, фотоотчёты с мероприятий и полезная информация для родителей.</p>
        <a href="<?= e(SITE_TELEGRAM) ?>" class="btn btn-primary" target="_blank" rel="noopener">Подписаться на канал</a>
      </div>
      <div class="qr">QR-код канала</div>
    </div>
  </div>
</section>

<?php if (!empty($page['content'])): ?>
<section class="section">
  <div class="container">
    <div class="page-content"><?= $page['content'] ?></div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
