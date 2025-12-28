<?php
/** @var string|null $username */
/** @var string $siteUrl */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Kontaktanfrage erhalten</title>
</head>

<body style="margin:0;padding:0;background:#050505;font-family:Arial,Helvetica,sans-serif;color:#ffffff;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:36px 0;">
<tr><td align="center">

<table width="100%" cellpadding="0" cellspacing="0"
       style="max-width:560px;background:#0b0b0b;border-radius:14px;
              overflow:hidden;box-shadow:0 0 60px rgba(196,0,0,0.35);">

    <!-- HEADER / LOGO -->
    <tr>
        <td style="padding:28px 0 22px;text-align:center;border-bottom:1px solid #2b0000;">
            <img src="https://i.ibb.co/ns1czZv9/Brennender-Wolf-und-Flammen-Sym33bole-removebg-preview.png"
                 alt="Engels811 Network" width="120"
                 style="display:block;margin:0 auto;border:0;">
        </td>
    </tr>

    <!-- GLOW LINE -->
    <tr>
        <td style="height:4px;background:linear-gradient(90deg,rgba(196,0,0,0),rgba(196,0,0,0.85),rgba(196,0,0,0));"></td>
    </tr>

    <!-- CONTENT -->
    <tr>
        <td style="padding:30px;line-height:1.7;text-align:center;">
            <h2 style="margin:0 0 16px;color:#c40000;letter-spacing:1px;">
                KONTAKTANFRAGE ERHALTEN
            </h2>

            <p style="margin:0 0 10px;">
                <?= $username ? 'Hallo <strong>'.htmlspecialchars($username).'</strong>,' : 'Hallo,' ?>
            </p>

            <p style="color:#b5b5b5;margin:0 0 10px;">
                vielen Dank für deine Nachricht über unser Kontaktformular.
            </p>

            <p style="color:#b5b5b5;margin:0;">
                Unser Team meldet sich so schnell wie möglich bei dir.
            </p>

            <p style="margin-top:18px;font-size:12px;color:#9a9a9a;">
                ℹ️ Diese E-Mail wurde automatisch erstellt. Bitte nicht antworten.
            </p>
        </td>
    </tr>

    <!-- FOOTER / BANNER -->
    <tr>
        <td style="background:#050505;">
            <img src="https://i.ibb.co/Y7zCgFFt/Chat-GPT-Image-27-Dez-2025-09-42-07.png"
                 alt="Engels811 Banner" width="100%"
                 style="display:block;border:0;max-width:560px;">
        </td>
    </tr>

    <!-- FOOTER LINKS -->
    <tr>
        <td style="padding:18px 22px;background:#070707;border-top:1px solid #2b0000;
                   text-align:center;font-size:11px;color:#8a8a8a;">
            © <?= date('Y') ?> Engels811 Network · Systemnachricht<br>
            <a href="<?= htmlspecialchars($siteUrl) ?>/impressum" style="color:#c40000;text-decoration:none;">Impressum</a>
            &nbsp;·&nbsp;
            <a href="<?= htmlspecialchars($siteUrl) ?>/kontakt" style="color:#c40000;text-decoration:none;">Kontakt</a>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
