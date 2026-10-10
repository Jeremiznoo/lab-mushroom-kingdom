<?php
require_once __DIR__ . '/auth.php';
$heroName = $_SESSION['hero_name'] ?? '';
$currentPage = $_GET['view'] ?? 'home';
$isAuthenticated = mkb_is_authenticated();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Mission Board') ?> · Mushroom Kingdom</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/theme.css">
  <link rel="stylesheet" href="/assets/css/overrides.css">
  <link rel="stylesheet" href="/assets/css/footer-layout.css">
  <link rel="stylesheet" href="/assets/css/icon-fixes.css">
  <link rel="stylesheet" href="/assets/css/evidence.css">
  <link rel="stylesheet" href="/assets/css/decoys.css">
  <link rel="stylesheet" href="/assets/css/auth-modern.css">
  <link rel="stylesheet" href="/assets/css/visual-fixes.css">
</head>
<body>
<div class="top-strip"><span class="live-dot"></span> Mission Board · réseau communautaire du Royaume Champignon <span class="top-status">● 2 418 Mario connectés</span></div>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="/index.php"><span class="brand-star"><img src="/img/mushroom.png" alt="Champignon" style="display:block;width:42px!important;height:42px!important;object-fit:contain;"></span><span><b>MUSHROOM</b><small>KINGDOM MISSION BOARD</small></span></a>
    <nav aria-label="Navigation principale">
      <a class="<?= str_contains($currentPage, 'home') ? 'active' : '' ?>" href="/index.php">Accueil</a>
      <a class="<?= str_contains($currentPage, 'forum') ? 'active' : '' ?>" href="/index.php?view=forum">Forum</a>
      <a class="<?= str_contains($currentPage, 'about') ? 'active' : '' ?>" href="/index.php?view=about">À propos</a>
    </nav>
    <form class="header-search" method="get" action="/index.php"><input type="hidden" name="view" value="forum"><span>⌕</span><input name="q" aria-label="Rechercher" placeholder="Rechercher dans le royaume…" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"><button aria-label="Lancer la recherche">↵</button></form>
    <?php if ($isAuthenticated): ?>
    <a class="hero-chip" href="/index.php?view=logout"><span class="mini-avatar avatar-mario"><img src="/img/Icones/mario.png" alt="Mario"></span><?= htmlspecialchars($_SESSION['username'] ?? 'Mario') ?> · Déconnexion</a>
    <?php else: ?>
    <a class="hero-chip" href="/index.php?view=login"><span class="mini-avatar avatar-mario"><img src="/img/Icones/mario.png" alt="Mario"></span>Login / Sign up</a>
    <?php endif; ?>
  </div>
</header>
<main class="container page-shell">
