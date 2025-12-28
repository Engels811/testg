<?php
/**
 * Admin – Favicon verwalten
 * SYSTEMKRITISCHES BRANDING
 */
?>

<h1 class="danger-title">🖼️ Favicon verwalten</h1>
<p class="danger-sub">
    Systemkritisches Branding · Änderungen wirken sofort · Browser-Cache beachten
</p>

<link rel="stylesheet" href="/assets/css/admin-favicon.css">
<script src="/assets/js/admin-favicon-preview.js" defer></script>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert error">
        <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert success">
        <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<form
    action="/admin/settings/favicon/upload"
    method="post"
    enctype="multipart/form-data"
    class="favicon-form"
>

    <?= Security::csrfField() ?>

    <!-- DATEI AUSWÄHLEN -->
    <label class="upload-label" for="faviconInput">
        <span>Bild auswählen (PNG, JPG, WEBP · max. 1&nbsp;MB)</span>
        <input
            type="file"
            name="favicon"
            id="faviconInput"
            accept="image/*"
            required
        >
    </label>

    <!-- VORSCHAU -->
    <div class="favicon-preview-zone">

        <div class="favicon-main">
            <img
                id="faviconPreview"
                src="/favicon.ico"
                alt="Aktuelles Favicon – Live-Vorschau"
                onerror="this.src='/assets/img/favicon-placeholder.png';"
            >
        </div>

        <div class="favicon-sizes">
            <span>16×16</span>
            <span>32×32</span>
            <span>48×48</span>
            <span>64×64</span>
        </div>

    </div>

    <!-- HINWEIS -->
    <div class="danger-hint">
        ⚠️ Nach dem Upload kann es notwendig sein, den Browser-Cache zu leeren
        oder die Seite mit <kbd>Strg</kbd> + <kbd>F5</kbd> neu zu laden.
    </div>

    <!-- ABSENDEN -->
    <button class="btn-danger" aria-label="Favicon dauerhaft ersetzen">
        Favicon übernehmen
    </button>

</form>
