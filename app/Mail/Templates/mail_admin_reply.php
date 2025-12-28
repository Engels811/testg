<?php
/** @var string|null $username */
/** @var string $siteUrl */
/** @var string $adminMessage */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nachricht von Engels811 Network</title>
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
                 alt="Engels811 TV Logo" width="120" style="display:inline-block;border:0;">
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
                NACHRICHT VON Engels811 Network
            </h2>

            <p>
                <?php if (!empty($username)): ?>
                    Hallo <strong><?= htmlspecialchars($username) ?></strong>,
                <?php else: ?>
                    Hallo,
                <?php endif; ?>
            </p>

            <p style="color:#b5b5b5;">
                <?= nl2br(htmlspecialchars($adminMessage)) ?>
            </p>

            <p style="margin-top:30px;font-size:13px;color:#9a9a9a;">
                Mit freundlichen Grüßen<br>
                <strong>Engels811 Network – Streaming &amp; Gaming</strong>
            </p>

            <p style="margin-top:18px;font-size:12px;color:#7a7a7a;">
                ℹ️ Diese Nachricht wurde manuell durch unser Team versendet.
                Bitte antworte nicht direkt auf diese E-Mail.
            </p>

        </td>
    </tr>

    <!-- FOOTER BANNER -->
    <tr>
        <td style="padding:0;background:#050505;">
            <img src="https://i.ibb.co/Y7zCgFFt/Chat-GPT-Image-27-Dez-2025-09-42-07.png"
                 alt="Engels811 TV Banner" width="100%" style="display:block;border:0;max-width:560px;">
        </td>
    </tr>

    <!-- FOOTER CONTENT -->
    <tr>
        <td style="padding:18px 22px;background:#070707;border-top:1px solid #2b0000;text-align:center;font-size:11px;color:#8a8a8a;">
            © <?= date('Y') ?> Engels811 Network · Systemnachricht<br>
            <a href="<?= htmlspecialchars($siteUrl) ?>/impressum" style="color:#c40000;text-decoration:none;">Impressum</a>
            &nbsp;·&nbsp;
            <a href="<?= htmlspecialchars($siteUrl) ?>/kontakt" style="color:#c40000;text-decoration:none;">Kontakt</a>
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
