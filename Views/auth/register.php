<section class="section">
    <div class="container" style="max-width: 420px;">

        <h1 class="section-title">📝 <span>Registrieren</span></h1>

        <?php if (!empty($error)): ?>
            <div class="error-box">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-box">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="card">

            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>

            <label for="email">E-Mail</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Passwort</label>
            <input type="password" name="password" id="password" required>

            <!-- ✅ AGB CHECKBOX -->
            <label class="checkbox" style="margin-top:12px;">
                <input type="checkbox" name="agb" required>
                Ich akzeptiere die
                <a href="/agb" target="_blank">AGB</a>
                und habe die
                <a href="/datenschutz" target="_blank">Datenschutzerklärung</a>
                gelesen.
            </label>

            <button class="btn btn-primary" style="width:100%;margin-top:16px;">
                Registrieren
            </button>

        </form>

        <div class="hint-box" style="margin-top:16px;text-align:center;">
            <p>
                Nach der Registrierung erhältst du eine E-Mail zur Bestätigung
                deines Kontos.
            </p>

            <p style="margin-top:8px;">
                Keine E-Mail erhalten?
                <a href="/resend-confirm-email">Bestätigungs-Mail erneut senden</a>
            </p>
        </div>

        <p style="margin-top:20px;text-align:center;">
            Bereits registriert?
            <a href="/login">Jetzt einloggen</a>
        </p>

    </div>
</section>
