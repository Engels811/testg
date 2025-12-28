<?php
// Galerie Sektion – z.B. Community / Artwork / BTS
?>
<section class="page-wrapper">

    <div class="page-head center">
        <span class="page-icon">📁</span>

        <h1 class="page-title accent">
            <?= htmlspecialchars($sectionTitle, ENT_QUOTES, 'UTF-8') ?>
        </h1>

        <p class="page-subtitle">
            <?= htmlspecialchars($sectionSubtitle, ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <?php if (!empty($_SESSION['user'])): ?>
        <form
            action="/galerie/upload"
            method="post"
            enctype="multipart/form-data"
            class="upload-box"
        >
            <input
                type="hidden"
                name="section"
                value="<?= htmlspecialchars($section, ENT_QUOTES, 'UTF-8') ?>"
            >

            <input type="file" name="image" required>

            <button type="submit" class="btn-accent">
                Bild hochladen
            </button>
        </form>
    <?php endif; ?>

    <?php if (!empty($images)): ?>
        <div class="media-grid images">

            <?php foreach ($images as $img): ?>
                <div class="media-card image-card">

                    <img
                        src="/uploads/gallery/<?= htmlspecialchars($section, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($img['file'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="Galerie Bild"
                        loading="lazy"

                        class="lightbox-trigger"

                        data-full="/uploads/gallery/<?= htmlspecialchars($section, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($img['file'], ENT_QUOTES, 'UTF-8') ?>"
                        data-id="<?= (int)$img['id'] ?>"
                    >

                    <div class="media-card-footer">

                        <span>
                            von <?= htmlspecialchars($img['username'], ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <?php if (
                            !empty($_SESSION['user']) &&
                            (int)$_SESSION['user']['id'] === (int)$img['user_id']
                        ): ?>
                            <form
                                action="/galerie/delete"
                                method="post"
                                class="image-delete-form"
                                onsubmit="return confirm('Möchtest du dieses Bild wirklich löschen?');"
                            >
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$img['id'] ?>"
                                >

                                <button type="submit" class="btn-danger small">
                                    Löschen
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>

                </div>
            <?php endforeach; ?>

        </div>
    <?php else: ?>
        <p class="page-subtitle center">
            Noch keine Bilder vorhanden.
        </p>
    <?php endif; ?>

</section>
