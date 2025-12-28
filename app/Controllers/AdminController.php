<?php
declare(strict_types=1);

class AdminController
{
    public function index(): void
    {
        /* =========================================
           ZUGRIFFSSCHUTZ (RBAC)
        ========================================= */
        if (!Permission::has('admin.access')) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            return;
        }

        /* =========================================
           DASHBOARD STATISTIKEN
        ========================================= */
        $stats = [

            /* =========================
               BENUTZER
            ========================= */
            'users' => (int)(Database::fetch("SELECT COUNT(*) AS c FROM users")['c'] ?? 0),

            'users_agb_accepted' => (int)(
                Database::fetch(
                    "SELECT COUNT(*) AS c FROM users WHERE agb_accepted_at IS NOT NULL"
                )['c'] ?? 0
            ),

            'users_locked' => (int)(
                Database::fetch(
                    "SELECT COUNT(*) AS c FROM users WHERE account_locked = 1"
                )['c'] ?? 0
            ),

            /* =========================
               LOGIN / SECURITY
            ========================= */
            'logins_24h' => (int)(
                Database::fetch(
                    "SELECT COUNT(*) AS c
                     FROM login_history
                     WHERE created_at > (NOW() - INTERVAL 24 HOUR)"
                )['c'] ?? 0
            ),

            'new_devices_24h' => (int)(
                Database::fetch(
                    "SELECT COUNT(DISTINCT device_hash) AS c
                     FROM remembered_devices
                     WHERE created_at > (NOW() - INTERVAL 24 HOUR)"
                )['c'] ?? 0
            ),

            /* =========================
               FORUM
            ========================= */
            'forum_threads' => (int)(Database::fetch("SELECT COUNT(*) AS c FROM forum_threads")['c'] ?? 0),
            'forum_posts'   => (int)(Database::fetch("SELECT COUNT(*) AS c FROM forum_posts")['c'] ?? 0),

            /* =========================
               GALERIE
            ========================= */
            'gallery_items' => (int)(Database::fetch("SELECT COUNT(*) AS c FROM gallery_media")['c'] ?? 0),

            /* =========================
               VIDEOS / PLAYLISTEN
            ========================= */
            'videos'    => (int)(Database::fetch("SELECT COUNT(*) AS c FROM videos")['c'] ?? 0),
            'playlists' => (int)(Database::fetch("SELECT COUNT(*) AS c FROM playlists")['c'] ?? 0),

            /* =========================
               HARDWARE
            ========================= */
            'hardware_items'  => (int)(Database::fetch("SELECT COUNT(*) AS c FROM hardware_items")['c'] ?? 0),
            'hardware_setups' => (int)(Database::fetch("SELECT COUNT(*) AS c FROM hardware_setups")['c'] ?? 0),

            /* =========================
               GAMES
            ========================= */
            'games' => (int)(Database::fetch("SELECT COUNT(*) AS c FROM games")['c'] ?? 0),

            'game_categories' => (int)(
                Database::fetch(
                    "SELECT COUNT(DISTINCT category) AS c
                     FROM games
                     WHERE category IS NOT NULL AND category != ''"
                )['c'] ?? 0
            ),

            /* =========================
               PARTNER / VODS / CMS
            ========================= */
            'partners'     => (int)(Database::fetch("SELECT COUNT(*) AS c FROM partners")['c'] ?? 0),
            'twitch_vods'  => (int)(Database::fetch("SELECT COUNT(*) AS c FROM twitch_vods")['c'] ?? 0),
            'cms_pages'    => (int)(Database::fetch("SELECT COUNT(*) AS c FROM cms_pages")['c'] ?? 0),

            /* =========================
               COMMUNITY
            ========================= */
            'reports_open' => (int)(
                Database::fetch(
                    "SELECT COUNT(*) AS c FROM reports WHERE status = 'open'"
                )['c'] ?? 0
            ),
        ];

        /* =========================================
           VIEW
        ========================================= */
        View::render('admin/index', [
            'title'       => 'Admin Dashboard',
            'currentPage' => 'admin',
            'stats'       => $stats
        ]);
    }
}
