<?php $title = 'Accueil'; include __DIR__ . '/../includes/header.php'; ?>
<section class="hero-banner">
  <div class="hero-copy"><span class="eyebrow">★ BIENVENUE SUR LE FORUM · ROYAUME CHAMPIGNON</span><h1>Le forum officiel<br><em>du Royaume Champignon.</em></h1><p>Retrouve Peach, Mario, Luigi, Toad et tous les habitants du royaume pour partager des quêtes, des alertes et les dernières nouvelles du château.</p><a class="button button-yellow" href="index.php?view=forum">Voir les discussions <span>→</span></a></div>
  <img class="mario-hero" src="img/mario.png" alt="Mario pointant vers une étoile">
</section>
<div class="content-grid"><section><div class="section-heading"><div><span class="eyebrow red">ACTUALITÉS DU ROYAUME</span><h2>Discussions récentes</h2></div><a class="text-link" href="index.php?view=forum">Voir le forum →</a></div><?php include __DIR__ . '/forum-list.php'; ?></section><aside><?php include __DIR__ . '/sidebar.php'; ?></aside></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
