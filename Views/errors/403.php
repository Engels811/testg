<section class="error-page error-403">
    <h1>403 – Zugriff verweigert</h1>
    <p>Du hast keine Berechtigung, diese Seite zu betreten.</p>

    <div class="error-actions">
        <a href="/" class="btn">Zur Startseite</a>

        <?php if (empty($_SESSION['user'])): ?>
            <a href="/login" class="btn secondary">Login</a>
        <?php endif; ?>
    </div>
</section>
