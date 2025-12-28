<section class="section partner-page">
    <div class="container">

        <!-- =========================
             HEADER
        ========================= -->
        <header class="section-head">
            <h1 class="section-title">
                🤝 <span>Unsere Partner</span>
            </h1>
            <p class="section-sub">
                Unterstützer, Creator & Community-Partner
            </p>
        </header>

        <!-- =========================
             CONTENT
        ========================= -->
        <?php if (empty($partners)): ?>
            <div class="empty-state">
                <p>Aktuell sind noch keine Partner eingetragen.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-3">

                <?php foreach ($partners as $partner): ?>
                    <div class="card partner-card">

                        <?php if (!empty($partner['logo'])): ?>
                            <div class="partner-logo-wrap">
                                <img
                                    src="<?= htmlspecialchars($partner['logo'], ENT_QUOTES) ?>"
                                    alt="<?= htmlspecialchars($partner['name'], ENT_QUOTES) ?>"
                                    class="partner-logo"
                                >
                            </div>
                        <?php endif; ?>

                        <h3 class="partner-name">
                            <?= htmlspecialchars($partner['name'], ENT_QUOTES) ?>
                        </h3>

                        <?php if (!empty($partner['description'])): ?>
                            <p class="partner-desc">
                                <?= nl2br(htmlspecialchars($partner['description'], ENT_QUOTES)) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($partner['url'])): ?>
                            <a
                                href="<?= htmlspecialchars($partner['url'], ENT_QUOTES) ?>"
                                target="_blank"
                                rel="noopener"
                                class="btn btn-secondary small"
                            >
                                🔗 Zum Partner
                            </a>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>

    </div>
</section>
