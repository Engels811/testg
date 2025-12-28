<?php
/** @var string $confirmLink */
/** @var string|null $username */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung bestätigen – Engels811 Network</title>
</head>

<body style="margin:0;padding:0;background:#050505;font-family:Arial,Helvetica,sans-serif;color:#ffffff;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:36px 0;">
<tr>
<td align="center">

<table width="100%" cellpadding="0" cellspacing="0"
       style="max-width:560px;background:#0b0b0b;border-radius:14px;overflow:hidden;box-shadow:0 0 60px rgba(196,0,0,0.35);">

    <!-- HEADER -->
    <tr>
        <td style="padding:28px 0 22px;background:#0b0b0b;text-align:center;border-bottom:1px solid #2b0000;">
            <img src="https://i.ibb.co/ns1czZv9/Brennender-Wolf-und-Flammen-Sym33bole-removebg-preview.png"
                 alt="Engels811 Network Logo" width="120" style="display:inline-block;border:0;">
        </td>
    </tr>

    <!-- GLOW LINE -->
    <tr>
        <td style="height:4px;background:linear-gradient(90deg,rgba(196,0,0,0),rgba(196,0,0,0.8),rgba(196,0,0,0));"></td>
    </tr>

    <!-- CONTENT -->
    <tr>
        <td style="padding:30px;line-height:1.7;">

            <h2 style="margin:0 0 16px;text-align:center;color:#c40000;letter-spacing:1px;">
                WILLKOMMEN BEI ENGELS811 NETWORK
            </h2>

            <p>
                <?php if (!empty($username)): ?>
                    Hallo <strong><?= htmlspecialchars($username) ?></strong>,
                <?php else: ?>
                    Hallo,
                <?php endif; ?>
            </p>

            <p style="color:#b5b5b5;">
                vielen Dank für deine Registrierung bei
                <strong>Engels811 Network</strong>! 🎉
            </p>

            <p style="color:#b5b5b5;">
                Bitte bestätige deine E-Mail-Adresse, um dein Konto vollständig zu aktivieren
                und Zugriff auf alle Features zu erhalten:
            </p>

            <ul style="color:#b5b5b5;padding-left:20px;">
                <li>Community-Forum & Support</li>
                <li>Exklusive Discord-Rollen</li>
            </ul>

            <p style="text-align:center;margin:36px 0;">
                <a href="<?= htmlspecialchars($confirmLink) ?>"
                   style="display:inline-block;padding:15px 38px;background:linear-gradient(180deg,#c40000,#5a0000);color:#ffffff;text-decoration:none;border-radius:10px;font-weight:bold;letter-spacing:1px;box-shadow:0 0 28px rgba(196,0,0,0.85);">
                    🔥 JETZT AKTIVIEREN
                </a>
            </p>

            <!-- TOKEN INFO -->
            <p style="font-size:12px;color:#9a9a9a;margin-top:18px;">
                🛡 Dieser Aktivierungslink ist aus Sicherheitsgründen nur
                <strong>24 Stunden gültig</strong>.
            </p>

            <p style="font-size:11px;word-break:break-all;color:#c40000;background:#1a0000;padding:10px;border-radius:6px;margin-top:12px;">
                <?= htmlspecialchars($confirmLink) ?>
            </p>

            <p style="font-size:12px;color:#7a7a7a;margin-top:22px;">
                Falls du dich nicht registriert hast, ignoriere diese E-Mail einfach.
                Deine Daten werden nach 24 Stunden automatisch gelöscht.
            </p>

        </td>
    </tr>

    <!-- FOOTER BANNER -->
    <tr>
        <td style="padding:0;background:#050505;">
            <img src="https://i.ibb.co/Y7zCgFFt/Chat-GPT-Image-27-Dez-2025-09-42-07.png"
                 alt="Engels811 Network Banner" width="100%" style="display:block;border:0;max-width:560px;">
        </td>
    </tr>

    <!-- FOOTER CONTENT -->
    <tr>
        <td style="padding:18px 22px;background:#070707;border-top:1px solid #2b0000;text-align:center;font-size:11px;color:#8a8a8a;">
            © <?= date('Y') ?> Engels811 Network · Automatische Systemnachricht<br>
            <a href="https://engels811-ttv.de/impressum" style="color:#c40000;text-decoration:none;">Impressum</a>
            &nbsp;·&nbsp;
            <a href="https://engels811-ttv.de/forum" style="color:#c40000;text-decoration:none;">Support</a>
            <br><br>
            <span style="color:#6f6f6f;">Bitte antworte nicht auf diese E-Mail.</span>
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>