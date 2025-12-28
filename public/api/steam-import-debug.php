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
$cronToken = env('CRON_TOKEN');

// Fallback wenn env nicht geladen wurde
if (empty($cronToken)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'CRON_TOKEN not configured in .env file'
    ], JSON_PRETTY_PRINT);
    exit;
}

if ($token === '' || $token !== $cronToken) {
    http_response_code(403);
    echo json_encode([
        'status' => 'forbidden',
        'message' => 'Invalid or missing cron token'
    ], JSON_PRETTY_PRINT);
    exit;
}

/* =========================================================
   🚀 STEAM IMPORT
========================================================= */
try {
    require_once BASE_PATH . '/app/Services/SteamImportService.php';
    require_once BASE_PATH . '/app/Core/Database.php';

    $service = new SteamImportService();
    $result  = $service->import();

    http_response_code(200);
    echo json_encode([
        'status'   => 'success',
        'data'     => $result
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage(),
        'file'    => $e->getFile(),
        'line'    => $e->getLine()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
