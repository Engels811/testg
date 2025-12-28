<?php
/** @var string $confirmLink */
/** @var string|null $username */
?>
═══════════════════════════════════════════════════════
    ENGELS811 NETWORK – E-MAIL-ADRESSE BESTÄTIGEN
═══════════════════════════════════════════════════════

<?php if (!empty($username)): ?>
Hallo <?= htmlspecialchars($username) ?>,
<?php else: ?>
Hallo,
<?php endif; ?>

du hast deine E-Mail-Adresse bei Engels811 Network geändert.
Bitte bestätige deine neue E-Mail-Adresse, um die Änderung 
abzuschließen.

🔥 BESTÄTIGUNGSLINK:
<?= htmlspecialchars($confirmLink) ?>


🛡 WICHTIG:
Dieser Bestätigungslink ist aus Sicherheitsgründen nur 
24 Stunden gültig.

Wenn du diese E-Mail-Änderung nicht selbst durchgeführt hast,
ignoriere diese E-Mail bitte oder kontaktiere unseren Support.

═══════════════════════════════════════════════════════

© <?= date('Y') ?> Engels811 Network
Automatische Systemnachricht – Bitte nicht antworten

Impressum: https://engels811-ttv.de/impressum
Support:   https://engels811-ttv.de/forum

═══════════════════════════════════════════════════════