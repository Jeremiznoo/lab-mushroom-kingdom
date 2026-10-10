<?php
session_start();
require __DIR__ . '/includes/auth.php';

if (($_GET['view'] ?? '') === 'logout') {
    mkb_logout();
    header('Location: index.php');
    exit;
}

$publicViews = [
    'home' => 'pages/home.php',
    'forum' => 'pages/forum.php',
    'thread' => 'pages/thread.php',
    'about' => 'pages/about.php',
    'login' => 'pages/login.php',
    'admin' => 'pages/admin.php',
];

$view = $_GET['view'] ?? 'home';
include($publicViews[$view] ?? $publicViews['home']);
