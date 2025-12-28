<section class="admin-wrapper">

    <!-- =========================
         HEADER
    ========================== -->
    <header class="admin-head">
        <div>
            <h1 class="admin-title">🎵 Playlisten</h1>
            <p class="admin-subtitle">
                Spotify & YouTube Music Inhalte verwalten
            </p>
        </div>

        <a href="/admin/playlists/create" class="btn-accent btn-glow">
            ＋ Neue Playlist
        </a>
    </header>

    <!-- =========================
         LISTE
    ========================== -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>Titel</th>
                <th>Plattform</th>
                <th>Status</th>
                <th>Sort</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($playlists as $pl): ?>
            <tr>
                <td>
                    <strong>
                        <?= htmlspecialchars($pl['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </td>

                <td>
                    <?= strtoupper(htmlspecialchars($pl['type'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                </td>

                <td>
                    <span class="<?= !empty($pl['is_active']) ? 'status-active' : 'status-inactive' ?>">
                        <?= !empty($pl['is_active']) ? 'Aktiv' : 'Inaktiv' ?>
                    </span>
                </td>

                <td>
                    <?= (int)($pl['sort_order'] ?? 0) ?>
                </td>

                <td class="admin-actions">

                    <form method="post" action="/admin/playlists/toggle" style="display:inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$pl['id'] ?>">
                        <button class="btn-small" title="Aktiv / Inaktiv">
                            Toggle
                        </button>
                    </form>

                    <form method="post"
                          action="/admin/playlists/delete"
                          onsubmit="return confirm('Playlist wirklich löschen?')"
                          style="display:inline">

                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$pl['id'] ?>">
                        <button class="btn-danger" title="Löschen">
                            ✕
                        </button>
                    </form>

                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</section>
