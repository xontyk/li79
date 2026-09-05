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

/* =========================================================================
 * Конструктор страниц из блоков (замена одного большого поля с HTML).
 * Содержимое страницы (pages.content) хранится как JSON-массив блоков,
 * например: [{"type":"heading","text":"...","level":2}, {"type":"image",...}].
 * Если в content лежит НЕ валидный JSON-массив — это старый формат (просто
 * HTML-строка из предыдущей версии сайта), и он выводится как есть, без
 * изменений, чтобы ничего не потерять при обновлении сайта.
 * ========================================================================= */

/** true, если массив — обычный список (ключи 0,1,2,...), а не ассоциативный. */
function is_list_array(array $arr): bool
{
    return array_keys($arr) === range(0, count($arr) - 1);
}

/**
 * Разбирает content страницы в массив блоков для конструктора в админке.
 * Старый HTML (не JSON-список) оборачивается в один блок типа legacy_html,
 * чтобы администратор видел и мог отредактировать существующий текст.
 */
function decode_page_blocks(?string $content): array
{
    $content = $content ?? '';

    if (trim($content) === '') {
        return [];
    }

    $decoded = json_decode($content, true);

    if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE && is_list_array($decoded)) {
        return $decoded;
    }

    return [['type' => 'legacy_html', 'html' => $content]];
}

/**
 * Проверяет и очищает блоки, присланные из формы редактора, перед сохранением в БД.
 * Возвращает JSON-строку для колонки pages.content.
 */
function sanitize_page_blocks(array $blocks): string
{
    $clean = [];

    foreach ($blocks as $block) {
        if (!is_array($block) || empty($block['type'])) {
            continue;
        }

        switch ($block['type']) {
            case 'heading':
                $clean[] = [
                    'type' => 'heading',
                    'level' => ((int) ($block['level'] ?? 2)) === 3 ? 3 : 2,
                    'text' => trim((string) ($block['text'] ?? '')),
                ];
                break;

            case 'paragraph':
                $html = (string) ($block['html'] ?? '');
                if (trim(strip_tags($html)) === '' && strpos($html, '<img') === false) {
                    break;
                }
                $clean[] = ['type' => 'paragraph', 'html' => $html];
                break;

            case 'image':
                $url = trim((string) ($block['url'] ?? ''));
                if ($url === '') {
                    break;
                }
                $clean[] = [
                    'type' => 'image',
                    'url' => $url,
                    'alt' => trim((string) ($block['alt'] ?? '')),
                    'caption' => trim((string) ($block['caption'] ?? '')),
                ];
                break;

            case 'button':
                $text = trim((string) ($block['text'] ?? ''));
                $url = trim((string) ($block['url'] ?? ''));
                if ($text === '' || $url === '' || stripos($url, 'javascript:') === 0) {
                    break;
                }
                $clean[] = [
                    'type' => 'button',
                    'text' => $text,
                    'url' => $url,
                    'style' => ($block['style'] ?? '') === 'outline' ? 'outline' : 'primary',
                ];
                break;

            case 'quote':
                $text = trim((string) ($block['text'] ?? ''));
                if ($text === '') {
                    break;
                }
                $clean[] = [
                    'type' => 'quote',
                    'text' => $text,
                    'author' => trim((string) ($block['author'] ?? '')),
                ];
                break;

            case 'list':
                $items = [];
                foreach ((array) ($block['items'] ?? []) as $item) {
                    $item = trim((string) $item);
                    if ($item !== '') {
                        $items[] = $item;
                    }
                }
                if (!$items) {
                    break;
                }
                $clean[] = [
                    'type' => 'list',
                    'style' => ($block['style'] ?? '') === 'numbered' ? 'numbered' : 'bullet',
                    'items' => $items,
                ];
                break;

            case 'cover':
                $imageUrl = trim((string) ($block['imageUrl'] ?? ''));
                $heading = trim((string) ($block['heading'] ?? ''));
                if ($imageUrl === '' && $heading === '') {
                    break;
                }
                $buttonText = trim((string) ($block['buttonText'] ?? ''));
                $buttonUrl = trim((string) ($block['buttonUrl'] ?? ''));
                if (stripos($buttonUrl, 'javascript:') === 0) {
                    $buttonUrl = '';
                }
                $clean[] = [
                    'type' => 'cover',
                    'imageUrl' => $imageUrl,
                    'overlay' => in_array($block['overlay'] ?? '', ['dark', 'light', 'none'], true) ? $block['overlay'] : 'dark',
                    'heading' => $heading,
                    'subtext' => trim((string) ($block['subtext'] ?? '')),
                    'buttonText' => ($buttonText !== '' && $buttonUrl !== '') ? $buttonText : '',
                    'buttonUrl' => ($buttonText !== '' && $buttonUrl !== '') ? $buttonUrl : '',
                ];
                break;

            case 'legacy_html':
                $html = (string) ($block['html'] ?? '');
                if (trim($html) === '') {
                    break;
                }
                $clean[] = ['type' => 'legacy_html', 'html' => $html];
                break;
        }
    }

    return json_encode($clean, JSON_UNESCAPED_UNICODE);
}

