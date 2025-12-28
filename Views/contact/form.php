<?php
/**
 * Kontaktformular
 * Engels811 Network
 *
 * Erwartete Variablen:
 * @var string $title
 */

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'Kontakt', ENT_QUOTES, 'UTF-8') ?> – Engels811 Network</title>

    <!-- Kontaktformular Styles -->
    <link rel="stylesheet" href="/assets/css/form/contact-form.css">
</head>
<body>

<div class="container">

    <!-- =========================================
         HEADER
    ========================================= -->
    <header class="header header-with-logo">
        <img
            src="https://i.ibb.co/ns1czZv9/Brennender-Wolf-und-Flammen-Sym33bole-removebg-preview.png"
            alt="Engels811 Network Logo"
            class="contact-logo"
            loading="lazy"
        >
        <div class="header-text">
            <h1>Kontakt</h1>
            <p>Hast du Fragen? Schreib uns eine Nachricht.</p>
        </div>
    </header>

    <!-- =========================================
         FLASH MELDUNGEN
    ========================================= -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash flash-success">
            <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash flash-error">
            <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- =========================================
         KONTAKTFORMULAR
    ========================================= -->
    <form method="post" action="/kontakt" class="contact-form" novalidate>

        <div class="form-group">
            <label for="name">Name *</label>
            <input
                type="text"
                id="name"
                name="name"
                required
                maxlength="100"
                placeholder="Dein Name"
                value="<?= htmlspecialchars($formData['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="email">E-Mail-Adresse *</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                placeholder="deine@email.de"
                value="<?= htmlspecialchars($formData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="subject">Betreff *</label>
            <input
                type="text"
                id="subject"
                name="subject"
                required
                maxlength="200"
                placeholder="Worum geht es?"
                value="<?= htmlspecialchars($formData['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="message">Nachricht *</label>
            <textarea
                id="message"
                name="message"
                required
                maxlength="5000"
                placeholder="Schreib uns deine Nachricht..."><?= htmlspecialchars($formData['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Honeypot (unsichtbar) -->
        <div class="form-group honeypot" aria-hidden="true">
            <label for="website">Website</label>
            <input
                type="text"
                id="website"
                name="website"
                tabindex="-1"
                autocomplete="off">
        </div>

        <button type="submit" class="btn-submit">
            Nachricht senden
        </button>
    </form>

    <!-- =========================================
         WEITERE KONTAKTINFOS
    ========================================= -->
    <section class="info-box">
        <h3>Weitere Kontaktmöglichkeiten</h3>
        <p>
            <strong>Forum:</strong>
            <a href="/forum">Support-Forum</a>
        </p>
        <p>
            <strong>E-Mail:</strong>
            <a href="mailto:kontakt@engels811-ttv.de">kontakt@engels811-ttv.de</a>
        </p>
    </section>

    <!-- =========================================
         DISCORD BANNER
    ========================================= -->
    <aside class="contact-banner">
        <a href="https://discord.gg/cQSD9A4pmM" target="_blank" rel="noopener noreferrer">
            <img
                src="https://i.ibb.co/Y7zCgFFt/Chat-GPT-Image-27-Dez-2025-09-42-07.png"
                alt="Engels811 Network Discord"
                loading="lazy">
        </a>
    </aside>

</div>

</body>
</html>
