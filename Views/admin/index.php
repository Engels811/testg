<section class="section admin-dashboard">
    <div class="container">

        <!-- =========================
             HEADER
        ========================= -->
        <header class="admin-head">
            <h1 class="section-title">
                🛠️ <span>Admin Menü</span>
            </h1>
            <p class="section-sub">
                Systemübersicht & Verwaltung
            </p>
        </header>

        <!-- =========================
             CORE MODULES
        ========================= -->
        <div class="grid grid-3">

            <div class="admin-card">
                <h3>👤 Benutzer</h3>
                <p class="admin-stat"><?= $stats['users'] ?></p>
                <span class="admin-label">Registriert</span>
                <a href="/admin/users" class="btn btn-secondary small">Verwalten</a>
            </div>

            <div class="admin-card">
                <h3>💬 Forum</h3>
                <p class="admin-stat"><?= $stats['forum_threads'] ?></p>
                <span class="admin-label">
                    Threads · <?= $stats['forum_posts'] ?> Beiträge
                </span>
                <a href="/admin/forum" class="btn btn-secondary small">Moderieren</a>
            </div>

            <div class="admin-card">
                <h3>🖼️ Galerie</h3>
                <p class="admin-stat"><?= $stats['gallery_items'] ?></p>
                <span class="admin-label">Uploads</span>
                <a href="/admin/gallery" class="btn btn-secondary small">Verwalten</a>
            </div>

            <div class="admin-card accent">
                <h3>🎮 Games</h3>
                <p class="admin-stat"><?= $stats['games'] ?? 0 ?></p>
                <span class="admin-label"><?= $stats['game_categories'] ?? 0 ?> Kategorien</span>
                <a href="/admin/games" class="btn btn-accent small">Verwalten</a>
            </div>

            <div class="admin-card">
                <h3>🤝 Partner</h3>
                <p class="admin-stat"><?= $stats['partners'] ?? 0 ?></p>
                <span class="admin-label">Öffentlich sichtbar</span>
                <a href="/admin/partners" class="btn btn-secondary small">Verwalten</a>
            </div>

            <div class="admin-card">
                <h3>🎵 Playlisten</h3>
                <p class="admin-stat"><?= $stats['playlists'] ?></p>
                <span class="admin-label">Playlisten</span>
                <a href="/admin/playlists" class="btn btn-secondary small">Bearbeiten</a>
            </div>

            <div class="admin-card">
                <h3>🖥️ Hardware</h3>
                <p class="admin-stat"><?= $stats['hardware_items'] ?></p>
                <span class="admin-label"><?= $stats['hardware_setups'] ?> Setups</span>
                <a href="/admin/hardware" class="btn btn-secondary small">Editor öffnen</a>
            </div>

            <div class="admin-card accent">
                <h3>🎥 Twitch & VODs</h3>
                <p class="admin-stat"><?= $stats['twitch_vods'] ?? '—' ?></p>
                <span class="admin-label">Importierte Streams</span>
                <a href="/admin/twitch" class="btn btn-accent small">Import-Log</a>
            </div>

            <!-- =========================
                 ADMIN MAIL SYSTEM
            ========================= -->
            <?php if (!empty($_SESSION['user']['role_is_admin'])): ?>
                <div class="admin-card accent">
                    <h3>📨 Admin-Mails</h3>
                    <p class="admin-text">Antworten · Vorschau · Versand</p>
                    <span class="admin-label">Mail-System</span>
                    <a href="/admin/mail" class="btn btn-accent small">Öffnen</a>
                </div>

                <div class="admin-card">
                    <h3>📜 Mail-Logs</h3>
                    <p class="admin-text">Gesendete Nachrichten</p>
                    <span class="admin-label">Verlauf & Inhalte</span>
                    <a href="/admin/mail/logs" class="btn btn-secondary small">Einsehen</a>
                </div>
            <?php endif; ?>

            <!-- =========================
                 ROLLEN & SYSTEM (LEVEL-BASIERT)
            ========================= -->

            <?php if (($_SESSION['user']['role_level'] ?? 0) >= 80): ?>
                <div class="admin-card">
                    <h3>🔐 Rollen & Rechte</h3>
                    <p class="admin-text">Rollen · Ranghöhen · Admin-Zugriff</p>
                    <a href="/admin/roles" class="btn btn-secondary small">Verwalten</a>
                </div>
            <?php endif; ?>

            <?php if (($_SESSION['user']['role_level'] ?? 0) >= 100): ?>
                <div class="admin-card danger">
                    <h3>⚙️ Systemeinstellungen</h3>
                    <p class="admin-text">Core · Sicherheit · Konfiguration</p>
                    <a href="/admin/settings" class="btn btn-danger small">Öffnen</a>
                </div>
            <?php endif; ?>

        </div>

        <!-- =========================
             MODERATION & RECHT
        ========================= -->
        <h2 class="section-title spaced">
            📄 <span>Recht & Moderation</span>
        </h2>

        <div class="grid grid-3">

            <div class="admin-card">
                <h3>📜 AGB & Datenschutz</h3>
                <p class="admin-text">CMS & Versionierung</p>
                <a href="/admin/agb" class="btn btn-secondary small">Bearbeiten</a>
            </div>

            <div class="admin-card">
                <h3>✅ AGB-Zustimmungen</h3>
                <p class="admin-text">User & Versionen</p>
                <a href="/admin/agb/consents" class="btn btn-secondary small">Anzeigen</a>
            </div>

            <div class="admin-card danger">
                <h3>🚨 Meldungen</h3>
                <p class="admin-text">Community-Reports</p>
                <a href="/admin/reports" class="btn btn-danger small">Moderieren</a>
            </div>

            <div class="admin-card accent">
                <h3>🛡️ Moderations-Dashboard</h3>
                <p class="admin-text">Reports · Appeals · Statistiken</p>
                <span class="admin-label">Team-Übersicht</span>
                <a href="/admin/dashboard/moderation" class="btn btn-accent small">Öffnen</a>
            </div>

            <div class="admin-card">
                <h3>📊 Aktivitäts-Logs</h3>
                <p class="admin-text">Admin & User Aktionen</p>
                <a href="/admin/logs" class="btn btn-secondary small">Einsehen</a>
            </div>

        </div>

    </div>
</section>