/** Рендерит блоки страницы в готовый HTML для публичного сайта. */
function render_page_blocks(?string $content): string
{
    $content = $content ?? '';

    if (trim($content) === '') {
        return '';
    }

    $decoded = json_decode($content, true);

    if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE || !is_list_array($decoded)) {
        // Старый формат — контент это уже готовый HTML.
        return $content;
    }

    $html = '';

    foreach ($decoded as $block) {
        if (!is_array($block) || empty($block['type'])) {
            continue;
        }

        switch ($block['type']) {
            case 'heading':
                $level = ((int) ($block['level'] ?? 2)) === 3 ? 3 : 2;
                $text = e($block['text'] ?? '');
                if ($text === '') {
                    break;
                }
                $html .= "<h{$level} class=\"block-heading\">{$text}</h{$level}>";
                break;

            case 'paragraph':
                $blockHtml = $block['html'] ?? '';
                if (trim((string) $blockHtml) === '') {
                    break;
                }
                $html .= '<div class="block-paragraph">' . $blockHtml . '</div>';
                break;

            case 'image':
                $url = $block['url'] ?? '';
                if ($url === '') {
                    break;
                }
                $alt = e($block['alt'] ?? '');
                $html .= '<figure class="block-image"><img src="' . e($url) . '" alt="' . $alt . '" loading="lazy">';
                if (!empty($block['caption'])) {
                    $html .= '<figcaption>' . e($block['caption']) . '</figcaption>';
                }
                $html .= '</figure>';
                break;

            case 'button':
                $text = e($block['text'] ?? '');
                $url = $block['url'] ?? '#';
                if ($text === '' || stripos(trim((string) $url), 'javascript:') === 0) {
                    break;
                }
                $style = ($block['style'] ?? '') === 'outline' ? 'btn-outline' : 'btn-primary';
                $html .= '<p class="block-button"><a class="btn ' . $style . '" href="' . e($url) . '">' . $text . '</a></p>';
                break;

            case 'quote':
                $text = e($block['text'] ?? '');
                if ($text === '') {
                    break;
                }
                $html .= '<blockquote class="block-quote"><p>' . $text . '</p>';
                if (!empty($block['author'])) {
                    $html .= '<cite>' . e($block['author']) . '</cite>';
                }
                $html .= '</blockquote>';
                break;

            case 'list':
                $items = is_array($block['items'] ?? null) ? $block['items'] : [];
                if (!$items) {
                    break;
                }
                $tag = ($block['style'] ?? '') === 'numbered' ? 'ol' : 'ul';
                $html .= "<{$tag} class=\"block-list\">";
                foreach ($items as $item) {
                    $html .= '<li>' . e((string) $item) . '</li>';
                }
                $html .= "</{$tag}>";
                break;

            case 'cover':
                $heading = e($block['heading'] ?? '');
                $subtext = e($block['subtext'] ?? '');
                $imageUrl = $block['imageUrl'] ?? '';
                $overlay = in_array($block['overlay'] ?? '', ['dark', 'light', 'none'], true) ? $block['overlay'] : 'dark';
                $bgStyle = $imageUrl !== '' ? ' style="background-image:url(\'' . e($imageUrl) . '\')"' : '';
                $html .= '<div class="block-cover block-cover-overlay-' . $overlay . '"' . $bgStyle . '>';
                $html .= '<div class="block-cover-inner">';
                if ($heading !== '') {
                    $html .= '<h2>' . $heading . '</h2>';
                }
                if ($subtext !== '') {
                    $html .= '<p>' . $subtext . '</p>';
                }
                if (!empty($block['buttonText']) && !empty($block['buttonUrl'])) {
                    $html .= '<a class="btn btn-primary" href="' . e($block['buttonUrl']) . '">' . e($block['buttonText']) . '</a>';
                }
                $html .= '</div></div>';
                break;

            case 'legacy_html':
                $html .= $block['html'] ?? '';
                break;
        }
    }

    return $html;
}
