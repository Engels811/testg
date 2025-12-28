<section class="page-wrapper">

    <div class="page-head center">
        <span class="page-icon">🎬</span>
        <h1 class="page-title accent">Videos</h1>
        <p class="page-subtitle">
            Highlights & Community-Videos
        </p>
    </div>

    <!-- ===============================
         VIDEO / STREAM TABS
    =============================== -->
    <div class="page-tabs center">
        <a href="/videos" class="tab active">
            🎬 Videos
        </a>
        <a href="/videos/streams" class="tab">
            🔴 Streams
        </a>
    </div>

    <!-- ===============================
         KATEGORIE-FILTER
    =============================== -->
    <?php if (!empty($categories)): ?>
        <div class="category-filter">
            <a href="/videos" class="<?= empty($activeCategory) ? 'active' : '' ?>">
                Alle
            </a>

            <?php foreach ($categories as $cat): ?>
                <a
                    href="/videos?cat=<?= htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8') ?>"
                    class="<?= ($activeCategory === $cat['slug']) ? 'active' : '' ?>"
                >
                    <?= htmlspecialchars($cat['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ===============================
         VIDEO UPLOAD (NUR MANUELL)
    =============================== -->
    <?php if (!empty($_SESSION['user'])): ?>
        <form action="/videos/upload" method="post" class="upload-box">

            <input
                type="text"
                name="title"
                placeholder="Video-Titel"
                required
            >

            <input
                type="url"
                name="url"
                placeholder="YouTube / Embed URL"
                required
            >

            <?php if (!empty($categories)): ?>
                <select name="category_id">
                    <option value="">Kategorie wählen</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>">
                            <?= htmlspecialchars($cat['title'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button type="submit" class="btn-accent">
                Video hinzufügen
            </button>

            <p class="form-hint">
                Twitch-Streams werden automatisch im Bereich
                <strong>Streams</strong> angezeigt.
            </p>
        </form>
    <?php endif; ?>

    <!-- ===============================
         VIDEO LISTE (NUR MANUELL)
    =============================== -->
    <?php if (!empty($videos)): ?>
        <div class="media-grid">

            <?php foreach ($videos as $video): ?>
                <div class="media-card">

                    <div class="media-card-body">
                        <h3><?= htmlspecialchars($video['title'], ENT_QUOTES, 'UTF-8') ?></h3>

                        <div class="video-embed">
                            <iframe
                                src="<?= htmlspecialchars($video['url'], ENT_QUOTES, 'UTF-8') ?>"
                                frameborder="0"
                                allowfullscreen
                                loading="lazy"
                            ></iframe>
                        </div>
                    </div>

                    <?php if (
                        !empty($_SESSION['user']) &&
                        (int)$_SESSION['user']['id'] === (int)$video['user_id']
                    ): ?>
                        <div class="media-card-footer">
                            <form
                                action="/videos/delete"
                                method="post"
                                onsubmit="return confirm('Möchtest du dieses Video wirklich löschen?');"
                                class="video-delete-form"
                            >
                                <input type="hidden" name="id" value="<?= (int)$video['id'] ?>">
                                <button type="submit" class="btn-danger small">
                                    Löschen
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </div>
    <?php else: ?>
        <p class="page-subtitle center">
            Noch keine Videos vorhanden.
        </p>
    <?php endif; ?>

</section>
