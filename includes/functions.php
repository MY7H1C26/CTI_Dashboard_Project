<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

function flash($type, $message)
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function show_flash()
{
    if (empty($_SESSION['flash'])) {
        return;
    }

    foreach ($_SESSION['flash'] as $item) {
        echo '<div class="alert alert-' . e($item['type']) . ' alert-dismissible fade show" role="alert">';
        echo e($item['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }

    unset($_SESSION['flash']);
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in()
{
    return current_user() !== null;
}

function role_name()
{
    return $_SESSION['user']['role_name'] ?? '';
}

function is_admin()
{
    return role_name() === 'Admin' || role_name() === 'System Administrator';
}

function require_login()
{
    if (!is_logged_in()) {
        flash('warning', 'Please login first.');
        $base = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false ? '../' : '';
        redirect($base . 'login.php');
    }
}

function require_admin()
{
    require_login();
    if (!is_admin()) {
        flash('danger', 'Access denied. Admin permission is required.');
        redirect('../dashboard.php');
    }
}

function upload_service_file($file)
{
    if (empty($file['name'])) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed.');
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('Only JPG, PNG, GIF, and PDF files are allowed.');
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('File must be smaller than 2 MB.');
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = uniqid('upload_', true) . '.' . strtolower($extension);
    $target = __DIR__ . '/../assets/uploads/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded file.');
    }

    return $safeName;
}
