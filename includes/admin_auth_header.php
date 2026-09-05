<?php
/** Простая центрированная обёртка для страниц входа/регистрации/ожидания. */
?><!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'Админ-панель — ' . SITE_NAME) ?></title>
<link rel="stylesheet" href="/css/style.css">
<link rel="stylesheet" href="/css/admin.css">
</head>
<body class="auth-body">
<div class="auth-wrap">
