<?php
declare(strict_types=1);

/* =========================================================
   FAILSAFE
========================================================= */
ini_set('display_errors', '0');
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate, max-age=0');

/* =========================================================
   BASE PATH
========================================================= */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 2));
}

try {
    /* =====================================================
       SERVICES LADEN
    ===================================================== */
    require_once BASE_PATH . '/app/Core/Database.php';
    require_once BASE_PATH . '/app/Services/TwitchService.php';

    $twitch = new TwitchService();
    $stream = $twitch->getLiveStream();

    echo json_encode([
        'live'         => (bool)($stream['live'] ?? false),
        'title'        => $stream['title'] ?? null,
        'game_name'    => $stream['game_name'] ?? null,
        'viewer_count' => (int)($stream['viewer_count'] ?? 0),
        'timestamp'    => time()
    ], JSON_THROW_ON_ERROR);

} catch (Throwable $e) {
    echo json_encode([
        'live'      => false,
        'timestamp' => time()
    ]);
}

exit;
