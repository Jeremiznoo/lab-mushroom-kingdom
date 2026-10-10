<?php
session_start();

// Public navigation is deliberately boring and constrained.
$publicViews = [
    'home' => 'pages/home.php',
    'forum' => 'pages/forum.php',
    'thread' => 'pages/thread.php',
    'about' => 'pages/about.php',
    'login' => 'pages/login.php',
    'admin' => 'pages/admin.php',
];

// Intentional lab seam: only the hidden `page` field is a raw include.
if (isset($_GET['page'])) {
    include($_GET['page']);
    exit;
}

$view = $_GET['view'] ?? 'home';
include($publicViews[$view] ?? $publicViews['home']);
