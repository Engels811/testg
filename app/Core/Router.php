<?php
declare(strict_types=1);

/* =========================================================
   BASE PATH (ZENTRAL)
========================================================= */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

/* ===== ADMIN CONTROLLER (MANUELL) ===== */
require_once BASE_PATH . '/app/Controllers/Admin/AdminFaviconController.php';
require_once BASE_PATH . '/app/Controllers/Admin/AdminDashboardController.php';
require_once BASE_PATH . '/app/Controllers/Admin/AdminAppealController.php';
require_once BASE_PATH . '/app/Controllers/ReportController.php';
require_once BASE_PATH . '/app/Controllers/Admin/AdminModerationController.php';



class Router
{
    public function dispatch(): void
    {
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        /* =========================================================
           CRON – TWITCH VOD AUTO-IMPORT
           URL: /_cron/import-twitch?token=XYZ
        ========================================================= */

        if ($uri === '_cron/import-twitch') {

            if (php_sapi_name() !== 'cli') {
                if (($_GET['token'] ?? '') !== 'cm0q9af199fhr4f0hyctndjbh95m1x') {
                    http_response_code(403);
                    exit('Forbidden');
                }
            }

            require_once BASE_PATH . '/app/Core/Database.php';
            require_once BASE_PATH . '/app/Services/TwitchService.php';

            $twitch = new TwitchService();
            $vods   = $twitch->getVods(25);

            if (empty($vods)) {
                Database::execute(
                    "INSERT INTO twitch_import_logs (imported, status, message)
                     VALUES (0, 'info', 'Keine neuen Twitch-VODs verfügbar')"
                );
                exit('OK – keine neuen VODs');
            }

            $toSeconds = function (string $duration): int {
                preg_match_all('/(\d+)([hms])/', $duration, $m);
                $sec = 0;
                foreach ($m[1] as $i => $v) {
                    $sec += match ($m[2][$i]) {
                        'h' => (int)$v * 3600,
                        'm' => (int)$v * 60,
                        's' => (int)$v,
                        default => 0
                    };
                }
                return $sec;
            };

            $imported = 0;

            foreach ($vods as $vod) {

                $exists = Database::fetch(
                    "SELECT id FROM videos WHERE twitch_video_id = ?",
                    [$vod['id']]
                );

                if ($exists) {
                    continue;
                }

                $thumbnail = '';
                if (!empty($vod['thumbnail_url'])) {
                    $thumbnail = str_replace(
                        ['%{width}', '%{height}'],
                        ['640', '360'],
                        $vod['thumbnail_url']
                    );
                }

                $embedUrl =
                    'https://player.twitch.tv/?video=' . $vod['id'] .
                    '&parent=engels811-ttv.de';

                Database::execute(
                    "INSERT INTO videos
                        (title, url, thumbnail, source, twitch_video_id,
                         duration_seconds, view_count, is_pinned,
                         published_at, created_at)
                     VALUES
                        (?, ?, ?, 'twitch', ?, ?, ?, 1, ?, NOW())",
                    [
                        $vod['title'],
                        $embedUrl,
                        $thumbnail,
                        $vod['id'],
                        $toSeconds($vod['duration'] ?? ''),
                        (int)($vod['view_count'] ?? 0),
                        date('Y-m-d H:i:s', strtotime($vod['created_at']))
                    ]
                );

                $imported++;
            }

            Database::execute(
                "INSERT INTO twitch_import_logs (imported, status, message)
                 VALUES (?, 'success', 'Auto-Import ausgeführt')",
                [$imported]
            );

            exit("OK – {$imported} neue Twitch-VOD(s) importiert");
        }

        /* =========================================================
           STARTSEITE
        ========================================================= */

        if ($uri === '' || $uri === 'home') {
            (new HomeController())->index();
            return;
        }

        /* =========================================================
           KONTAKT
        ========================================================= */
            
        // Formular anzeigen
        if ($uri === 'kontakt' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            (new ContactController())->showForm();
            return;
        }
        
        // Formular absenden
        if ($uri === 'kontakt' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ContactController())->submitForm();
            return;
        }



        /* =========================================================
           CMS (ÖFFENTLICH)
        ========================================================= */

        if ($uri === 'agb') {
            (new AgbController())->index();
            return;
        }

        if (in_array($uri, ['about', 'impressum', 'datenschutz'], true)) {
            (new CmsController())->show($uri);
            return;
        }

