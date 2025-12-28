<?php
/**
 * Game Card Component – Engels811 Network
 *
 * Erwartete Variablen:
 * @var array $game
 * @var int|null $rank   (optional, z.B. Top-Liste)
 * @var bool $hero       (optional)
 */

$hero = $hero ?? false;
$rank = $rank ?? null;
?>
<article
    class="game-card glass
        <?= $hero ? 'hero is-top' : '' ?>
        <?= !empty($game['is_top']) ? 'is-top' : '' ?>"
    onclick="location.href='/games/<?= htmlspecialchars($game['slug']) ?>'">

    <!-- BADGES -->
    <span class="game-badge provider">
        <?= htmlspecialchars($game['provider']) ?>
    </span>

    <?php if (!empty($game['is_top'])): ?>
        <span class="game-badge top">TOP</span>
    <?php endif; ?>

    <?php if ($rank !== null): ?>
        <span class="game-badge rank">#<?= (int)$rank ?></span>
    <?php endif; ?>

    <!-- COVER -->
    <div class="game-cover">
        <img
            src="<?= htmlspecialchars($game['cover'] ?: '/assets/img/game-placeholder.jpg') ?>"
            alt="<?= htmlspecialchars($game['name']) ?>"
            loading="lazy">

        <!-- HOVER STATS -->
        <div class="game-hover-stats">
            <strong><?= number_format((float)$game['hours'], 1, ',', '.') ?> Std</strong>
            <small><?= htmlspecialchars($game['category'] ?? '—') ?></small>
            <small>
                Zuletzt:
                <?= $game['last_played']
                    ? date('d.m.Y', (int)$game['last_played'])
                    : '–' ?>
            </small>
        </div>
    </div>

    <!-- NAME -->
    <h3><?= htmlspecialchars($game['name']) ?></h3>

    <!-- META -->
    <div class="game-meta">
        <span>
            <strong><?= number_format((float)$game['hours'], 1, ',', '.') ?></strong> Std
        </span>
        <span><?= htmlspecialchars($game['category'] ?? '—') ?></span>
    </div>

    <!-- PROGRESS -->
    <?php if (!empty($game['__progress'])): ?>
        <div class="game-progress">
            <div class="game-progress-bar"
                 style="width:<?= (float)$game['__progress'] ?>%"></div>
        </div>
    <?php endif; ?>

</article>
