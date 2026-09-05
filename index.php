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
      <h1>Нам доверяют самое ценное</h1>
      <p class="hero-lead">Качественное образование, сильные преподаватели, олимпиадные традиции и забота о каждом ученике — с 5 по 11 класс.</p>
      <div class="hero-actions">
        <a href="/admission.php" class="btn btn-primary">Поступить в лицей</a>
        <a href="/about.php" class="btn btn-outline">Узнать больше о лицее ↓</a>
      </div>
    </div>
    <div class="hero-media">
      <img src="/img/hero.svg" alt="Ученики лицея-интерната №79 на занятиях" loading="lazy">
    </div>
  </div>
  <div class="container stats-strip-wrap">
    <div class="stats-strip">
      <div class="stats-strip-item">
        <div class="stats-strip-icon">🏆</div>
        <div class="stats-strip-number"><?= (int) $winnersCount ?>+</div>
        <div class="stats-strip-label">призёров олимпиад</div>
      </div>
      <div class="stats-strip-item">
        <div class="stats-strip-icon">🎓</div>
        <div class="stats-strip-number">95%</div>
        <div class="stats-strip-label">поступают в вузы</div>
      </div>
      <div class="stats-strip-item">
        <div class="stats-strip-icon">👥</div>
        <div class="stats-strip-number"><?= (int) $teachersCount ?>+</div>
        <div class="stats-strip-label">опытных преподавателей</div>
      </div>
      <div class="stats-strip-item">
        <div class="stats-strip-icon">🏫</div>
        <div class="stats-strip-number">20+</div>
        <div class="stats-strip-label">лет работы лицея</div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="event-card">
      <div>
        <span class="hero-eyebrow">Мероприятие</span>
        <h2>День открытых дверей</h2>
        <p>Приглашаем будущих учеников и их родителей познакомиться с лицеем: расскажем об условиях поступления, покажем учебные классы и пансион, ответим на все вопросы.</p>
        <div class="event-chips">
          <span class="event-chip">📅 18 апреля (пятница)</span>
          <span class="event-chip">📅 16 мая (пятница)</span>
          <span class="event-chip">🕐 12:00</span>
        </div>
        <div class="hero-actions">
          <a href="/admission.php" class="btn btn-primary">Зарегистрироваться</a>
          <a href="/admission.php" class="btn btn-outline">Подробнее о поступлении →</a>
        </div>
      </div>
      <div class="event-poster">
        <span class="event-poster-badge">79</span>
        <h3>День открытых дверей</h3>
        <p>Идёт набор в 5 классы</p>
        <div class="event-poster-schedule">
          <span>18 апреля, 16 мая</span>
          <span>12:00</span>
        </div>
        <div class="event-poster-contact">
          <span>📞 <?= e(SITE_PHONE) ?></span>
          <span>📍 <?= e(SITE_ADDRESS) ?></span>
        </div>
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
    <div class="section-header-row">
      <div>
        <span class="eyebrow">Жизнь лицея</span>
        <h2>Больше, чем уроки</h2>
      </div>
      <div class="carousel-nav">
        <button type="button" class="carousel-btn" data-dir="-1" aria-label="Показать предыдущие фото">‹</button>
        <button type="button" class="carousel-btn" data-dir="1" aria-label="Показать следующие фото">›</button>
      </div>
    </div>
    <div class="gallery-scroll" id="lifeGallery">
      <figure>
        <img src="/img/life-7.svg" alt="Комфортные комнаты пансиона лицея" loading="lazy">
        <figcaption><strong>Комфортные условия</strong><span>Уютный пансион, всё для отдыха и учёбы.</span></figcaption>
      </figure>
      <figure>
        <img src="/img/life-4.svg" alt="Столовая лицея" loading="lazy">
        <figcaption><strong>Полноценное питание</strong><span>Пятиразовое питание, баланс и польза рациона.</span></figcaption>
      </figure>
      <figure>
        <img src="/img/life-1.svg" alt="Спортивный зал лицея" loading="lazy">
        <figcaption><strong>Спорт и здоровье</strong><span>Спортивный зал, секции и активный образ жизни.</span></figcaption>
      </figure>
      <figure>
        <img src="/img/life-2.svg" alt="Ученики на кружке в лицее" loading="lazy">
        <figcaption><strong>Кружки и развитие</strong><span>Робототехника, программирование, творчество.</span></figcaption>
      </figure>
    </div>
    <p style="text-align:center;margin-top:28px;"><a href="/life.php" class="btn btn-outline">Смотреть больше →</a></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="eyebrow">Наши результаты</span>
      <h2>Наши ученики поступают в ведущие вузы</h2>
    </div>
    <div class="results-strip">
      <div class="result-item"><span class="result-badge" style="background:#fdece0;color:#c2410c;">Л</span><span class="result-name">МФТИ</span></div>
      <div class="result-item"><span class="result-badge" style="background:#e6f0fd;color:#1d4ed8;">М</span><span class="result-name">МГУ<br>им. М.В. Ломоносова</span></div>
      <div class="result-item"><span class="result-badge" style="background:#fdeef4;color:#be185d;">В</span><span class="result-name">ВШЭ</span></div>
      <div class="result-item"><span class="result-badge" style="background:#eafaf0;color:#15803d;">С</span><span class="result-name">СПбГУ</span></div>
      <div class="result-item"><span class="result-badge" style="background:#f1ecfd;color:#6d28d9;">К</span><span class="result-name">КФУ</span></div>
      <div class="result-item"><span class="result-badge" style="background:#fef7e0;color:#a16207;">Р</span><span class="result-name">РУДН</span></div>
      <div class="result-item"><span class="result-badge" style="background:#e6f2fd;color:#0369a1;">М</span><span class="result-name">МГТУ<br>им. Н.Э. Баумана</span></div>
    </div>
  </div>
</section>

<section class="section section-soft">
  <div class="container">
    <div class="telegram-block">
      <div>
        <span class="hero-eyebrow" style="background:rgba(255,255,255,0.14);color:#cfe0f7;">Будьте на связи</span>
        <h2>Подпишитесь на Telegram-канал лицея</h2>
        <p>Новости, объявления, фотоотчёты с мероприятий и полезная информация для родителей.</p>
        <a href="<?= e(SITE_TELEGRAM) ?>" class="btn btn-accent" target="_blank" rel="noopener">Подписаться на канал</a>
      </div>
      <div class="telegram-visual">
        <div class="phone-mock">
          <div class="phone-mock-header">
            <span class="phone-mock-logo">79</span>
            <div><strong>Лицей-интернат №79</strong><span>1 248 подписчиков</span></div>
          </div>
          <div class="phone-mock-msg">Официальный канал лицея. Новости, события и жизнь лицея изнутри.</div>
          <div class="phone-mock-btn">ПОДПИСАТЬСЯ</div>
        </div>
        <div class="qr">QR-код канала</div>
      </div>
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