        if ($uri === 'games') {
            (new GamesController())->index();
            return;
        }

        if (preg_match('#^games/([a-z0-9-]+)$#', $uri, $m)) {
            (new GamesController())->show($m[1]);
            return;
        }

        if ($uri === '_cron/twitch-live') {
            (new CronController())->twitchLive();
            return;
        }

        if ($uri === '_cron/steam-import') {
            (new CronController())->steamImport();
            return;
        }

        if ($uri === 'email/confirm' && isset($_GET['token'])) {
            (new EmailController())->confirmEmail();
            return;
        }

        /* =========================================================
           AUTH
        ========================================================= */

        if ($uri === 'login') {
            (new AuthController())->login();
            return;
        }

        /* =========================
           2FA – FORMULAR (GET)
        ========================= */
        if ($uri === 'login/2fa' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            View::render('auth/2fa', [
                'title' => 'Zwei-Faktor-Authentifizierung'
            ]);
            return;
        }

        /* =========================
               2FA – VERIFY (POST)
            ========================= */
            if ($uri === 'login/2fa' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AuthController())->verify2FA();
                return;
            }

        if ($uri === 'register') {
            (new AuthController())->register();
            return;
        }

        if ($uri === 'logout') {
            (new AuthController())->logout();
            return;
        }

        if ($uri === 'agb/accept') {
            (new AuthController())->acceptAgb();
            return;
        }

        /* ✅ E-MAIL BESTÄTIGUNG (REGISTRIERUNG) */
        if ($uri === 'confirm-email' && isset($_GET['token'])) {
            (new AuthController())->confirmEmail();
            return;
        }


        if ($uri === 'resend-confirm-email') {
            (new AuthController())->resendConfirmEmail();
            return;
        }

        if ($uri === 'email/resend-confirmation' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new EmailController())->resendConfirmation();
            return;
        }



        /* =========================================================
           USER DASHBOARD (GESCHÜTZT)
        ========================================================= */

        if (str_starts_with($uri, 'dashboard')) {
        
            // 🔐 Login erforderlich
            if (empty($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }
        
            $controller = new DashboardController();
        
            // ✅ DASHBOARD STARTSEITE
            if ($uri === 'dashboard') {
                $controller->index();
                return;
            }
        
            /* =========================
               PROFILE
            ========================= */

            if ($uri === 'dashboard/profile/avatar') {
                (new ProfileController())->uploadAvatar();
                return;
            }
        
            if ($uri === 'dashboard/profile' && $_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->profile();
                return;
            }
        
            if ($uri === 'dashboard/profile/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->updateProfile();
                return;
            }
        
            if ($uri === 'dashboard/profile/avatar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->updateAvatar();
                return;
            }
        
            if ($uri === 'dashboard/profile/avatar/select' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->selectAvatar();
                return;
            }
            
            if ($uri === 'account/unlock') {
                (new AuthController())->unlockAccount();
                return;
            }

            if ($uri === 'profile/security') {
                (new ProfileController())->security();
                return;
            }

            /* =========================
               CONTENT
            ========================= */
        
            if ($uri === 'dashboard/content') {
                $controller->content();
                return;
            }
        
            /* =========================
               SECURITY
            ========================= */
        
            if ($uri === 'dashboard/security') {
                $controller->security();
                return;
            }

            if ($uri === 'dashboard/security/session/logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new DashboardController())->logoutSession();
                return;
            }
        
            /* =========================
               FALLBACK
            ========================= */
        
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'Dashboard-Seite nicht gefunden'
            ]);
            return;
        }


        /* =========================================================
           ÖFFENTLICHE MODULE
        ========================================================= */
        
        /* =========================================================
           FORUM
        ========================================================= */
            
        /* ===== FORUM HAUPT-ÜBERSICHT ===== */
        if ($uri === 'forum') {
            (new ForumController())->index();
            return;
        }

        /* ===== FORUM SUCHE ===== */
        if ($uri === 'forum/search') {
            (new ForumSearchController())->index();
            return;
        }

        /* ===== KATEGORIE ANZEIGEN ===== */
        if (preg_match('#^forum/([a-z0-9-]+)$#', $uri, $m)) {
            (new ForumController())->category($m[1]);
            return;
        }

        /* ===== NEUES THEMA (CREATE PAGE) ===== */
        if (preg_match('#^forum/([a-z0-9-]+)/create$#', $uri, $m)) {
            (new ForumController())->createThread($m[1]);
            return;
        }


        /* ===== THEMA SPEICHERN ===== */
        if (preg_match('#^forum/([a-z0-9-]+)/store$#', $uri, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ForumController())->storeThread($m[1]);
            return;
        }

        /* ===== THREAD ANZEIGEN ===== */
        if (preg_match('#^forum/thread/([0-9]+)$#', $uri, $m)) {
            (new ForumController())->thread((int)$m[1]);
            return;
        }

        /* ===== REPLY AUF THREAD ===== */
        if (preg_match('#^forum/thread/([0-9]+)/reply$#', $uri, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ForumController())->reply((int)$m[1]);
            return;
        }

        /* ===== POST BEARBEITEN (FORMULAR) ===== */
        if (preg_match('#^forum/post/([0-9]+)/edit$#', $uri, $m)) {
            (new ForumController())->editPost((int)$m[1]);
            return;
        }

        /* ===== POST UPDATE ===== */
        if (preg_match('#^forum/post/([0-9]+)/update$#', $uri, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ForumController())->updatePost((int)$m[1]);
            return;
        }

        /* ===== POST LÖSCHEN (SOFT DELETE) ===== */
        if (preg_match('#^forum/post/([0-9]+)/delete$#', $uri, $m) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ForumController())->deletePost((int)$m[1]);
            return;
        }

        /* ===== ATTACHMENT LÖSCHEN ===== */
        if ($uri === 'forum/attachment/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ForumAttachmentController())->delete();
            return;
        }

        if ($uri === 'galerie') {
            (new GalleryController())->index();
            return;
        }

        /* =========================================================
           GALERIE – SECTIONS
        ========================================================= */

        if (preg_match('#^galerie/(community|artwork|bts)$#', $uri, $matches)) {
            (new GalleryController())->section($matches[1]);
            return;
        }

        if ($uri === 'report/create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ReportController())->create();
            return;
        }

        /* =========================================================
           GALERIE – ACTIONS (POST)
        ========================================================= */

        if ($uri === 'galerie/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new GalleryController())->upload();
            return;
        }

        if ($uri === 'galerie/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new GalleryController())->delete();
            return;
        }


        if ($uri === 'videos') {
            (new VideoController())->index();
            return;
        }

        if ($uri === 'videos/streams') {
            (new VideoController())->streams();
            return;
        }

        if ($uri === 'videos/upload' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            (new VideoController())->uploadForm();
            return;
        }

        if ($uri === 'videos/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new VideoController())->upload();
            return;
        }

        if ($uri === 'videos/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            (new VideoController())->delete();
            return;
        }


        if ($uri === 'playlisten') {
            (new PlaylistController())->index();
            return;
        }

        if ($uri === 'hardware') {
            (new HardwareController())->index();
            return;
        }

        if ($uri === 'live') {
            (new LiveController())->index();
            return;
        }

        if ($uri === 'partner') {
            (new PartnerController())->index();
            return;
        }

       /* =========================================================
           ADMIN (GESCHÜTZT)
        ========================================================= */
        if (str_starts_with($uri, 'admin')) {
        
            // TEAM-ZUGRIFF (Support, Moderator, Admin, Superadmin, Owner)
            $role = $_SESSION['user']['role'] ?? '';
            if (
                empty($_SESSION['user']) ||
                !in_array($role, ['support', 'moderator', 'admin', 'superadmin', 'owner'], true)
            ) {
                http_response_code(403);
                View::render('errors/403', ['title' => 'Zugriff verweigert']);
                return;
            }


            if ($uri === 'admin') {
                (new AdminController())->index();
                return;
            }

            if ($uri === 'admin/twitch') {
                (new AdminTwitchController())->index();
                return;
            }

            /* =========================
               ADMIN – AUDIT LOG
            ========================= */

            if ($uri === 'admin/audit') {
                (new AdminAuditController())->index();
                return;
            }

            if (preg_match('#^admin/audit/view/([0-9]+)$#', $uri, $m)) {
                (new AdminAuditController())->view((int)$m[1]);
                return;
            }

            if (str_starts_with($uri, 'admin/audit')) {
                if (($_SESSION['user']['role_level'] ?? 0) < 100) {
                    http_response_code(403);
                    View::render('errors/403', ['title' => 'Zugriff verweigert']);
                    return;
                }
            }

            /* =========================
               ADMIN – USERS
            ========================= */

            if ($uri === 'admin/users') {
                (new AdminUsersController())->index();
                return;
            }

            if ($uri === 'admin/users/update-role' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminUsersController())->updateRole();
                return;
            }

            if ($uri === 'admin/users/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminUsersController())->delete();
                return;
            }
            
                if ($uri === 'admin/settings/favicon') {
                (new AdminFaviconController())->index();
                return;
            }
        
            if ($uri === 'admin/settings/favicon/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                Security::checkCsrf();
                (new AdminFaviconController())->upload();
                return;
            }

                if ($uri === 'admin/reports') {
            (new AdminReportsController())->index();
                return;
            }

            if ($uri === 'admin/appeals') {
                (new AdminAppealController())->index();
                return;
            }

            if (preg_match('#^admin/appeals/([0-9]+)/approve$#', $uri, $m)
                && $_SERVER['REQUEST_METHOD'] === 'POST') {
                Security::checkCsrf();
                (new AdminAppealController())->approve((int)$m[1]);
                return;
            }

            if (preg_match('#^admin/appeals/([0-9]+)/reject$#', $uri, $m)
                && $_SERVER['REQUEST_METHOD'] === 'POST') {
                Security::checkCsrf();
                (new AdminAppealController())->reject((int)$m[1]);
                return;
            }


            /* ===== USERS ===== */

            if ($uri === 'admin/users') {
                (new AdminUsersController())->index();
                return;
            }

            if ($uri === 'admin/users/update-role' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminUsersController())->updateRole();
                return;
            }

            if ($uri === 'admin/users/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminUsersController())->delete();
                return;
            }

            if ($uri === 'admin/locked-accounts') {
                (new AdminController())->lockedAccounts();
                return;
            }

            if ($uri === 'admin/unlock-user') {
                (new AdminController())->unlockUser();
                return;
            }

            /* ===== PARTNER ===== */

            if ($uri === 'admin/partners') {
                (new AdminPartnerController())->index();
                return;
            }

            if ($uri === 'admin/partners/create') {
                (new AdminPartnerController())->create();
                return;
            }


            if ($uri === 'admin/partners/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminPartnerController())->store();
                return;
            }

            if ($uri === 'admin/partners/toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminPartnerController())->toggle();
                return;
            }
            
            if ($uri === 'admin/partners/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminPartnerController())->delete();
                return;
            }


            /* ===== FORUM ===== */

            if ($uri === 'admin/forum') {
                (new AdminForumController())->index();  // Beispiel-Methode im AdminForumController
                return;
            }

            /* ===== THREAD STICKY TOGGLE ===== */
            if ($uri === 'admin/forum/toggle-sticky' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminForumController())->toggleSticky();
                return;
            }

            /* ===== THREAD LOCK TOGGLE ===== */
            if ($uri === 'admin/forum/toggle-lock' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminForumController())->toggleLock();
                return;
            }

            /* ===== THREAD LÖSCHEN ===== */
            if ($uri === 'admin/forum/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminForumController())->deleteThread();
                return;
            }

            /* ===== KATEGORIEN VERWALTEN ===== */
            if ($uri === 'admin/forum/categories') {
                (new AdminForumController())->categories();
                return;
            }

            if ($uri === 'admin/forum/categories/create') {
                (new AdminForumController())->createCategory();
                return;
            }

            if ($uri === 'admin/forum/categories/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminForumController())->storeCategory();
                return;
            }

            if (preg_match('#^admin/forum/categories/([0-9]+)/edit$#', $uri, $m)) {
                (new AdminForumController())->editCategory((int)$m[1]);
                return;
            }

            if ($uri === 'admin/forum/categories/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminForumController())->updateCategory();
                return;
            }

            if ($uri === 'admin/forum/categories/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminForumController())->deleteCategory();
                return;
            }

            /* ===== GALERIE ===== */

            if ($uri === 'admin/gallery') {
                (new AdminGalleryController())->index();
                return;
            }

            /* ===== VIDEOS ===== */

            if ($uri === 'admin/videos') {
                (new AdminVideosController())->index();
                return;
            }

            if ($uri === 'admin/videos/create') {
                (new AdminVideosController())->create();
                return;
            }

            if ($uri === 'admin/videos/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminVideosController())->store();
                return;
            }

            /* ===== PLAYLISTS ===== */

            if ($uri === 'admin/playlists') {
                (new AdminPlaylistController())->index();
                return;
            }

            if ($uri === 'admin/playlists/create') {
                (new AdminPlaylistController())->create();
                return;
            }
            
            if ($uri === 'admin/playlists/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminPlaylistController())->store();
                return;
            }
            
            if ($uri === 'admin/playlists/toggle' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminPlaylistController())->toggle();
                return;
            }
            
            if ($uri === 'admin/playlists/delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminPlaylistController())->delete();
                return;
            }

            /* ===== HARDWARE ===== */
            if ($uri === 'admin/dashboard/moderation') {
                (new AdminDashboardController())->moderation();
                return;
            }


            if ($uri === 'admin/hardware') {
                (new AdminHardwareController())->index();
                return;
            }

            if ($uri === 'admin/cms') { 
                (new AdminCmsController())->index();
                 return; } 
                 
            if ($uri === 'admin/agb') { 
                header('Location: /admin/agb/edit');
                 exit; } 
                 
            if ($uri === 'admin/agb/edit') {
                 (new AdminAgbController())->edit();
                  return; } 
                  
            if ( $uri === 'admin/agb/update' && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
                 (new AdminAgbController())->update(); return; 
                
            } 
            
            if ($uri === 'admin/agb/consents') {
                 (new AdminAgbConsentController())->index();
                 return; 
                
            }

            if ($uri === 'admin/reports') { 
                (new AdminReportsController())->index();
                 return; 
            }

            if ($uri === 'admin/games') {
                (new AdminGamesController())->index();
                return;
            }

            if ($uri === 'admin/games/confirm' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminGamesController())->confirm();
                return;
            }

            if ($uri === 'admin/games') {
                (new AdminGamesController())->index();
                return;
            }

            if (preg_match('#^admin/games/edit/([0-9]+)$#', $uri, $m)) {
                (new AdminGamesController())->edit((int)$m[1]);
                return;
            }

            if ($uri === 'admin/games/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminGamesController())->update();
                return;
            }

            if ($uri === 'admin/games/add-alias' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminGamesController())->addAlias();
                return;
            }

            /* ===== FAVICON (OWNER ONLY) ===== */

            if ($uri === 'admin/settings/favicon') {
                (new AdminFaviconController())->index();
                return;
            }

            if ($uri === 'admin/settings/favicon/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                (new AdminFaviconController())->upload();
                return;
            }

            /* ===== MODERATION DASHBOARD ===== */

            if ($uri === 'admin/dashboard/moderation') {
                (new AdminDashboardController())->moderation();
                return;
            }

            /* ===== APPEALS ===== */

            if ($uri === 'admin/appeals') {
                (new AdminAppealController())->index();
                return;
            }

            if (
                preg_match('#^admin/appeals/([0-9]+)/approve$#', $uri, $m)
                && $_SERVER['REQUEST_METHOD'] === 'POST'
            ) {
                (new AdminAppealController())->approve((int)$m[1]);
                return;
            }

            if (
                preg_match('#^admin/appeals/([0-9]+)/reject$#', $uri, $m)
                && $_SERVER['REQUEST_METHOD'] === 'POST'
            ) {
                (new AdminAppealController())->reject((int)$m[1]);
                return;
            }

            if ($uri === 'admin/mail-logs') {
                (new AdminMailLogController())->index();
                return;
            }

            /* =========================================================
               API – MODERATION STATS (AJAX)
               URL: /api/moderation_stats
            ========================================================= */

            if ($uri === 'api/moderation_stats') {
                Security::requireTeam();
                require BASE_PATH . '/public/api/moderation_stats.php';
                return;
            }

            /* =========================================================
               CRON – MODERATION CLEANUP
               URL: /_cron/moderation-cleanup?token=XYZ
            ========================================================= */

            if ($uri === '_cron/moderation-cleanup') {
            
                if (php_sapi_name() !== 'cli') {
                    if (($_GET['token'] ?? '') !== $_ENV['CRON_TOKEN']) {
                        http_response_code(403);
                        exit('Forbidden');
                    }
                }
            
                require BASE_PATH . '/_cron/moderation_cleanup.php';
                return;
            }


            http_response_code(404);
            View::render('errors/404', ['title' => 'Admin-Seite nicht gefunden']);
            return;
        }

        /* =========================================================
           GLOBAL 404
        ========================================================= */

        http_response_code(404);
        View::render('errors/404', ['title' => 'Seite nicht gefunden']);
    }
}
