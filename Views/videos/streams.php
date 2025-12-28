<section class="section">
    <div class="container">

        <!-- =========================
             LIVE STREAM (PINNED)
        ========================== -->
        <?php if (!empty($isLive)): ?>
            <div class="live-stream-box">

                <div class="live-badge">🔴 LIVE</div>

                <iframe
                    src="https://player.twitch.tv/?channel=engels811&parent=<?= $_SERVER['HTTP_HOST'] ?>"
                    allowfullscreen
                    loading="lazy">
                </iframe>

            </div>
        <?php endif; ?>


        <!-- =========================
             HEADER
        ========================== -->
        <header class="section-head center">
            <h2 class="section-title">
                🎥 <span>Streams</span>
            </h2>
            <p class="section-sub">
                Vergangene Twitch-Livestreams
            </p>
        </header>


        <!-- =========================
             EMPTY STATE
        ========================== -->
        <?php if (empty($videos)): ?>

            <div class="streams-empty">
                <div class="empty-icon">📡</div>
                <h3>Noch keine Streams verfügbar</h3>
                <p>
                    Vergangene Livestreams erscheinen hier automatisch,
                    sobald ein Stream beendet wurde.
                </p>

                <a href="/live" class="btn-accent">
                    🔴 Zum Live-Stream
                </a>
            </div>

        <?php else: ?>


        <!-- =========================
             STREAM GRID
        ========================== -->
        <div class="streams-grid">

            <?php foreach ($videos as $v): ?>
                <article class="stream-card <?= !empty($v['is_pinned']) ? 'is-new' : '' ?>">

                    <!-- THUMBNAIL / CLICK TO PLAY -->
                    <div class="stream-thumb" data-play>
                        <img
                            src="<?= htmlspecialchars($v['thumbnail'], ENT_QUOTES) ?>"
                            alt="<?= htmlspecialchars($v['title'], ENT_QUOTES) ?>"
                            loading="lazy"
                        >
                        <span class="play-overlay">▶</span>
                    </div>

                    <!-- META -->
                    <div class="stream-body">
                        <h3 class="stream-title">
                            <?= htmlspecialchars($v['title'], ENT_QUOTES) ?>
                        </h3>

                        <div class="stream-meta">
                            <span>📅 <?= date('d.m.Y', strtotime($v['published_at'])) ?></span>
                            <?php if (!empty($v['duration_seconds'])): ?>
                                <span>⏱ <?= gmdate('H:i:s', (int)$v['duration_seconds']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($v['view_count'])): ?>
                                <span>👁 <?= number_format((int)$v['view_count']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- PLAYER (LAZY) -->
                    <div class="stream-player">
                        <iframe
                            data-src="<?= htmlspecialchars($v['url'], ENT_QUOTES) ?>"
                            allowfullscreen>
                        </iframe>
                    </div>

                </article>
            <?php endforeach; ?>

        </div>

        <?php endif; ?>

    </div>
</section>


<!-- =========================
     CLICK TO PLAY SCRIPT
========================== -->
<script>
document.addEventListener('click', function (e) {
    const thumb = e.target.closest('[data-play]');
    if (!thumb) return;

    const card = thumb.closest('.stream-card');
    if (!card || card.classList.contains('is-playing')) return;

    const iframe = card.querySelector('iframe');
    iframe.src = iframe.dataset.src;

    card.classList.add('is-playing');
});
</script>