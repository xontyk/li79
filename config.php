<?php
/**
 * Общие настройки сайта.
 *
 * При переносе на школьный сервер отредактируйте значения по умолчанию ниже
 * или (предпочтительно) задайте переменные окружения с такими же именами
 * средствами хостинга — тогда этот файл менять не придётся.
 */

function env(string $key, string $default): string
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'licey79'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'Лицей-интернат №79');
define('SITE_CITY', 'г. Набережные Челны');
define('SITE_PHONE', '+7 (8552) 00-00-00');
define('SITE_EMAIL', 'info@licey79.ru');
define('SITE_ADDRESS', 'г. Набережные Челны, просп. Мира, д. 1');
define('SITE_TELEGRAM', 'https://t.me/licey79');

define('UPLOAD_MAX_HEIGHT', 720);
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', '/uploads');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', '0');
