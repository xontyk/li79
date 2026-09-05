<?php

require_once __DIR__ . '/db.php';

/** Экранирует строку для безопасного вывода в HTML. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/**
 * Публичные страницы, у которых есть собственный php-шаблон (карта сайта, п.5.1).
 * Любая другая страница из таблицы pages открывается через общий page.php?slug=...
 */
function core_page_files(): array
{
    return [
        'home' => 'index.php',
        'about' => 'about.php',
        'education' => 'education.php',
        'admission' => 'admission.php',
        'life' => 'life.php',
        'teachers' => 'teachers.php',
        'olympiad' => 'olympiad.php',
        'careers' => 'careers.php',
        'contacts' => 'contacts.php',
    ];
}

function page_url(string $slug): string
{
    $core = core_page_files();
    return isset($core[$slug]) ? '/' . $core[$slug] : '/page.php?slug=' . urlencode($slug);
}

function get_page(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM pages WHERE slug = ? LIMIT 1');
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
    return $page ?: null;
}

function get_nav_pages(PDO $pdo): array
{
    return $pdo->query('SELECT slug, title FROM pages ORDER BY nav_order ASC, id ASC')->fetchAll();
}

/**
 * Преобразует произвольную строку в slug для новой страницы:
 * латиница/цифры/дефисы в нижнем регистре.
 */
function slugify(string $text): string
{
    $map = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '',
        'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];

    $text = mb_strtolower($text, 'UTF-8');
    $text = strtr($text, $map);
    $text = preg_replace('~[^a-z0-9]+~', '-', $text);
    $text = trim($text, '-');

    return $text !== '' ? $text : 'page-' . substr(bin2hex(random_bytes(4)), 0, 8);
}

/**
 * Загружает изображение из $_FILES[$field], проверяет его формат и, если
 * высота превышает UPLOAD_MAX_HEIGHT, пропорционально сжимает его (п.7.7 ТЗ).
 * Возвращает относительный путь для сохранения в БД либо null, если файл не передан.
 * При ошибке бросает RuntimeException с сообщением для пользователя.
 */
function upload_and_resize_image(string $field, string $subdir): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$field];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Не удалось загрузить файл. Попробуйте ещё раз.');
    }

    if ($file['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('Файл слишком большой (максимум 8 МБ).');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Разрешены только изображения форматов JPG, PNG или WebP.');
    }

    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        throw new RuntimeException('Файл повреждён или не является изображением.');
    }

    [$width, $height] = $imageInfo;
    $ext = $allowed[$mime];

    switch ($mime) {
        case 'image/jpeg':
            $source = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            $source = imagecreatefrompng($file['tmp_name']);
            break;
        default:
            $source = imagecreatefromwebp($file['tmp_name']);
    }

    if ($source === false) {
        throw new RuntimeException('Не удалось обработать изображение.');
    }

    if ($height > UPLOAD_MAX_HEIGHT) {
        $newHeight = UPLOAD_MAX_HEIGHT;
        $newWidth = max(1, (int) round($width * ($newHeight / $height)));
        $resized = imagecreatetruecolor($newWidth, $newHeight);

        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);
        $source = $resized;
    }

    $targetDir = rtrim(UPLOAD_DIR, '/') . '/' . trim($subdir, '/');
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
        imagedestroy($source);
        throw new RuntimeException('Не удалось создать папку для загрузки.');
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $targetPath = $targetDir . '/' . $filename;

    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($source, $targetPath, 85);
            break;
        case 'image/png':
            imagepng($source, $targetPath, 6);
            break;
        default:
            imagewebp($source, $targetPath, 85);
    }

    imagedestroy($source);

    return trim($subdir, '/') . '/' . $filename;
}

function delete_uploaded_file(?string $relativePath): void
{
    if (!$relativePath) {
        return;
    }

    $fullPath = rtrim(UPLOAD_DIR, '/') . '/' . ltrim($relativePath, '/');
    if (is_file($fullPath)) {
        @unlink($fullPath);
    }
}
