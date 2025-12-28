<?php
/**
 * Engels811 TTV - Mail Configuration
 * Tischtennis Website: engels811-ttv.de
 * 
 * WICHTIG: Diese Datei außerhalb des Web-Root speichern!
 * Empfohlen: /config/mail.config.php (außerhalb von public_html)
 */

return [
    // ====================================
    // SMTP SERVER EINSTELLUNGEN
    // Basierend auf: engels811-ttv.de
    // ====================================
    
    'smtp_host'     => 'engels811-ttv.de',         // SMTP Server
    'smtp_port'     => 465,                         // SMTP Port (SSL)
    'smtp_secure'   => 'ssl',                       // SSL Verschlüsselung
    'smtp_username' => 'no-reply@engels811-ttv.de', // Vollständige E-Mail
    'smtp_password' => 'Schatz@14102013',  // ⚠️ Passwort eintragen!
    
    // Alternative TLS-Konfiguration (falls SSL nicht funktioniert):
    // 'smtp_port'     => 587,
    // 'smtp_secure'   => 'tls',
    
    // ====================================
    // ABSENDER EINSTELLUNGEN
    // ====================================
    
    'from_email'    => 'no-reply@engels811-ttv.de',
    'from_name'     => 'Engels811',
    'reply_to'      => 'kontakt@engels811-ttv.de',  // Wo Empfänger antworten können
    
    // ====================================
    // ADMIN / KONTAKT
    // ====================================
    
    'admin_email'   => 'admin@engels811-ttv.de',
    'admin_name'    => 'Engels811',
    'contact_email' => 'kontakt@engels811-ttv.de',
    
    // ====================================
    // OPTIONALE EINSTELLUNGEN
    // ====================================
    
    'charset'       => 'UTF-8',
    'timeout'       => 30,                          // SMTP Timeout in Sekunden
    'debug'         => false,                       // true nur für Entwicklung!
    
    // ====================================
    // TEMPLATES
    // ====================================
    
    'templates_path' => __DIR__ . '/../templates/mail/',
    
    // ====================================
    // RATE LIMITING
    // ====================================
    
    'rate_limit' => [
        'enabled'       => true,
        'max_per_hour'  => 30,                      // Max 30 Mails pro Stunde
        'max_per_day'   => 100,                     // Max 100 Mails pro Tag
    ],
    
    // ====================================
    // BRANDING
    // ====================================
    
    'website_name'  => 'Engels811 Network',
    'website_url'   => 'https://engels811-ttv.de',
    'logo_url'      => 'https://i.ibb.co/ns1czZv9/Brennender-Wolf-und-Flammen-Sym33bole-removebg-preview.png',
    'banner_url'    => 'https://i.ibb.co/Y7zCgFFt/Chat-GPT-Image-27-Dez-2025-09-42-07.png',
];

/*
==============================================
KORREKTE EINSTELLUNGEN FÜR ENGELS811-TTV.DE
==============================================

Basierend auf deinen E-Mail-Client Einstellungen:

1. SMTP Server (Ausgehende Mails):
   - Host: engels811-ttv.de
   - Port: 465 (SSL)
   - Verschlüsselung: SSL
   - Authentifizierung: Erforderlich
   - Username: no-reply@engels811-ttv.de (vollständige E-Mail)
   - Passwort: Dein E-Mail Passwort

2. IMAP Server (Eingehende Mails):
   - Host: engels811-ttv.de
   - Port: 993 (SSL)

3. POP3 Server (Alternative):
   - Host: engels811-ttv.de
   - Port: 995 (SSL)

4. E-Mail Adressen bei deinem Hoster einrichten:
   - no-reply@engels811-ttv.de (für automatische Mails)
   - kontakt@engels811-ttv.de (für Anfragen)
   - admin@engels811-ttv.de (für Admin-Benachrichtigungen)

==============================================
WICHTIGE HINWEISE
==============================================

✅ Verwende die VOLLSTÄNDIGE E-Mail als Username
✅ Port 465 mit SSL (nicht TLS!)
✅ Server ist engels811-ttv.de (NICHT smtp.strato.de)
✅ Authentifizierung ist erforderlich

Falls Probleme auftreten:
- Prüfe ob E-Mail Adresse im Hosting-Panel existiert
- Teste mit einem E-Mail Client (Thunderbird, etc.)
- Aktiviere Debug-Modus in Config: 'debug' => true

==============================================
SICHERHEIT
==============================================

✅ Config außerhalb Web-Root
✅ .htaccess Schutz für Config-Ordner
✅ Starke Passwörter
✅ SSL Verschlüsselung
✅ Dateirechte: chmod 600 mail.config.php

*/
?>
