<?php
declare(strict_types=1);

/**
 * MailService
 * Engels811 Network
 * 
 * PHPMailer-basierter Mail-Versand mit Logging
 * HOTFIX-Version mit absoluter Type-Safety
 */

/* =========================================================
   PHPMailer – MANUELL EINBINDEN (KEIN COMPOSER)
========================================================= */

require_once BASE_PATH . '/vendor/PHPMailer/PHPMailer/src/Exception.php';
require_once BASE_PATH . '/vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require_once BASE_PATH . '/vendor/PHPMailer/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService
{
    /* =========================================================
       🔥 SICHERER ENV-HELPER
    ========================================================= */

    private static function env(string $key, $default = null)
    {
        // Erst $_ENV, dann getenv()
        $value = $_ENV[$key] ?? null;
        
        if ($value === null) {
            $value = getenv($key);
        }
        
        // false, leerer String oder null = default nutzen
        if ($value === false || $value === '' || $value === null) {
            return $default;
        }
        
        return $value;
    }

    /* =========================================================
       ADMIN-MAIL MIT FALLBACK
    ========================================================= */

    private static function getAdminMail(): string
    {
        // 1. Versuch: ADMIN_MAIL aus .env
        $mail = self::env('ADMIN_MAIL');
        
        // 2. Versuch: MAIL_FROM_ADDRESS
        if (empty($mail)) {
            $mail = self::env('MAIL_FROM_ADDRESS');
        }
        
        // 3. Fallback: Hard-coded default
        if (empty($mail)) {
            $mail = 'kontakt@engels811-ttv.de';
        }
        
        // Type-Cast für absolute Sicherheit
        return (string) $mail;
    }

    /* =========================================================
       PUBLIC API
    ========================================================= */

    /* =========================
       KONTAKTFORMULAR
    ========================= */
    public static function sendContact(
        string $name,
        string $email,
        string $message
    ): bool {
        // 🔒 GARANTIERT ein String
        $adminMail = self::getAdminMail();
        
        // Betreff aus Nachricht extrahieren
        $subject = 'Neue Kontaktanfrage';
        if (preg_match('/^Betreff:\s*(.+?)$/m', $message, $matches)) {
            $subject = 'Kontaktformular: ' . trim($matches[1]);
        }

        // HTML-Version
        $html = self::buildContactHTML($name, $email, $message);
        
        // Text-Version
        $text = self::buildContactText($name, $email, $message);

        return self::sendAndLog(
            $adminMail,
            $subject,
            $html,
            $text,
            'contact',
            $email  // Reply-To
        );
    }

    /* =========================
       E-MAIL-ÄNDERUNG BESTÄTIGEN
    ========================= */
    public static function sendEmailChangeConfirmation(
        string $email,
        string $username,
        string $token
    ): bool {
        $confirmLink = self::env('APP_URL', 'https://engels811-ttv.de') . '/email/confirm?token=' . urlencode($token);

        return self::sendTemplate(
            $email,
            'E-Mail-Adresse bestätigen – Engels811 Network',
            'emails/email-change-confirm',
            [
                'confirmLink' => $confirmLink,
                'username'    => $username,
            ],
            'email_change_confirm'
        );
    }

    /* =========================
       REGISTRIERUNG BESTÄTIGEN
    ========================= */
    public static function sendRegistrationConfirmation(
        string $email,
        string $username,
        string $token
    ): bool {
        $confirmLink = self::env('APP_URL', 'https://engels811-ttv.de') . '/confirm-email?token=' . urlencode($token);

        return self::sendTemplate(
            $email,
            'Willkommen bei Engels811 Network – Bestätige deine E-Mail',
            'emails/registration-confirm',
            [
                'confirmLink' => $confirmLink,
                'username'    => $username,
            ],
            'registration_confirm'
        );
    }

    /* =========================
       PASSWORT RESET
    ========================= */
    public static function sendPasswordReset(
        string $email,
        string $username,
        string $token
    ): bool {
        $resetLink = self::env('APP_URL', 'https://engels811-ttv.de') . '/password/reset?token=' . urlencode($token);

        return self::sendTemplate(
            $email,
            'Passwort zurücksetzen – Engels811 Network',
            'emails/password-reset',
            [
                'resetLink' => $resetLink,
                'username'  => $username,
            ],
            'password_reset'
        );
    }

    /* =========================
       WILLKOMMEN
    ========================= */
    public static function sendWelcomeEmail(
        string $email,
        string $username
    ): bool {
        return self::sendTemplate(
            $email,
            '🎉 Willkommen im Engels811 Network!',
            'emails/welcome',
            [
                'username' => $username,
            ],
            'welcome'
        );
    }

    /* =========================
       LOGIN-BENACHRICHTIGUNG
    ========================= */
    public static function sendLoginNotification(
        string $email,
        string $username,
        string $ip,
        string $userAgent
    ): bool {
        return self::sendTemplate(
            $email,
            'Neuer Login in dein Konto – Engels811 Network',
            'emails/login-notification',
            [
                'username'  => $username,
                'ip'        => $ip,
                'userAgent' => $userAgent,
            ],
            'login_notification'
        );
    }

    /* =========================
       ACCOUNT GESPERRT
    ========================= */
    public static function sendSecurityLockMail(
        string $email,
        string $username
    ): bool {
        return self::sendTemplate(
            $email,
            'Sicherheitswarnung – Account gesperrt',
            'emails/account-locked',
            [
                'username' => $username,
            ],
            'account_locked'
        );
    }

    /* =========================
       ACCOUNT ENTSPERREN
    ========================= */
    public static function sendAccountUnlockMail(
        string $email,
        string $username,
        string $token
    ): bool {
        $link = self::env('APP_URL', 'https://engels811-ttv.de') . '/unlock-account?token=' . urlencode($token);

        return self::sendTemplate(
            $email,
            'Account entsperren – Engels811 Network',
            'emails/account-unlock',
            [
                'username' => $username,
                'link'     => $link,
            ],
            'account_unlock'
        );
    }

    /* =========================================================
       HTML BUILDER - KONTAKT
    ========================================================= */

    private static function buildContactHTML(string $name, string $email, string $message): string
    {
        return
            "<div style='font-family: Arial, sans-serif; padding: 20px; background: #1a1a1a; color: #fff;'>" .
            "<h2 style='color: #ff3333; border-bottom: 2px solid #ff3333; padding-bottom: 10px;'>Neue Kontaktanfrage</h2>" .
            "<table style='width: 100%; margin: 20px 0;'>" .
            "<tr><td style='padding: 8px; font-weight: bold; color: #ff3333;'>Name:</td><td style='padding: 8px;'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</td></tr>" .
            "<tr><td style='padding: 8px; font-weight: bold; color: #ff3333;'>E-Mail:</td><td style='padding: 8px;'>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</td></tr>" .
            "</table>" .
            "<div style='background: #2a2a2a; padding: 15px; border-left: 4px solid #ff3333; margin: 20px 0;'>" .
            "<strong style='color: #ff3333;'>Nachricht:</strong><br><br>" .
            nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) .
            "</div>" .
            "<hr style='border: none; border-top: 1px solid #333; margin: 20px 0;'>" .
            "<p style='color: #888; font-size: 12px;'>Diese Nachricht wurde über das Kontaktformular auf engels811-ttv.de gesendet.</p>" .
            "</div>";
    }

    /* =========================================================
       TEXT BUILDER - KONTAKT
    ========================================================= */

    private static function buildContactText(string $name, string $email, string $message): string
    {
        return
            "==============================================\n" .
            "NEUE KONTAKTANFRAGE - ENGELS811 NETWORK\n" .
            "==============================================\n\n" .
            "Name: {$name}\n" .
            "E-Mail: {$email}\n\n" .
            "Nachricht:\n" .
            "----------------------------------------------\n" .
            "{$message}\n" .
            "----------------------------------------------\n\n" .
            "Diese Nachricht wurde über das Kontaktformular auf engels811-ttv.de gesendet.";
    }

    /* =========================================================
       TEMPLATE SEND (HTML + TEXT)
    ========================================================= */

    private static function sendTemplate(
        string $to,
        string $subject,
        string $view,
        array $data,
        string $type
    ): bool {
        try {
            $html = View::renderPartial($view, $data);
            $text = View::renderPartial($view . '.txt', $data);
        } catch (Throwable $e) {
            error_log('[MailService] Template-Fehler: ' . $e->getMessage());
            $html = '<p>Eine Nachricht vom Engels811 Network.</p>';
            $text = 'Eine Nachricht vom Engels811 Network.';
        }

        return self::sendAndLog($to, $subject, $html, $text, $type);
    }

    /* =========================================================
       CORE SEND + LOG
    ========================================================= */

    private static function sendAndLog(
        string $to,
        string $subject,
        string $html,
        ?string $text,
        string $type,
        ?string $replyTo = null
    ): bool {
        try {
            self::sendWithPHPMailer($to, $subject, $html, $text, $replyTo);
            self::logMail($to, $subject, $type, 'sent', null);
            return true;
        } catch (Throwable $e) {
            self::logMail($to, $subject, $type, 'failed', $e->getMessage());
            error_log('[MailService] ' . $e->getMessage());
            return false;
        }
    }

    /* =========================================================
       PHPMailer SMTP
    ========================================================= */

    private static function sendWithPHPMailer(
        string $to,
        string $subject,
        string $html,
        ?string $text,
        ?string $replyTo = null
    ): void {

        $mail = new PHPMailer(true);

        // SMTP-Konfiguration
        $mail->isSMTP();
        $mail->Host       = self::env('MAIL_HOST', 'engels811-ttv.de');
        $mail->SMTPAuth   = true;
        $mail->Username   = self::env('MAIL_USERNAME', 'no-reply@engels811-ttv.de');
        $mail->Password   = self::env('MAIL_PASSWORD', '');
        $mail->Port       = (int) self::env('MAIL_PORT', 465);
        $mail->CharSet    = 'UTF-8';

        // SSL/TLS
        $encryption = self::env('MAIL_ENCRYPTION', 'ssl');
        $mail->SMTPSecure = ($encryption === 'tls') 
            ? PHPMailer::ENCRYPTION_STARTTLS 
            : PHPMailer::ENCRYPTION_SMTPS;

        // Debug (nur im Development)
        if (self::env('APP_ENV') === 'development') {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = fn($str) => error_log('[SMTP] ' . $str);
        }

        // Absender
        $mail->setFrom(
            self::env('MAIL_FROM_ADDRESS', 'no-reply@engels811-ttv.de'),
            self::env('MAIL_FROM_NAME', 'Engels811 Network')
        );

        // Reply-To
        if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $mail->addReplyTo($replyTo);
        } elseif ($defaultReplyTo = self::env('MAIL_REPLY_TO')) {
            $mail->addReplyTo($defaultReplyTo);
        }

        // Empfänger
        $mail->addAddress($to);

        // Inhalt
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->AltBody = $text ?: strip_tags($html);

        // Custom Headers
        $mail->addCustomHeader('X-Mailer', 'Engels811-MailService');
        $mail->addCustomHeader('X-Priority', '3');

        // Senden
        $mail->send();
    }

    /* =========================================================
       MAIL LOGGING
    ========================================================= */

    private static function logMail(
        string $email,
        string $subject,
        string $type,
        string $status,
        ?string $error
    ): void {
        try {
            Database::execute(
                'INSERT INTO mail_logs (email, subject, type, status, error_message, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())',
                [$email, $subject, $type, $status, $error]
            );
        } catch (Throwable $e) {
            error_log('[MailLog] Logging fehlgeschlagen: ' . $e->getMessage());
        }
    }

    /* =========================================================
       TEST-FUNKTIONEN
    ========================================================= */

    public static function testConnection(): array
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = self::env('MAIL_HOST', 'engels811-ttv.de');
            $mail->SMTPAuth   = true;
            $mail->Username   = self::env('MAIL_USERNAME', 'no-reply@engels811-ttv.de');
            $mail->Password   = self::env('MAIL_PASSWORD', '');
            $mail->Port       = (int) self::env('MAIL_PORT', 465);
            
            $encryption = self::env('MAIL_ENCRYPTION', 'ssl');
            $mail->SMTPSecure = ($encryption === 'tls')
                ? PHPMailer::ENCRYPTION_STARTTLS
                : PHPMailer::ENCRYPTION_SMTPS;

            $mail->smtpConnect();
            $mail->smtpClose();

            return [
                'success' => true,
                'message' => 'SMTP-Verbindung erfolgreich!'
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'SMTP-Fehler: ' . $e->getMessage()
            ];
        }
    }

    public static function sendTestMail(string $to): bool
    {
        $html = 
            "<div style='font-family: Arial, sans-serif; padding: 20px; background: #1a1a1a; color: #fff;'>" .
            "<h2 style='color: #ff3333;'>🔥 Test-Mail – Engels811 Network</h2>" .
            "<p>Diese Mail wurde erfolgreich über PHPMailer versendet!</p>" .
            "<p style='color: #888; font-size: 12px;'>Gesendet am: " . date('d.m.Y H:i:s') . "</p>" .
            "</div>";

        $text = 
            "TEST-MAIL - ENGELS811 NETWORK\n\n" .
            "Diese Mail wurde erfolgreich über PHPMailer versendet!\n\n" .
            "Gesendet am: " . date('d.m.Y H:i:s');

        return self::sendAndLog(
            $to,
            '🔥 Test-Mail – Engels811 Network',
            $html,
            $text,
            'test'
        );
    }
}