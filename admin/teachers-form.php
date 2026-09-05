<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$teacher = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM teachers WHERE id = ?');
    $stmt->execute([$id]);
    $teacher = $stmt->fetch();

    if (!$teacher) {
        set_flash('error', 'Запись не найдена.');
        redirect('teachers.php');
    }
}

$fullName = $teacher['full_name'] ?? '';
$subject = $teacher['subject'] ?? '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $fullName = trim($_POST['full_name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');

    if ($fullName === '' || $subject === '') {
        $errors[] = 'Заполните ФИО и предмет.';
    }

    $photoPath = $teacher['photo_path'] ?? null;

    if (!$errors) {
        try {
            $newPhoto = upload_and_resize_image('photo', 'teachers');
            if ($newPhoto !== null) {
                delete_uploaded_file($photoPath);
                $photoPath = $newPhoto;
            }
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        if ($teacher) {
            $stmt = $pdo->prepare('UPDATE teachers SET full_name = ?, subject = ?, photo_path = ? WHERE id = ?');
            $stmt->execute([$fullName, $subject, $photoPath, $teacher['id']]);
            set_flash('success', 'Данные учителя обновлены.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO teachers (full_name, subject, photo_path) VALUES (?, ?, ?)');
            $stmt->execute([$fullName, $subject, $photoPath]);
            set_flash('success', 'Учитель добавлен.');
        }
        redirect('teachers.php');
    }
}

$pageTitle = ($teacher ? 'Редактирование учителя' : 'Новый учитель') . ' — ' . SITE_NAME;
require __DIR__ . '/../includes/admin_header.php';
?>
<h1><?= $teacher ? 'Редактирование учителя' : 'Добавить учителя' ?></h1>

<div class="admin-form-card">
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>

  <?php if (!empty($teacher['photo_path'])): ?>
    <div class="current-photo">
      <img src="<?= e(UPLOAD_URL . '/' . $teacher['photo_path']) ?>" alt="Текущее фото учителя">
      <span>Текущее фото</span>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <label>ФИО
      <input type="text" name="full_name" required autofocus value="<?= e($fullName) ?>">
    </label>
    <label>Предмет
      <input type="text" name="subject" required value="<?= e($subject) ?>">
    </label>
    <label>Фото (необязательно)
      <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
    </label>
    <p class="form-hint">Изображение автоматически сожмётся до высоты не более 720 пикселей.</p>
    <img id="photoPreview" alt="Предпросмотр нового фото" hidden style="max-width:160px;border-radius:8px;margin-bottom:16px;">
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <a href="teachers.php" class="btn btn-outline">Отмена</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
