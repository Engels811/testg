<?php
declare(strict_types=1);

/* =========================
   SECURITY
========================= */
if (($_GET['token'] ?? '') !== $_ENV['CRON_TOKEN']) {
    http_response_code(403);
    exit;
}

/* =========================
   BOOTSTRAP (OHNE ROUTER)
========================= */
$start = microtime(true);
require __DIR__ . '/../cron_bootstrap.php';

try {

    // Cleanup
    Database::execute(
        "UPDATE user_actions
         SET active = 0
         WHERE active = 1
         AND expires_at IS NOT NULL
         AND expires_at <= NOW()"
    );

    $runtime = (int)((microtime(true) - $start) * 1000);

    CronLogger::log(
        'moderation_cleanup',
        'success',
        'Expired actions cleaned',
        $runtime
    );

    echo 'CRON DONE';

} catch (Throwable $e) {

    CronLogger::log(
        'moderation_cleanup',
        'error',
        $e->getMessage()
    );

    http_response_code(500);
    echo 'CRON ERROR';
}
