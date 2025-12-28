<?php
/**
 * Game Detail Page
 * /games/{slug}
 */

$g = $game;

// ===============================
// COVER
// ===============================
$cover = !empty($g['cover'])
    ? $g['cover']
    : '/assets/img/game-placeholder.jpg';

// ===============================
// LAST PLAYED
// ===============================
$daysAgo = function ($ts) {
    if (!$ts) return '';
    $d = floor((time() - (int)$ts) / 86400);
    return $d <= 0 ? 'heute' : "vor {$d} Tagen";
};

// ===============================
// PROVIDER SVG
// ===============================
$providerIcon = match ($g['provider']) {
    'steam'     => '/assets/icons/steam.svg',
    'epic'      => '/assets/icons/epic.svg',
    'rockstar'  => '/assets/icons/rockstar.svg',
    'ubisoft'   => '/assets/icons/ubisoft.svg',
    default     => '/assets/icons/gamepad.svg',
};
?>

<section class="section section-soft">
    <div class="container game-detail">

        <div class="game-detail-grid">

            <!-- COVER -->
            <div class="game-cover">
                <img
                    src="<?= htmlspecialchars($cover) ?>"
                    alt="<?= htmlspecialchars($g['name']) ?>"
                >
            </div>

            <!-- INFO -->
            <div class="game-info">

                <h1>
                    <?= htmlspecialchars($g['name']) ?>

                    <?php if (!empty($isLive)): ?>
                        <span class="badge badge-live">LIVE</span>
                    <?php endif; ?>
                </h1>

                <!-- META -->
                <div class="game-meta">

                    <!-- PROVIDER -->
                    <span class="provider-badge <?= htmlspecialchars($g['provider']) ?>">
                        <img
                            src="<?= $providerIcon ?>"
                            alt="<?= htmlspecialchars($g['provider']) ?>"
                            width="16"
                            height="16"
                        >
                        <?= strtoupper(htmlspecialchars($g['provider'])) ?>
                    </span>

                    <!-- CATEGORY -->
                    <span class="badge badge-category">
                        <?= htmlspecialchars($g['category'] ?? 'Other') ?>
                    </span>

                </div>

                <!-- HOURS -->
                <p class="game-hours">
                    🎮
                    <?= number_format((float)$g['hours'], 1, ',', '.') ?>
                    Stunden gespielt
                </p>

                <!-- LAST PLAYED -->
                <?php if (!empty($g['last_played'])): ?>
                    <p class="game-last">
                        ⏱ Zuletzt gespielt <?= $daysAgo($g['last_played']) ?>
                    </p>
                <?php endif; ?>

                <!-- TWITCH LIVE -->
                <?php if (!empty($isLive)): ?>
                    <a
                        class="btn btn-primary mt-20"
                        target="_blank"
                        href="https://www.twitch.tv/engels811"
                    >
                        🔴 Jetzt live auf Twitch
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </div>
</section>
