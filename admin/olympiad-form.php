<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$winner = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM olympiad_winners WHERE id = ?');
    $stmt->execute([$id]);
    $winner = $stmt->fetch();

    if (!$winner) {
        set_flash('error', 'Запись не найдена.');
        redirect('olympiad.php');
    }
}

$year = $winner['year'] ?? (int) date('Y');
$fullName = $winner['full_name'] ?? '';
$subject = $winner['subject'] ?? '';
$quote = $winner['quote'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $year = (int) ($_POST['year'] ?? 0);
    $fullName = trim($_POST['full_name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $quote = trim($_POST['quote'] ?? '');

    if ($fullName === '' || $subject === '') {
        $errors[] = 'Заполните ФИО и предмет.';
    }

    if ($year < 2000 || $year > 2100) {
        $errors[] = 'Укажите корректный год.';
    }

    if (!$errors) {
        $quoteValue = $quote !== '' ? $quote : null;

        if ($winner) {
            $stmt = $pdo->prepare('UPDATE olympiad_winners SET year = ?, full_name = ?, subject = ?, quote = ? WHERE id = ?');
            $stmt->execute([$year, $fullName, $subject, $quoteValue, $winner['id']]);
            set_flash('success', 'Запись обновлена.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO olympiad_winners (year, full_name, subject, quote) VALUES (?, ?, ?, ?)');
            $stmt->execute([$year, $fullName, $subject, $quoteValue]);
            set_flash('success', 'Олимпиадник добавлен.');
        }
        redirect('olympiad.php');
    }
}

$pageTitle = ($winner ? 'Редактирование записи' : 'Новый олимпиадник') . ' — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<h1><?= $winner ? 'Редактирование записи' : 'Добавить олимпиадника' ?></h1>

<div class="admin-form-card">
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <label>Год
      <input type="number" name="year" required min="2000" max="2100" value="<?= e((string) $year) ?>">
    </label>
    <label>ФИО ученика
      <input type="text" name="full_name" required value="<?= e($fullName) ?>">
    </label>
    <label>Предмет
      <input type="text" name="subject" required value="<?= e($subject) ?>">
    </label>
    <label>Фраза ученика (необязательно)
      <textarea name="quote"><?= e($quote) ?></textarea>
    </label>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <a href="olympiad.php" class="btn btn-outline">Отмена</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
