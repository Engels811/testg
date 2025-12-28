<?php ob_start(); ?>

<header class="dashboard-head">
    <h1>📂 Meine Inhalte</h1>
    <p>Verwalte alle Inhalte, die du erstellt hast</p>
</header>

<!-- =========================
     VIDEOS
========================= -->
<section class="dashboard-section">
    <h2>🎥 Videos</h2>

    <?php if (empty($videos)): ?>
        <p class="muted">Keine Videos vorhanden.</p>
    <?php else: ?>
        <ul class="dashboard-list">
            <?php foreach ($videos as $v): ?>
                <li>
                    <span><?= htmlspecialchars($v['title']) ?></span>

                    <div class="actions">
                        <a href="/videos/edit?id=<?= (int)$v['id'] ?>">
                            Bearbeiten
                        </a>

                        <form method="post" action="/videos/delete">
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                            <button type="submit">Löschen</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<!-- =========================
     GALERIE
========================= -->
<section class="dashboard-section">
    <h2>🖼 Galerie</h2>

    <?php if (empty($gallery)): ?>
        <p class="muted">Keine Bilder hochgeladen.</p>
    <?php else: ?>
        <ul class="dashboard-list">
            <?php foreach ($gallery as $g): ?>
                <li>
                    <span><?= htmlspecialchars($g['title']) ?></span>

                    <div class="actions">
                        <form method="post" action="/galerie/delete">
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                            <button type="submit">Löschen</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<!-- =========================
     FORUM
========================= -->
<section class="dashboard-section">
    <h2>💬 Forum-Beiträge</h2>

    <?php if (empty($threads)): ?>
        <p class="muted">Keine Threads erstellt.</p>
    <?php else: ?>
        <ul class="dashboard-list">
            <?php foreach ($threads as $t): ?>
                <li>
                    <span><?= htmlspecialchars($t['title']) ?></span>

                    <a href="/forum/thread/<?= (int)$t['id'] ?>" class="btn-link">
                        Öffnen →
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
