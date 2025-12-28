<?php
declare(strict_types=1);

/* =========================================================
   BASIS-PFAD
========================================================= */
define('BASE_PATH', dirname(__DIR__, 2));

/* =========================================================
   AUTOLOADER + ENV
========================================================= */
require BASE_PATH . '/app/Core/Autoload.php';
require BASE_PATH . '/app/Core/Env.php';

Env::load(BASE_PATH . '/.env');

/* =========================================================
   RESPONSE
========================================================= */
header('Content-Type: application/json; charset=utf-8');

/* =========================================================
   🔐 TOKEN-SCHUTZ (ABSOLUT WICHTIG)
========================================================= */
$token = trim($_GET['token'] ?? '');

if ($token === '' || $token !== env('CRON_TOKEN')) {
    http_response_code(403);
    echo json_encode([
        'status' => 'forbidden',
        'message' => 'Invalid cron token'
    ], JSON_PRETTY_PRINT);
    exit;
}

/* =========================================================
   🚀 STEAM IMPORT
========================================================= */
try {
    require_once BASE_PATH . '/app/Services/SteamImportService.php';

    $service = new SteamImportService();
    $result  = $service->import();

    echo json_encode([
        'status'   => 'ok',
        'imported' => $result
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
