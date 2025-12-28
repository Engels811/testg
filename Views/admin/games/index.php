<?php
/**
 * Admin – Spiele verwalten (Card/Grid Version)
 */
?>

<section class="admin-section">

    <!-- =========================
         HEADER
    ========================= -->
    <header class="admin-header">
        <h1>🎮 Spiele verwalten</h1>
        <p>Alle importierten Spiele – sichtbar, versteckt & Top-Spiele</p>
    </header>

    <!-- =========================
         GRID
    ========================= -->
    <div class="games-grid auto admin-games-grid">

        <?php foreach ($games as $g): ?>
            <article class="game-card glass admin-game-card
                <?= !$g['confirmed'] ? 'is-hidden' : '' ?>
                <?= $g['is_top'] ? 'is-top' : '' ?>">

                <!-- BADGES -->
                <span class="game-badge provider">
                    <?= strtoupper(htmlspecialchars($g['provider'])) ?>
                </span>

                <?php if ($g['is_top']): ?>
                    <span class="game-badge top">TOP</span>
                <?php endif; ?>

                <!-- COVER + OVERLAY -->
                <div class="game-cover">

                    <img
                        src="<?= htmlspecialchars($g['cover'] ?: '/assets/img/game-placeholder.jpg') ?>"
                        alt="<?= htmlspecialchars($g['name']) ?>"
                        loading="lazy"
                    >

                    <div class="game-admin-overlay">

                        <!-- TOP -->
                        <button
                            class="admin-icon star <?= $g['is_top'] ? 'active' : '' ?>"
                            type="button"
                            title="Top-Spiel umschalten"
                            onclick="toggleGame(<?= (int)$g['id'] ?>,'is_top')"
                        >
                            ⭐
                        </button>

                        <!-- VISIBILITY -->
                        <button
                            class="admin-icon <?= $g['confirmed'] ? 'active' : '' ?>"
                            type="button"
                            title="Sichtbarkeit umschalten"
                            onclick="toggleGame(<?= (int)$g['id'] ?>,'confirmed')"
                        >
                            👁
                        </button>

                        <!-- EDIT -->
                        <a
                            href="/admin/games/edit/<?= (int)$g['id'] ?>"
                            class="admin-icon edit"
                            title="Bearbeiten"
                        >
                            ✏️
                        </a>

                    </div>
                </div>

                <!-- NAME -->
                <h3><?= htmlspecialchars($g['name']) ?></h3>

                <!-- META -->
                <div class="game-meta">
                    <span>
                        <strong>
                            <?= number_format(
                                $g['hours_override'] ?? $g['hours'],
                                1,
                                ',',
                                '.'
                            ) ?>
                        </strong> Std
                    </span>

                    <span><?= htmlspecialchars($g['category'] ?? '—') ?></span>
                </div>

            </article>
        <?php endforeach; ?>

        <?php if (empty($games)): ?>
            <div class="empty-state">
                <p>Keine Spiele gefunden.</p>
            </div>
        <?php endif; ?>

    </div>

</section>

<script>
function toggleGame(id, field) {
    fetch('/admin/games/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': window.CSRF_TOKEN || ''
        },
        body: 'id=' + encodeURIComponent(id) + '&field=' + encodeURIComponent(field)
    })
    .then(res => {
        if (!res.ok) {
            alert('Aktion fehlgeschlagen');
            return;
        }
        location.reload();
    })
    .catch(() => alert('Netzwerkfehler'));
}
</script>
