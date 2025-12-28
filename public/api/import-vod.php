<?php
declare(strict_types=1);

/* =========================================================
   FAIL-SAFE SETTINGS
========================================================= */
ini_set('display_errors', '0');
error_reporting(0);

header('Content-Type: application/json');

/* =========================================================
   BASE PATH
========================================================= */
define('BASE_PATH', dirname(__DIR__));

/* =========================================================
   LOAD CORE
========================================================= */
require_once BASE_PATH . '/app/Core/Autoload.php';
require_once BASE_PATH . '/app/Core/Database.php';

/* =========================================================
   READ JSON INPUT
========================================================= */
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data || !is_array($data)) {
    exit(json_encode(['error' => 'invalid_json']));
}

/* =========================================================
   AUTH (BOT TOKEN)
========================================================= */
$BOT_TOKEN = 'a7c1f4b2e9d84c3a9b6e7f0d1c2a4e8b9f3a2c6d1e0f9a8b7c6d5e4f3a2b1';

if (($data['token'] ?? '') !== $BOT_TOKEN) {
    http_response_code(403);
    exit(json_encode(['error' => 'forbidden']));
}

/* =========================================================
   REQUIRED FIELDS
========================================================= */
$required = [
    'twitch_video_id',
    'title',
    'embed_url',
    'thumbnail',
    'published_at'
];

foreach ($required as $field) {
    if (empty($data[$field])) {
        exit(json_encode([
            'error' => 'missing_field',
            'field' => $field
        ]));
    }
}

/* =========================================================
   DUPLICATE CHECK
========================================================= */
$exists = Database::fetch(
    "SELECT id FROM videos WHERE twitch_video_id = ?",
    [$data['twitch_video_id']]
);

if ($exists) {
    exit(json_encode(['status' => 'already_exists']));
}

/* =========================================================
   OPTIONAL DEFAULTS
========================================================= */
$userId     = 1;   // Admin/User-ID (WICHTIG bei NOT NULL)
$categoryId = null;

$duration = (int)($data['duration_seconds'] ?? 0);
$views    = (int)($data['view_count'] ?? 0);

/* =========================================================
   INSERT
========================================================= */
try {
    Database::execute(
        "INSERT INTO videos
            (user_id, title, url, thumbnail, source, twitch_video_id,
             duration_seconds, view_count, is_pinned,
             published_at, created_at)
         VALUES
            (?, ?, ?, ?, 'twitch', ?, ?, ?, 1, ?, NOW())",
        [
            $userId,
            $data['title'],
            $data['embed_url'],
            $data['thumbnail'],
            $data['twitch_video_id'],
            $duration,
            $views,
            date('Y-m-d H:i:s', strtotime($data['published_at']))
        ]
    );

    echo json_encode(['status' => 'imported']);

} catch (Throwable $e) {

    // niemals 500 an den Bot geben
    echo json_encode([
        'error' => 'db_error'
        // 'debug' => $e->getMessage() // nur bei Bedarf
    ]);
}
