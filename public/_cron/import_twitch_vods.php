<?php
/**
 * TWITCH VOD IMPORT (PRODUKTIV)
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Bootstrap
require_once __DIR__ . '/../../app/Core/Database.php';
require_once __DIR__ . '/../../app/Services/TwitchService.php';

// Log-Funktion
function logImport(string $msg, bool $isError = false): void
{
    $logDir = __DIR__ . '/../../storage/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/twitch-import.log';
    $timestamp = date('Y-m-d H:i:s');
    $prefix = $isError ? '❌' : '✅';
    
    file_put_contents(
        $logFile,
        "[{$timestamp}] {$prefix} {$msg}\n",
        FILE_APPEND
    );
}

// Token-Check
$token = $_GET['token'] ?? '';

if ($token !== 'cm0q9af199fhr4f0hyctndjbh95m1x') {
    logImport("Ungültiger Token-Versuch: $token", true);
    http_response_code(403);
    exit('Forbidden');
}

logImport("Import gestartet");

try {
    $twitch = new TwitchService();
    $vods   = $twitch->getVods(25);

    if (empty($vods)) {
        logImport("Keine VODs von Twitch erhalten");
        echo "Keine VODs gefunden";
        exit;
    }

    logImport("VODs von Twitch erhalten: " . count($vods));

    $imported = 0;
    $skipped = 0;

    foreach ($vods as $vod) {

        // Duplikat prüfen
        $exists = Database::fetch(
            "SELECT id FROM videos WHERE twitch_video_id = ?",
            [$vod['id']]
        );

        if ($exists) {
            $skipped++;
            continue;
        }

        // Thumbnail
        $thumbnail = '';
        if (!empty($vod['thumbnail_url'])) {
            $thumbnail = str_replace(
                ['%{width}', '%{height}'],
                ['640', '360'],
                $vod['thumbnail_url']
            );
        }

        // Embed URL
        $embedUrl = 'https://player.twitch.tv/?video=' . $vod['id'] .
                    '&parent=engels811-ttv.de';

        // In DB schreiben
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
        logImport("VOD importiert: {$vod['title']} (ID: {$vod['id']})");
    }

    $message = "Import abgeschlossen – {$imported} neu, {$skipped} übersprungen";
    logImport($message);
    
    echo "OK – {$message}";

} catch (Throwable $e) {
    $error = "Fehler: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
    logImport($error, true);
    
    http_response_code(500);
    echo "ERROR – " . $e->getMessage();
}