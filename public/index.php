<?php
declare(strict_types=1);

/* =========================================================
   ERROR REPORTING (ENV-gesteuert)
========================================================= */
error_reporting(E_ALL);

$debug = getenv('APP_DEBUG') === 'true';
ini_set('display_errors', $debug ? '1' : '0');

/* =========================================================
   SESSION (zentral, nur einmal)
========================================================= */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* =========================================================
   BASIS-PFADE
========================================================= */
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

/* =========================================================
   AUTOLOADER (MUSS VOR ALLEM ANDEREN)
========================================================= */
require BASE_PATH . '/app/Core/Autoload.php';

/* =========================================================
   ENV LADEN (.env liegt im Projekt-Root)
========================================================= */
require BASE_PATH . '/app/Core/Env.php';
Env::load(BASE_PATH . '/.env');

/* =========================================================
   🔄 AUTO-IMPORTER (SILENT / NON-BLOCKING)
========================================================= */

// Twitch
if (is_file(BASE_PATH . '/app/Services/TwitchAutoImport.php')) {
    require_once BASE_PATH . '/app/Services/TwitchAutoImport.php';
    try {
        (new TwitchAutoImport())->run();
    } catch (Throwable $e) {
        // optional: Logger::warning($e->getMessage());
    }
}

// Steam
if (is_file(BASE_PATH . '/app/Services/SteamImportService.php')) {
    require_once BASE_PATH . '/app/Services/SteamImportService.php';
    try {
        (new SteamImportService())->import();
    } catch (Throwable $e) {
        // optional: Logger::warning($e->getMessage());
    }
}

/* =========================================================
   🔐 AGB-GUARD
========================================================= */
if (class_exists('AgbGuard')) {
    AgbGuard::check();
}

/* =========================================================
   🚀 APP START
========================================================= */
$app = new App();
$app->run();
