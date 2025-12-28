<?php
/** @var string $confirmLink */
/** @var string|null $username */
?>
═══════════════════════════════════════════════════════
    WILLKOMMEN BEI ENGELS811 NETWORK
═══════════════════════════════════════════════════════

<?php if (!empty($username)): ?>
Hallo <?= htmlspecialchars($username) ?>,
<?php else: ?>
Hallo,
<?php endif; ?>

vielen Dank für deine Registrierung bei Engels811 Network! 🎉

Bitte bestätige deine E-Mail-Adresse, um dein Konto 
vollständig zu aktivieren und Zugriff auf alle Features 
zu erhalten:

✓ Community-Forum & Support
✓ Exklusive Discord-Rollen

🔥 AKTIVIERUNGSLINK:
<?= htmlspecialchars($confirmLink) ?>


🛡 WICHTIG:
Dieser Aktivierungslink ist aus Sicherheitsgründen nur 
24 Stunden gültig.

Falls du dich nicht registriert hast, ignoriere diese 
E-Mail einfach. Deine Daten werden nach 24 Stunden 
automatisch gelöscht.

═══════════════════════════════════════════════════════

© <?= date('Y') ?> Engels811 Network
Automatische Systemnachricht – Bitte nicht antworten

Impressum: https://engels811-ttv.de/impressum
Support:   https://engels811-ttv.de/forum

═══════════════════════════════════════════════════════