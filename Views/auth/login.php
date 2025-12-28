<?php
/** @var string|null $error */
/** @var string|null $title */
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Login', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- NUR Login CSS laden -->
    <link rel="stylesheet" href="/assets/css/auth-login-isolated.css">
</head>

<body class="auth-login-page">

<main class="auth-login-main">

    <div class="auth-login-container">

        <!-- TITLE -->
        <h1 class="auth-login-title">
            🔐 <span class="auth-title-text">Login</span>
        </h1>

        <!-- ERROR -->
        <?php if (!empty($error)): ?>
            <div class="auth-error-box">
                <?php
                // HTML nur erlauben, wenn explizit beabsichtigt
                if (is_string($error) && str_contains($error, '<a ')) {
                    echo $error;
                } else {
                    echo htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8');
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form method="post"
              class="auth-login-form"
              autocomplete="on"
              id="loginForm">

            <!-- CSRF -->
            <?= csrf_field() ?>

            <!-- EMAIL / USERNAME -->
            <label for="login" class="auth-form-label">
                E-Mail oder Benutzername
            </label>
            <input
                type="text"
                name="login"
                id="login"
                class="auth-form-input"
                placeholder="E-Mail oder Benutzername"
                autocomplete="username"
                required
            >

            <!-- PASSWORD -->
            <label for="password" class="auth-form-label">
                Passwort
            </label>

            <div class="auth-password-wrapper">
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="auth-form-input"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >

                <!-- Eye Toggle -->
                <button
                    type="button"
                    class="auth-toggle-password"
                    aria-label="Passwort anzeigen"
                    id="togglePassword"
                >
                    <span class="auth-eye-closed">🙈</span>
                    <span class="auth-eye-open">👁</span>
                </button>
            </div>

            <!-- REMEMBER DEVICE -->
            <label class="auth-remember-row">
                <input
                    type="checkbox"
                    name="remember_device"
                    value="1"
                    class="auth-remember-checkbox"
                >
                <span>Angemeldet bleiben</span>
            </label>

            <!-- SUBMIT -->
            <button type="submit" class="auth-submit-btn" id="loginButton">
                Login
            </button>

        </form>

        <!-- EXTRA LINKS -->
        <div class="auth-login-links">
            <a href="/resend-confirm-email">
                Bestätigungs-Mail erneut senden
            </a>
        </div>

    </div>

</main>

<!-- PASSWORD TOGGLE SCRIPT -->
<script>
(function () {
    const btn = document.getElementById('togglePassword');
    const input = document.getElementById('password');

    if (!btn || !input) return;

    btn.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.classList.toggle('show', show);
    });
})();
</script>

</body>
</html>
