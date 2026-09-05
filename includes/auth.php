<?php

require_once __DIR__ . '/functions.php';

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    return [
        'id' => $_SESSION['admin_id'],
        'full_name' => $_SESSION['admin_name'] ?? '',
        'email' => $_SESSION['admin_email'] ?? '',
    ];
}

function require_login(): void
{
    if (!current_admin()) {
        redirect('login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function check_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', is_string($token) ? $token : '')) {
        http_response_code(403);
        die('Проверка формы не пройдена (CSRF). Обновите страницу и попробуйте снова.');
    }
}
