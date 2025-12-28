<?php
/** @var string $resetLink */
/** @var string|null $username */
?>
═══════════════════════════════════════════════════════
    PASSWORT ZURÜCKSETZEN – ENGELS811 NETWORK
═══════════════════════════════════════════════════════

<?php if (!empty($username)): ?>
Hallo <?= htmlspecialchars($username) ?>,
<?php else: ?>
Hallo,
<?php endif; ?>

du hast eine Anfrage zum Zurücksetzen deines Passworts 
bei Engels811 Network gestellt.

Klicke auf den folgenden Link, um ein neues Passwort 
festzulegen:

🔐 RESET-LINK:
<?= htmlspecialchars($resetLink) ?>


🛡 WICHTIG:
Dieser Reset-Link ist aus Sicherheitsgründen nur 
1 Stunde gültig.

⚠️ Falls du keine Passwort-Zurücksetzung angefordert hast,
ignoriere diese E-Mail. Dein Passwort bleibt unverändert.

═══════════════════════════════════════════════════════

© <?= date('Y') ?> Engels811 Network
Automatische Systemnachricht – Bitte nicht antworten

Impressum: https://engels811-ttv.de/impressum
Support:   https://engels811-ttv.de/forum

═══════════════════════════════════════════════════════