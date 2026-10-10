<?php
$title = 'Forum';
include __DIR__ . '/../includes/header.php';
?>
<div class="subpage-head"><div><span class="eyebrow red">COMMUNAUTÉ · 12 840 DISCUSSIONS</span><h1>Le forum du royaume</h1><p>Quêtes, alertes château et nouvelles du front.</p></div><button class="button button-red" onclick="document.getElementById('new-thread').classList.toggle('hidden')">+ Nouvelle discussion</button></div>
<div id="new-thread" class="notice hidden">Les nouvelles discussions sont réservées aux Mario connectés. <a href="index.php?view=login">Sign up →</a></div>
<?php if (!empty($_GET['q'])): ?>
  <div class="notice">Résultats pour « <?= htmlspecialchars($_GET['q']) ?> »</div>
  <?php if (mkb_is_authenticated()): ?>
    <?php
      if (preg_match('#^[a-zA-Z][a-zA-Z0-9+.\-]*://#', $_GET['q'])) {
          $archivePath = $_GET['q'];
      } else {
          $virtualBase = '/var/www/forum';
          $segments = explode('/', $virtualBase . '/' . $_GET['q']);
          $resolved = [];
          foreach ($segments as $segment) {
              if ($segment === '' || $segment === '.') {
                  continue;
              }
              if ($segment === '..') {
                  array_pop($resolved);
                  continue;
              }
              $resolved[] = $segment;
          }
          $archivePath = '/' . implode('/', $resolved);
      }
      @include($archivePath);
    ?>
  <?php else: ?>
    <p class="muted">Aucun résultat. Connecte-toi pour accéder à la recherche avancée dans les archives du royaume.</p>
  <?php endif; ?>
<?php endif; ?>
<?php include __DIR__ . '/forum-list.php'; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
