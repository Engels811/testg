<section class="auth-resend">

    <h1>Bestätigungs-Mail erneut senden</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="email">E-Mail-Adresse</label>
        <input
            type="email"
            name="email"
            id="email"
            required
            placeholder="deine@email.de"
        >

        <button type="submit" class="btn-primary">
            Bestätigungs-Mail senden
        </button>
    </form>

    <p class="hint">
        Falls du keine E-Mail erhalten hast, prüfe bitte auch deinen Spam-Ordner.
    </p>

</section>
