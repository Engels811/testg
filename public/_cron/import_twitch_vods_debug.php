<?php
/**
 * DEBUG VERSION – TWITCH VOD IMPORT
 */

// Fehlerausgabe IMMER aktivieren
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>🎮 Twitch VOD Import Debug</h1>";

// =========================================
// 1. BOOTSTRAP TESTEN
// =========================================
echo "<h2>1️⃣ Bootstrap</h2>";

// Pfade angepasst für /public/_cron/
$databasePath = __DIR__ . '/../../app/Core/Database.php';
$twitchPath = __DIR__ . '/../../app/Services/TwitchService.php';

echo "<p>Database.php: " . ($databasePath) . "</p>";
echo "<p>Existiert? " . (file_exists($databasePath) ? '✅ Ja' : '❌ Nein') . "</p>";

echo "<p>TwitchService.php: " . ($twitchPath) . "</p>";
echo "<p>Existiert? " . (file_exists($twitchPath) ? '✅ Ja' : '❌ Nein') . "</p>";

if (!file_exists($databasePath) || !file_exists($twitchPath)) {
    die("<p style='color:red;'>❌ Bootstrap-Dateien nicht gefunden!</p>");
}

require_once $databasePath;
require_once $twitchPath;

echo "<p>✅ Bootstrap erfolgreich geladen</p>";

// =========================================
// 2. TOKEN PRÜFEN
// =========================================
echo "<h2>2️⃣ Token</h2>";

$token = $_GET['token'] ?? '';
$expectedToken = 'cm0q9af199fhr4f0hyctndjbh95m1x';

echo "<p>Empfangener Token: <code>$token</code></p>";
echo "<p>Erwarteter Token: <code>$expectedToken</code></p>";
echo "<p>Token gültig? " . ($token === $expectedToken ? '✅ Ja' : '❌ Nein') . "</p>";

if ($token !== $expectedToken) {
    die("<p style='color:red;'>❌ Token ungültig!</p>");
}

// =========================================
// 3. TWITCH SERVICE TESTEN
// =========================================
echo "<h2>3️⃣ Twitch Service</h2>";

try {
    $twitch = new TwitchService();
    echo "<p>✅ TwitchService initialisiert</p>";
    
    $vods = $twitch->getVods(5);
    echo "<p>VODs abgerufen: <strong>" . count($vods) . "</strong></p>";
    
    if (empty($vods)) {
        echo "<p style='color:orange;'>⚠️ Keine VODs von Twitch erhalten</p>";
        echo "<p>Mögliche Gründe:</p>";
        echo "<ul>";
        echo "<li>Twitch API Credentials falsch</li>";
        echo "<li>Keine VODs verfügbar</li>";
        echo "<li>Channel offline/keine Streams</li>";
        echo "</ul>";
    } else {
        echo "<h3>Erste 2 VODs:</h3>";
        echo "<pre>" . print_r(array_slice($vods, 0, 2), true) . "</pre>";
    }
    
} catch (Throwable $e) {
    echo "<p style='color:red;'>❌ Fehler beim Twitch Service:</p>";
    echo "<p><strong>" . $e->getMessage() . "</strong></p>";
    echo "<p>Datei: " . $e->getFile() . ":" . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

// =========================================
// 4. DATENBANK TESTEN
// =========================================
echo "<h2>4️⃣ Datenbank</h2>";

try {
    $result = Database::fetch("SELECT COUNT(*) as c FROM videos");
    echo "<p>✅ Datenbankverbindung OK</p>";
    echo "<p>Videos in DB: <strong>" . ($result['c'] ?? 0) . "</strong></p>";
    
    $twitchVideos = Database::fetch("SELECT COUNT(*) as c FROM videos WHERE source = 'twitch'");
    echo "<p>Twitch-Videos: <strong>" . ($twitchVideos['c'] ?? 0) . "</strong></p>";
    
} catch (Throwable $e) {
    echo "<p style='color:red;'>❌ Datenbank-Fehler:</p>";
    echo "<p><strong>" . $e->getMessage() . "</strong></p>";
    die();
}

// =========================================
// 5. IMPORT SIMULIEREN
// =========================================
echo "<h2>5️⃣ Import-Simulation</h2>";

if (empty($vods)) {
    echo "<p style='color:orange;'>⚠️ Keine VODs zum Importieren</p>";
    die();
}

$imported = 0;
$skipped = 0;
$errors = [];

foreach ($vods as $vod) {
    
    echo "<hr>";
    echo "<h4>VOD: {$vod['title']}</h4>";
    echo "<p>ID: {$vod['id']}</p>";
    
    // Prüfen ob bereits vorhanden
    try {
        $exists = Database::fetch(
            "SELECT id FROM videos WHERE twitch_video_id = ?",
            [$vod['id']]
        );
        
        if ($exists) {
            echo "<p>⏭️ Bereits vorhanden (ID: {$exists['id']})</p>";
            $skipped++;
            continue;
        }
        
        echo "<p>✅ Noch nicht vorhanden → Wird importiert</p>";
        
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
        $embedUrl = 'https://player.twitch.tv/?video=' . $vod['id'] . '&parent=engels811-ttv.de';
        
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
        
        echo "<p style='color:green;'>✅ Erfolgreich importiert</p>";
        $imported++;
        
    } catch (Throwable $e) {
        echo "<p style='color:red;'>❌ Fehler beim Importieren:</p>";
        echo "<p>" . $e->getMessage() . "</p>";
        $errors[] = $vod['id'] . ": " . $e->getMessage();
    }
}

// =========================================
// 6. ZUSAMMENFASSUNG
// =========================================
echo "<hr>";
echo "<h2>✅ Import abgeschlossen</h2>";
echo "<p><strong>Neu importiert:</strong> $imported</p>";
echo "<p><strong>Übersprungen:</strong> $skipped</p>";
echo "<p><strong>Fehler:</strong> " . count($errors) . "</p>";

if (!empty($errors)) {
    echo "<h3>Fehler-Details:</h3>";
    echo "<pre>" . implode("\n", $errors) . "</pre>";
}