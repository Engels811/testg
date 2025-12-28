<?php
/**
 * Engels811 Network – Global Header
 * RBAC-clean (role_level)
 * Live-Status wird per JavaScript aus /api/twitch-status.php geladen
 */
?>

<!-- FAVICON (CACHE-BUST) -->
<link rel="icon"
      href="/favicon.ico?v=<?= htmlspecialchars(FaviconService::getVersion()) ?>"
      type="image/x-icon">

<header class="site-header">
    <div class="header-inner">

        <!-- =========================
         BRAND (LOGO)
        ========================= -->
        <div class="brand">
            <a href="/" class="brand-logo-link" aria-label="Engels811 Network – Home">
                <span class="brand-text">
                    <span class="brand-name">Engels811</span>
                    <span class="brand-sub">Network</span>
                </span>
            </a>
        </div>

        <!-- =========================
         DESKTOP NAV
        ========================= -->
        <nav class="main-nav">
            <a href="/" class="<?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>">Home</a>
            <a href="/games" class="<?= ($currentPage ?? '') === 'games' ? 'active' : '' ?>">Games</a>
            <a href="/forum" class="<?= ($currentPage ?? '') === 'forum' ? 'active' : '' ?>">Forum</a>
            <a href="/galerie" class="<?= ($currentPage ?? '') === 'galerie' ? 'active' : '' ?>">Galerie</a>
            <a href="/videos" class="<?= ($currentPage ?? '') === 'videos' ? 'active' : '' ?>">Videos</a>
            <a href="/playlisten" class="<?= ($currentPage ?? '') === 'playlisten' ? 'active' : '' ?>">Playlisten</a>
            <a href="/hardware" class="<?= ($currentPage ?? '') === 'hardware' ? 'active' : '' ?>">Hardware</a>
            <a href="/partner" class="<?= ($currentPage ?? '') === 'partner' ? 'active' : '' ?>">Partner</a>
            <a href="/live" class="<?= ($currentPage ?? '') === 'live' ? 'active' : '' ?>">Live</a>
            <a href="/about" class="<?= ($currentPage ?? '') === 'about' ? 'active' : '' ?>">About</a>

            <!-- ADMIN / MODERATOR (RBAC) -->
            <?php if (!empty($_SESSION['user']['role_level']) && $_SESSION['user']['role_level'] >= 50): ?>
                <a href="/admin" class="<?= ($currentPage ?? '') === 'admin' ? 'active' : '' ?>">
                    Admin
                </a>
            <?php endif; ?>
        </nav>

        <!-- =========================
         RIGHT SIDE
        ========================= -->
        <div class="header-actions">

            <!-- LIVE BADGE (JS) -->
            <a href="/live"
               class="live-badge"
               id="liveBadge"
               style="display:none">
                LIVE
            </a>

            <!-- AUTH -->
            <?php if (!empty($_SESSION['user'])): ?>

                <!-- LOGOUT -->
                <form method="post" action="/logout" class="logout-form">
                    <?= Security::csrfField() ?>
                    <button type="submit" class="btn btn-secondary small btn-logout">
                        Logout
                    </button>
                </form>

            <?php else: ?>

                <a href="/login" class="btn btn-secondary small">
                    Login
                </a>

                <a href="/register" class="btn btn-primary small">
                    Registrieren
                </a>

            <?php endif; ?>

            <!-- BURGER -->
            <button class="burger" id="burgerBtn" aria-label="Menü öffnen">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </div>
    </div>

    <!-- =========================
     MOBILE NAV
    ========================= -->
    <div class="mobile-nav" id="mobileNav">
        <a href="/">Home</a>
        <a href="/games">Games</a>
        <a href="/forum">Forum</a>
        <a href="/galerie">Galerie</a>
        <a href="/videos">Videos</a>
        <a href="/playlisten">Playlisten</a>
        <a href="/hardware">Hardware</a>
        <a href="/partner">Partner</a>
        <a href="/live">Live</a>
        <a href="/about">About</a>

        <!-- ADMIN / MODERATOR (RBAC) -->
        <?php if (!empty($_SESSION['user']['role_level']) && $_SESSION['user']['role_level'] >= 50): ?>
            <a href="/admin">Admin</a>
        <?php endif; ?>

        <?php if (!empty($_SESSION['user'])): ?>

            <!-- MOBILE LOGOUT -->
            <form method="post" action="/logout" class="logout-form mobile-logout">
                <?= Security::csrfField() ?>
                <button type="submit" class="mobile-link">
                    Logout
                </button>
            </form>

        <?php else: ?>

            <a href="/login">Login</a>
            <a href="/register">Registrieren</a>

        <?php endif; ?>
    </div>
</header>
