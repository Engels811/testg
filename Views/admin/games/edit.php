<?php
/**
 * Admin – Spiel bearbeiten
 *
 * Erwartete Variablen:
 * - $game (Array)
 * - $categories (Array)
 */
?>

<section class="admin-wrapper">
    <div class="admin-container">

        <!-- HEADER -->
        <header class="admin-header">
            <h1>🎮 Spiel bearbeiten</h1>
            <p>
                <strong><?= htmlspecialchars($game['name']) ?></strong>
                (ID: <?= (int)$game['id'] ?>)
            </p>
        </header>

        <!-- FORM -->
        <form
            method="post"
            action="/admin/games/update"
            class="admin-form"
        >

            <?= Security::csrfField() ?>

            <input type="hidden" name="id" value="<?= (int)$game['id'] ?>">

            <!-- =========================
                 BASISDATEN
            ========================= -->
            <div class="form-group">
                <label>Spielname</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($game['name']) ?>"
                    disabled
                >
            </div>

            <div class="form-group">
                <label>Slug</label>
                <input
                    type="text"
                    value="<?= htmlspecialchars($game['slug']) ?>"
                    disabled
                >
            </div>

            <!-- =========================
                 KATEGORIE
            ========================= -->
            <div class="form-group">
                <label>Kategorie</label>
                <select name="category">
                    <option value="">— keine —</option>

                    <?php foreach ($categories as $cat): ?>
                        <option
                            value="<?= htmlspecialchars($cat) ?>"
                            <?= ($game['category'] === $cat) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- =========================
                 COVER
            ========================= -->
            <div class="form-group">
                <label>Cover URL</label>
                <input
                    type="text"
                    name="cover"
                    placeholder="https://..."
                    value="<?= htmlspecialchars($game['cover'] ?? '') ?>"
                >

                <?php if (!empty($game['cover'])): ?>
                    <div class="cover-preview">
                        <img
                            src="<?= htmlspecialchars($game['cover']) ?>"
                            alt="Cover Preview"
                        >
                    </div>
                <?php endif; ?>
            </div>

            <!-- =========================
                 STUNDEN
            ========================= -->
            <div class="form-group">
                <label>Stunden überschreiben</label>
                <input
                    type="number"
                    step="0.1"
                    name="hours_override"
                    placeholder="leer = automatisch"
                    value="<?= htmlspecialchars($game['hours_override'] ?? '') ?>"
                >
            </div>

            <!-- =========================
                 PROVIDER
            ========================= -->
            <div class="form-group">
                <label>Provider</label>
                <select name="provider">
                    <?php
                    $providers = [
                        'steam'     => 'Steam',
                        'epic'      => 'Epic Games',
                        'rockstar'  => 'Rockstar',
                        'ubisoft'   => 'Ubisoft',
                        'custom'    => 'Custom'
                    ];
                    ?>
                    <?php foreach ($providers as $key => $label): ?>
                        <option
                            value="<?= $key ?>"
                            <?= ($game['provider'] === $key) ? 'selected' : '' ?>
                        >
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- =========================
                 FLAGS
            ========================= -->
            <div class="form-group check-group">
                <label>
                    <input
                        type="checkbox"
                        name="confirmed"
                        <?= $game['confirmed'] ? 'checked' : '' ?>
                    >
                    Öffentlich sichtbar
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="is_top"
                        <?= $game['is_top'] ? 'checked' : '' ?>
                    >
                    Als Top-Spiel markieren
                </label>
            </div>

            <!-- =========================
                 META INFO
            ========================= -->
            <div class="meta-box">
                <small>
                    Erstellt: <?= htmlspecialchars($game['created_at']) ?><br>
                    Zuletzt geändert: <?= htmlspecialchars($game['updated_at']) ?><br>
                    Zuletzt gespielt:
                    <?= $game['last_played']
                        ? htmlspecialchars($game['last_played'])
                        : '–'
                    ?>
                </small>
            </div>

            <!-- =========================
                 ACTIONS
            ========================= -->
            <div class="form-actions">
                <a href="/admin/games" class="btn btn-secondary">
                    ← Zurück
                </a>

                <button type="submit" class="btn btn-primary">
                    💾 Speichern
                </button>
            </div>

        </form>

    </div>
</section>
