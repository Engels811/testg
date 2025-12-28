<?php
/**
 * AGB – Allgemeine Geschäftsbedingungen
 * Engels811 TV – Streaming & Gaming
 *
 * Erwartete Variablen:
 * - $version
 * - $stand
 * - $agb (Array mit ['geltung','konto','verhalten', ...])
 */
?>

<!-- AGB SPEZIAL CSS (NUR DIESE SEITE) -->
<!-- <link rel="stylesheet" href="/assets/css/agb.css"> -->

<section class="page-wrapper agb-page">

    <!-- =========================
         PAGE HEAD
    ========================= -->
    <div class="page-head center">
        <span class="page-icon">📜</span>
        <h1 class="page-title accent">AGB</h1>
        <p class="page-subtitle">
            Version <?= htmlspecialchars($version) ?>
            · Stand <?= htmlspecialchars($stand) ?>
        </p>
    </div>

    <!-- =========================
         AGB CONTENT
    ========================= -->
    <div class="media-stack">

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['geltung']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['geltung']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['konto']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['konto']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['verhalten']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['verhalten']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['forum']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['forum']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['uploads']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['uploads']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['rechte']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['rechte']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['sperre']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['sperre']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['haftung']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['haftung']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['aenderung']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['aenderung']['content'])) ?>
            </p>
        </div>

        <div class="media-card">
            <h3><?= htmlspecialchars($agb['schluss']['title']) ?></h3>
            <p class="text-muted">
                <?= nl2br(htmlspecialchars($agb['schluss']['content'])) ?>
            </p>
        </div>

    </div>

    <!-- =========================
         FOOTER NOTE
    ========================= -->
    <p class="footer-note center">
        © <?= date('Y') ?> Engels811 TV – Streaming & Gaming
    </p>

</section>
