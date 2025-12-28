<section class="admin-wrapper">

    <div class="admin-container">

        <header class="admin-header">
            <h1>Galerie verwalten</h1>
            <p>Verwaltung der öffentlichen Galerie</p>
        </header>

        <div class="admin-toolbar">
            <a href="#" class="btn btn-primary">+ Bild hochladen</a>
        </div>

        <?php if (empty($images)): ?>
            <div class="admin-empty">
                <p>Keine Bilder vorhanden.</p>
            </div>
        <?php else: ?>
            <div class="gallery-grid">
                <?php foreach ($images as $img): ?>
                    <div class="gallery-item">
                        <img
                            src="/uploads/gallery/<?= htmlspecialchars($img['section'], ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($img['file'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($img['title'] ?? 'Ohne Titel', ENT_QUOTES, 'UTF-8') ?>"
                        >


                        <div class="gallery-meta">
                            <strong>
                                <?= htmlspecialchars($img['title'] ?? 'Ohne Titel', ENT_QUOTES, 'UTF-8') ?>
                            </strong>
                            <small>
                                <?= htmlspecialchars($img['section'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</section>
