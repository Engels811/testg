<?php ob_start(); ?>

<!-- =========================================================
     DASHBOARD OVERVIEW – FIRE STYLE (FINAL)
========================================================= -->

<section class="dashboard-overview">

    <!-- =========================
         WELCOME / HERO
    ========================= -->
    <div class="dashboard-welcome-card">

        <div class="dashboard-welcome-avatar">
            <img
                src="/uploads/avatars/<?= htmlspecialchars($user['avatar'] ?? 'default.png') ?>"
                alt="Avatar"
            >
            <span class="status-dot online" title="Online"></span>
        </div>

        <div class="dashboard-welcome-text">
            <h1>
                Willkommen,
                <span>
                    <?= htmlspecialchars($user['username'] ?? 'User') ?>
                </span>
            </h1>
            <p>Dein persönliches Control Panel</p>
        </div>

    </div>

    <!-- =========================
         OVERVIEW GRID
    ========================= -->
    <div class="dashboard-overview-grid">

        <!-- PROFIL -->
        <div class="dashboard-overview-card">
            <h3>
                <span class="icon">👤</span>
                Profil
            </h3>
            <p>Daten & Avatar</p>
            <a href="/dashboard/profile">
                Profil verwalten →
            </a>
        </div>

        <!-- INHALTE -->
        <div class="dashboard-overview-card">
            <h3>
                <span class="icon">📂</span>
                Inhalte
            </h3>
            <p>Videos, Uploads & Beiträge</p>
            <a href="/dashboard/content">
                Inhalte verwalten →
            </a>
        </div>

        <!-- SICHERHEIT -->
        <div class="dashboard-overview-card">
            <h3>
                <span class="icon">🔐</span>
                Sicherheit
            </h3>
            <p>Passwort, Sitzungen & 2FA</p>
            <a href="/dashboard/security">
                Sicherheit öffnen →
            </a>
        </div>

    </div>

</section>

<?php
$content = ob_get_clean();
require __DIR__ . '/_layout.php';
