<?php ob_start(); ?>

<h1>🔐 Zwei-Faktor-Authentifizierung</h1>

<p>Scanne den QR-Code in Google Authenticator oder gib den Code manuell ein.</p>

<p><strong>Secret:</strong> <?= htmlspecialchars($secret) ?></p>

<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($uri) ?>">

<form method="post" action="/dashboard/security/2fa/enable">
    <?= Security::csrfField() ?>
    <input type="text" name="code" placeholder="6-stelliger Code" required>
    <button class="btn-primary">2FA aktivieren</button>
</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
