<?php
/**
 * ENGELS811 NETWORK – TWITCH VOD IMPORT
 * FINAL (HTTPDOCS-WEBROOT)
 */

/* =========================================================
   ERROR OUTPUT (NUR ZUM TESTEN!)
========================================================= */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* =========================================================
   BOOTSTRAP
========================================================= */

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Services/TwitchService.php';

/* =========================================================
   SICHERHEIT
========================================================= */

$token = $_GET['token'] ?? '';
if ($token !== 'cm0q9af199fhr4f0hyctndjbh95m1x') {
    http_response_code(403);
    exit('Forbidden');
}

echo "BOOT OK<br>";

/* =========================================================
   IMPORT
========================================================= */

$twitch = new TwitchService();
$vods   = $twitch->getVods(10);

if (empty($vods)) {
    exit('Keine VODs gefunden');
}

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
            (title, url, thumbnail, source, twitch_video_id, published_at, created_at)
         VALUES
            (?, ?, ?, 'twitch', ?, ?, NOW())",
        [
            $vod['title'],
            $embedUrl,
            $thumbnail,
            $vod['id'],
            date('Y-m-d H:i:s', strtotime($vod['created_at']))
        ]
    );

    $imported++;
}

echo "OK – {$imported} Twitch-VOD(s) importiert";
