<?php
declare(strict_types=1);

// Bootstrap
require_once __DIR__ . '/../../config/bootstrap.php';

// Nur für Admins
if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die('Admin only');
}

echo "<h1>🔍 Cron Debug</h1>";

// ==============================
// 1. PHP-Fehlerausgabe aktivieren
// ==============================
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h2>1️⃣ PHP-Konfiguration</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Error Reporting: " . error_reporting() . "</p>";

// ==============================
// 2. Pfade prüfen
// ==============================
echo "<h2>2️⃣ Pfade</h2>";
echo "<p>BASE_PATH: " . (defined('BASE_PATH') ? BASE_PATH : '❌ NICHT DEFINIERT') . "</p>";
echo "<p>Cron-Datei: " . __DIR__ . '/../cron/twitch-import.php</p>';
echo "<p>Cron existiert? " . (file_exists(__DIR__ . '/../cron/twitch-import.php') ? '✅ Ja' : '❌ Nein') . "</p>";

// ==============================
// 3. TwitchAutoImport-Klasse prüfen
// ==============================
echo "<h2>3️⃣ TwitchAutoImport-Klasse</h2>";
echo "<p>Klasse existiert? " . (class_exists('TwitchAutoImport') ? '✅ Ja' : '❌ Nein') . "</p>";

// ==============================
// 4. Twitch Service prüfen
// ==============================
echo "<h2>4️⃣ Twitch Service</h2>";
try {
    $twitch = new TwitchService();
    echo "<p>✅ TwitchService initialisiert</p>";
    
    $vods = $twitch->getVods(5);
    echo "<p>VODs abgerufen: " . count($vods) . "</p>";
    echo "<pre>" . print_r(array_slice($vods, 0, 2), true) . "</pre>";
    
} catch (Throwable $e) {
    echo "<p>❌ Fehler: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// ==============================
// 5. Database-Verbindung prüfen
// ==============================
echo "<h2>5️⃣ Datenbank</h2>";
try {
    $test = Database::fetch("SELECT COUNT(*) as c FROM videos");
    echo "<p>✅ Database verbunden</p>";
    echo "<p>Videos in DB: " . ($test['c'] ?? 0) . "</p>";
} catch (Throwable $e) {
    echo "<p>❌ Fehler: " . $e->getMessage() . "</p>";
}

// ==============================
// 6. Import manuell ausführen
// ==============================
echo "<h2>6️⃣ Manueller Import</h2>";

if (isset($_GET['run'])) {
    try {
        echo "<p>🚀 Import wird ausgeführt...</p>";
        
        $importer = new TwitchAutoImport();
        $importer->run();
        
        echo "<p>✅ Import erfolgreich abgeschlossen!</p>";
        
    } catch (Throwable $e) {
        echo "<p>❌ Fehler beim Import:</p>";
        echo "<p><strong>" . $e->getMessage() . "</strong></p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo '<a href="?run=1" style="padding: 10px 20px; background: #9146ff; color: white; text-decoration: none; border-radius: 5px;">▶️ Import JETZT ausführen</a>';
}

// ==============================
// 7. Lock-File prüfen
// ==============================
echo "<h2>7️⃣ Lock-File</h2>";
$lockFile = BASE_PATH . '/storage/cache/twitch_auto_import.json';
echo "<p>Lock-Datei: $lockFile</p>";

if (file_exists($lockFile)) {
    $lock = json_decode(file_get_contents($lockFile), true);
    echo "<p>✅ Lock existiert</p>";
    echo "<p>Letzter Lauf: " . date('Y-m-d H:i:s', $lock['time'] ?? 0) . "</p>";
    echo "<p>Nächster Lauf: " . date('Y-m-d H:i:s', ($lock['time'] ?? 0) + 300) . "</p>";
    
    if (isset($_GET['unlock'])) {
        unlink($lockFile);
        echo "<p>🔓 Lock gelöscht! <a href='?'>Neu laden</a></p>";
    } else {
        echo '<a href="?unlock=1" style="padding: 5px 10px; background: #ff4444; color: white; text-decoration: none; border-radius: 3px; font-size: 12px;">🔓 Lock löschen</a>';
    }
} else {
    echo "<p>⚠️ Kein Lock vorhanden</p>";
}

// ==============================
// 8. Logs anzeigen
// ==============================
echo "<h2>8️⃣ Import-Logs</h2>";
$logFile = BASE_PATH . '/storage/logs/twitch-import.log';

if (file_exists($logFile)) {
    $lines = file($logFile);
    $recent = array_slice($lines, -20);
    echo "<pre style='background: #1a1a1a; color: #00ff00; padding: 15px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars(implode('', $recent));
    echo "</pre>";
} else {
    echo "<p>⚠️ Keine Logs vorhanden</p>";
}