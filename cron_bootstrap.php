<?php
declare(strict_types=1);

/* =========================
   SICHERHEIT
========================= */
if (($_GET['token'] ?? '') !== $_ENV['CRON_TOKEN']) {
    http_response_code(403);
    exit;
}

/* =========================
   BOOTSTRAP (OHNE ROUTER)
========================= */
require __DIR__ . '/../cron_bootstrap.php';

/* =========================
   LOGIK
========================= */
Database::execute(
    "UPDATE user_actions
     SET active = 0
     WHERE active = 1
     AND expires_at IS NOT NULL
     AND expires_at <= NOW()"
);

echo 'CRON DONE';
