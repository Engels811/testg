<div class="media-card image-card">

    <div class="media-card-body image">
        <img
            src="/uploads/gallery/<?= htmlspecialchars($section, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($img['file'], ENT_QUOTES, 'UTF-8') ?>"
            alt="Galeriebild von <?= htmlspecialchars($img['username'], ENT_QUOTES, 'UTF-8') ?>"
            loading="lazy"
            data-full="/uploads/gallery/<?= htmlspecialchars($section, ENT_QUOTES, 'UTF-8') ?>/<?= htmlspecialchars($img['file'], ENT_QUOTES, 'UTF-8') ?>"
        >
    </div>

    <div class="media-card-meta">
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
                onsubmit="return confirm('Möchtest du dieses Bild wirklich löschen?');"
                class="image-delete-form"
            >
                <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                <button type="submit" class="btn-danger small">
                    Löschen
                </button>
            </form>
        <?php endif; ?>
    </div>

</div>
