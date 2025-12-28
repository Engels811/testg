<?php
/** @var string|null $title */
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'E-Mail bereits bestätigt' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/auth-login-isolated.css">
</head>

<body class="auth-login-page">

<main class="auth-login-main">
    <div class="auth-login-container">

        <h1 class="auth-login-title">
            ✅ <span class="auth-title-text">Bereits bestätigt</span>
        </h1>

        <div class="auth-info-box">
            <p>Diese E-Mail-Adresse wurde bereits bestätigt.</p>
            <p style="margin-top:12px;color:#b5b5b5;">
                Du kannst dich jetzt anmelden oder eine neue Bestätigungs-Mail anfordern,
                falls du den Link erneut benötigst.
            </p>
        </div>

        <!-- ACTIONS -->
        <div style="margin-top:26px;text-align:center;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">

            <a href="/login"
               class="auth-submit-btn"
               style="text-decoration:none;">
                🔐 Zum Login
            </a>

            <form method="post" action="/email/resend-confirmation">
                <button type="submit"
                        class="auth-submit-btn secondary">
                    🔁 Bestätigungs-Mail erneut senden
                </button>
            </form>

        </div>

    </div>
</main>

</body>
</html>
