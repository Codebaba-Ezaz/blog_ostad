<?php

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function current_user_id(): ?int
{
    return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
}

function is_admin(): bool
{
    return isset($_SESSION['user']['user_type']) && $_SESSION['user']['user_type'] === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function flash_set(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $message;
}

function save_image(array $file): ?string
{
    if (!isset($file['name']) || $file['name'] === '' || !isset($file['tmp_name']) || $file['tmp_name'] === '') {
        return null;
    }

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $originalName = (string) $file['name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt, true)) {
        return null;
    }

    $newName = uniqid('blog_', true) . '.' . $ext;
    $target = __DIR__ . '/../uploads/' . $newName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return null;
    }

    return $newName;
}
