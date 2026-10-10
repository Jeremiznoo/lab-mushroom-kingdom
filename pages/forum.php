<?php $title = 'Forum'; include __DIR__ . '/../includes/header.php'; ?>
<div class="subpage-head"><div><span class="eyebrow red">COMMUNAUTÉ · 12 840 DISCUSSIONS</span><h1>Le forum du royaume</h1><p>Quêtes, alertes château et nouvelles du front.</p></div><button class="button button-red" onclick="document.getElementById('new-thread').classList.toggle('hidden')">+ Nouvelle discussion</button></div>
<div id="new-thread" class="notice hidden">Les nouvelles discussions sont réservées aux héros connectés. <a href="index.php?view=login">Sign up →</a></div>
<?php include __DIR__ . '/forum-list.php'; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
