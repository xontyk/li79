<?php
require_once __DIR__ . '/includes/functions.php';

$page = get_page($pdo, 'contacts');
$pageTitle = ($page['title'] ?? 'Контакты') . ' — ' . SITE_NAME;
$pageDescription = $page['meta_description'] ?? '';
$activeSlug = 'contacts';

$errors = [];
$formName = '';
$formContact = '';
$formMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formName = trim($_POST['name'] ?? '');
    $formContact = trim($_POST['contact'] ?? '');
    $formMessage = trim($_POST['message'] ?? '');

    if ($formName === '' || $formContact === '' || $formMessage === '') {
        $errors[] = 'Пожалуйста, заполните все поля формы.';
    } elseif (mb_strlen($formMessage) > 4000) {
        $errors[] = 'Сообщение слишком длинное.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, contact, message) VALUES (?, ?, ?)');
        $stmt->execute([$formName, $formContact, $formMessage]);
        set_flash('success', 'Спасибо! Ваше сообщение отправлено, мы свяжемся с вами в ближайшее время.');
        redirect('/contacts.php');
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <h1><?= e($page['title'] ?? 'Контакты') ?></h1>
    <p>Адрес, телефон и форма обратной связи лицея-интерната №79.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2">
      <div>
        <div class="page-content">
          <?= $page['content'] ?? '' ?>
        </div>
        <p><strong>Адрес:</strong> <?= e(SITE_ADDRESS) ?></p>
        <p><strong>Телефон:</strong> <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', SITE_PHONE)) ?>"><?= e(SITE_PHONE) ?></a></p>
        <p><strong>E-mail:</strong> <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
        <p><strong>Telegram:</strong> <a href="<?= e(SITE_TELEGRAM) ?>" target="_blank" rel="noopener"><?= e(SITE_TELEGRAM) ?></a></p>
      </div>
      <div class="card">
        <h2>Напишите нам</h2>
        <?php foreach ($errors as $error): ?>
          <p class="alert alert-error"><?= e($error) ?></p>
        <?php endforeach; ?>
        <form method="post" class="contact-form" id="contactForm" novalidate>
          <label>Ваше имя
            <input type="text" name="name" required value="<?= e($formName) ?>">
          </label>
          <label>Телефон или e-mail для связи
            <input type="text" name="contact" required value="<?= e($formContact) ?>">
          </label>
          <label>Сообщение
            <textarea name="message" required><?= e($formMessage) ?></textarea>
          </label>
          <button type="submit" class="btn btn-primary btn-block">Отправить</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
