<?php
declare(strict_types=1);

/**
 * ContactController
 * Engels811 Network
 * 
 * Verarbeitet das Kontaktformular
 */

class ContactController
{
    /* =====================================================
       FORMULAR ANZEIGEN
    ===================================================== */
    public function showForm(): void
    {
        View::render('contact/form', [
            'title' => 'Kontakt'
        ]);
    }

    /* =====================================================
       FORMULAR VERARBEITEN
    ===================================================== */
    public function submitForm(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        // -------------------------------------------------
        // Honeypot (Spam-Schutz)
        // -------------------------------------------------
        if (!empty($_POST['website'] ?? '')) {
            http_response_code(200);
            return;
        }

        // -------------------------------------------------
        // Eingaben
        // -------------------------------------------------
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $_SESSION['form_data'] = [
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
            'message' => $message
        ];

        // -------------------------------------------------
        // Validierung
        // -------------------------------------------------
        if ($name === '' || $email === '' || $subject === '' || $message === '') {
            $_SESSION['flash_error'] = 'Bitte alle Felder vollständig ausfüllen.';
            header('Location: /kontakt');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Bitte eine gültige E-Mail-Adresse angeben.';
            header('Location: /kontakt');
            exit;
        }

        // -------------------------------------------------
        // Mailversand über MailService
        // -------------------------------------------------
        $fullMessage = "Betreff: {$subject}\n\n{$message}";
        
        $sent = MailService::sendContact($name, $email, $fullMessage);

        if (!$sent) {
            $_SESSION['flash_error'] = 'Die Nachricht konnte nicht gesendet werden. Bitte versuche es später erneut.';
            header('Location: /kontakt');
            exit;
        }

        // -------------------------------------------------
        // Erfolg
        // -------------------------------------------------
        unset($_SESSION['form_data']);
        $_SESSION['flash_success'] = 'Vielen Dank! Deine Nachricht wurde erfolgreich gesendet.';
        header('Location: /kontakt');
        exit;
    }
}