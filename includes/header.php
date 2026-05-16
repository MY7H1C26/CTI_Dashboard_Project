<?php
require_once __DIR__ . '/functions.php';
$base = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false ? '../' : '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'CTI Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<?php if (is_logged_in()): ?>
<div class="app-shell">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
        <div class="topbar">
            <div>
                <h1><?= e($pageTitle ?? 'Dashboard') ?></h1>
                <p><?= e($pageSubtitle ?? 'Cyber Threat Intelligence operations') ?></p>
            </div>
            <div class="user-chip"><?= e(current_user()['name']) ?> | <?= e(role_name()) ?></div>
        </div>
        <?php show_flash(); ?>
<?php else: ?>
<main class="auth-page">
    <?php show_flash(); ?>
<?php endif; ?>
