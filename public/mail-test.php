<?php
// Test-Datei - NACH TEST LÖSCHEN!

require_once __DIR__ . '/TTVMailer.php';

echo "<h1>📧 Mail-System Test</h1>";

try {
    $mailer = new TTVMailer();
    
    // Debug-Info anzeigen
    echo "<h3>Konfiguration:</h3>";
    echo "<pre>";
    print_r($mailer->getDebugInfo());
    echo "</pre>";
    
    // Test-Mail senden (ÄNDERE DIE E-MAIL!)
    $testEmail = 'engels811@gmail.com'; // ⚠️ HIER DEINE E-MAIL EINTRAGEN!
    
    echo "<h3>Sende Test-Mail an: {$testEmail}</h3>";
    
    $result = $mailer->send(
        $testEmail,
        'Test: TTVMailer Integration',
        '<h2>Test erfolgreich! ✅</h2><p>TTVMailer funktioniert mit deinem MailService!</p>'
    );
    
    if ($result['success']) {
        echo "<div style='background:#d4edda;padding:20px;border-radius:8px;color:#155724;'>";
        echo "<h2>✅ ERFOLG!</h2>";
        echo "<p>Test-Mail wurde versendet. Prüfe dein Postfach!</p>";
        echo "</div>";
    } else {
        echo "<div style='background:#f8d7da;padding:20px;border-radius:8px;color:#721c24;'>";
        echo "<h2>❌ FEHLER</h2>";
        echo "<p>Fehler: " . ($result['error'] ?? 'Unbekannt') . "</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background:#f8d7da;padding:20px;border-radius:8px;color:#721c24;'>";
    echo "<h2>❌ EXCEPTION</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>⚠️ WICHTIG:</strong> Lösche diese Test-Datei nach dem Test!</p>";
?>