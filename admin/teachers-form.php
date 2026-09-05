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
    $removePhoto = !empty($_POST['remove_photo']);

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
            } elseif ($removePhoto && $photoPath) {
                delete_uploaded_file($photoPath);
                $photoPath = null;
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
$currentPhotoUrl = !empty($teacher['photo_path']) ? UPLOAD_URL . '/' . $teacher['photo_path'] : null;
?>
<h1><?= $teacher ? 'Редактирование учителя' : 'Добавить учителя' ?></h1>

<div class="admin-form-card">
  <?php foreach ($errors as $error): ?>
    <p class="alert alert-error"><?= e($error) ?></p>
  <?php endforeach; ?>

  <form method="post" enctype="multipart/form-data" novalidate id="teacherForm">
    <?= csrf_field() ?>
    <label>ФИО
      <input type="text" name="full_name" required autofocus value="<?= e($fullName) ?>">
    </label>
    <label>Предмет
      <input type="text" name="subject" required value="<?= e($subject) ?>">
    </label>

    <label>Фото (необязательно)</label>
    <div class="photo-dropzone" id="photoDropzone">
      <input type="file" name="photo" id="photoInput" class="photo-dropzone-input" accept="image/jpeg,image/png,image/webp" hidden>
      <div class="photo-dropzone-preview" id="photoDropzonePreview">
        <?php if ($currentPhotoUrl): ?>
          <img src="<?= e($currentPhotoUrl) ?>" alt="Текущее фото учителя">
        <?php else: ?>
          <div class="photo-dropzone-placeholder">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 16.5V6a2 2 0 0 1 2-2h4l1.5 2H18a2 2 0 0 1 2 2v8.5"/><path d="M2.5 18.5 8 13a2 2 0 0 1 2.8 0l1.7 1.7a2 2 0 0 0 2.8 0L18 12l3.5 3.5"/><circle cx="8" cy="9" r="1.5"/><path d="M2 18.5v0A2.5 2.5 0 0 0 4.5 21h15a2.5 2.5 0 0 0 2.5-2.5v0"/></svg>
            <span>Перетащите фото сюда<br>или нажмите, чтобы выбрать файл</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <input type="hidden" name="remove_photo" id="removePhotoField" value="0">
    <div class="photo-dropzone-actions">
      <button type="button" class="btn btn-sm btn-outline" id="photoChooseBtn">Выбрать файл</button>
      <button type="button" class="btn btn-sm btn-outline" id="photoRemoveBtn" <?= $currentPhotoUrl ? '' : 'hidden' ?>>Убрать фото</button>
    </div>
    <p class="form-hint">JPG, PNG или WebP. Фото автоматически сожмётся до высоты не более 720 пикселей — оригинал большого размера не сохраняется.</p>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Сохранить</button>
      <a href="teachers.php" class="btn btn-outline">Отмена</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
