<section class="page-wrapper">

    <div class="page-head center">
        <span class="page-icon">🎵</span>
        <h1 class="page-title accent">Playlisten</h1>
        <p class="page-subtitle">
            Spotify & YouTube Music Playlists von Engels811
        </p>
    </div>

    <?php if (!empty($playlists) && is_array($playlists)): ?>

        <!-- ===============================
             SPOTIFY
        =============================== -->
        <section class="content-block">
            <h2 class="section-title">🎧 Spotify <span>Playlists</span></h2>

            <div class="media-grid">
                <?php
                $hasSpotify = false;
                foreach ($playlists as $pl):
                    if (($pl['type'] ?? '') !== 'spotify') continue;
                    $hasSpotify = true;
                ?>
                    <div class="media-card">
                        <iframe
                            src="<?= htmlspecialchars($pl['url'], ENT_QUOTES, 'UTF-8') ?>"
                            width="100%"
                            height="352"
                            frameborder="0"
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy"
                            style="border-radius:14px;"
                        ></iframe>
                    </div>
                <?php endforeach; ?>

                <?php if (!$hasSpotify): ?>
                    <p class="muted-text">Noch keine Spotify-Playlists vorhanden.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- ===============================
             YOUTUBE MUSIC
        =============================== -->
        <section class="content-block alt-bg">
            <h2 class="section-title">📺 YouTube <span>Music</span></h2>

            <div class="media-grid">
                <?php
                $hasYoutube = false;
                foreach ($playlists as $pl):
                    if (!in_array($pl['type'] ?? '', ['youtube', 'youtube_music'], true)) continue;
                    $hasYoutube = true;
                ?>
                    <div class="media-card">
                        <div class="media-card-body">
                            <h3><?= htmlspecialchars($pl['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>

                            <a
                                href="<?= htmlspecialchars($pl['url'], ENT_QUOTES, 'UTF-8') ?>"
                                target="_blank"
                                class="btn-accent"
                                style="margin-top:12px; display:inline-block;"
                            >
                                ▶️ Anhören
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (!$hasYoutube): ?>
                    <p class="muted-text">Noch keine YouTube-Playlists vorhanden.</p>
                <?php endif; ?>
            </div>
        </section>

    <?php else: ?>

        <div class="empty-state center">
            <p class="muted-text">
                Aktuell sind noch keine Playlisten verfügbar.
            </p>
        </div>

    <?php endif; ?>

</section>
