<?php
/**
 * Эндпоинт для загрузки изображений прямо из редактора страницы (Quill).
 * Принимает файл через POST (multipart/form-data), сжимает его до 720p
 * теми же правилами, что и фото учителей, и возвращает JSON со ссылкой.
 */

require_once __DIR__ . '/../includes/auth.php';
require_login();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не поддерживается.']);
    exit;
}

check_csrf();

try {
    $path = upload_and_resize_image('file', 'pages');

    if ($path === null) {
        throw new RuntimeException('Файл не передан.');
    }

    echo json_encode(['url' => UPLOAD_URL . '/' . $path]);
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
