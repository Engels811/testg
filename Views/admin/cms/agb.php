<section class="section">
    <div class="container">

        <h1 class="section-title">📄 AGB bearbeiten</h1>

        <form method="post" action="/admin/cms/save">
            <?= Security::csrfField() ?>

            <label>Version</label>
            <input type="text" name="version"
                   value="<?= htmlspecialchars($agb['version'] ?? '1.0') ?>">

            <label>AGB-Text</label>
            <textarea name="content" rows="20"
                      class="editor"><?= htmlspecialchars($agb['content'] ?? '') ?></textarea>

            <button class="btn-primary">Speichern</button>
        </form>

    </div>
</section>
