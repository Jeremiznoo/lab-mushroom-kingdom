<?php
$title = 'Login / Sign up';
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$mode = $_POST['mode'] ?? 'login';
$notice = '';
$error = '';

if ($submitted && $mode === 'login') {
    $username = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    if ($username !== '' && mkb_attempt_login($username, $password)) {
        header('Location: index.php?view=forum');
        exit;
    }
    $error = 'Identifiants invalides.';
} elseif ($submitted && $mode === 'signup') {
    $error = 'Les inscriptions sont fermées pour le moment. Contacte un Koopaling si tu penses avoir un accès légitime.';
} elseif ($submitted && $mode === 'reset') {
    $notice = 'Si cette adresse existe, un lien de récupération vient d’être envoyé.';
}

include __DIR__ . '/../includes/header.php';
?>
<section class="auth-page"><div class="auth-art"><span class="eyebrow">★ MISSION BOARD ACCESS</span><h1>Prêt pour<br><em>la prochaine quête ?</em></h1><p>Rejoins la communauté des Mario du Royaume Champignon.</p><div class="auth-art-stars">✦　★　✧</div><img src="img/Icones/mario.png" alt="Mario" class="auth-mario"></div><div class="auth-card"><div class="auth-tabs"><button class="auth-tab active" data-auth-tab="login">Login</button><button class="auth-tab" data-auth-tab="signup">Sign up</button></div><?php if ($notice): ?><div class="auth-notice">✓ <?= htmlspecialchars($notice) ?></div><?php endif; ?><?php if ($error): ?><div class="auth-notice auth-error">✕ <?= htmlspecialchars($error) ?></div><?php endif; ?><form class="auth-form auth-panel active" data-auth-panel="login" method="post"><span class="eyebrow red">BON RETOUR, MARIO</span><h2>Se connecter</h2><p>Retrouve tes quêtes et tes discussions.</p><label>Email ou identifiant<input type="text" name="email" placeholder="mario@kingdom.local" required></label><label>Mot de passe<input type="password" name="password" placeholder="••••••••" required></label><div class="auth-row"><label class="check"><input type="checkbox" name="remember"> Se souvenir de moi</label><button type="button" class="link-button" data-reset-open>Mot de passe oublié ?</button></div><input type="hidden" name="mode" value="login"><button class="button button-red auth-submit">Login <span>→</span></button><small class="auth-foot">Pas encore de compte ? <button type="button" class="link-button" data-auth-switch="signup">Créer un compte</button></small></form><form class="auth-form auth-panel" data-auth-panel="signup" method="post"><span class="eyebrow red">NOUVEAU MARIO</span><h2>Créer un compte</h2><p>Inscris-toi pour suivre les missions du royaume.</p><label>Nom de Mario<input type="text" name="hero" placeholder="Mario du Royaume" required></label><label>Email<input type="email" name="email" placeholder="mario@kingdom.local" required></label><label>Mot de passe<input type="password" name="password" placeholder="Choisis un mot de passe" required></label><input type="hidden" name="mode" value="signup"><button class="button button-red auth-submit">Sign up <span>→</span></button><small class="auth-foot">Déjà membre ? <button type="button" class="link-button" data-auth-switch="login">Se connecter</button></small></form><form class="auth-form auth-panel reset-panel" data-auth-panel="reset" method="post"><span class="eyebrow red">RÉCUPÉRATION</span><h2>Retrouver l’accès</h2><p>Entre ton email et nous préparerons un lien magique.</p><label>Email<input type="email" name="email" placeholder="mario@kingdom.local" required></label><input type="hidden" name="mode" value="reset"><button class="button button-red auth-submit">Envoyer le lien <span>→</span></button><small class="auth-foot"><button type="button" class="link-button" data-auth-switch="login">Retour au login</button></small></form></div></section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
