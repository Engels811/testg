<?php
/**
 * Admin – AGB bearbeiten
 * Jeder Abschnitt separat speicherbar
 *
 * Erwartete Variablen:
 * - $sections (Array aus agb_sections)
 */
?>

<section class="page-wrapper agb-admin">

    <div class="page-head center">
        <span class="page-icon">📄</span>
        <h1 class="page-title accent">AGB bearbeiten</h1>
        <p class="page-subtitle">Jeder Abschnitt ist separat editierbar</p>
    </div>

    <?php foreach ($sections as $section): ?>
        <form method="post" action="/admin/agb/update" class="media-card agb-edit-card">

            <input type="hidden" name="id" value="<?= (int)$section['id'] ?>">
            <input type="hidden" name="section_key" value="<?= htmlspecialchars($section['section_key']) ?>">
            <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">

            <h3><?= htmlspecialchars($section['title']) ?></h3>

            <textarea
                name="content"
                rows="8"
                required
            ><?= htmlspecialchars($section['content']) ?></textarea>

            <div class="right">
                <button type="submit" class="btn btn-primary small">
                    Abschnitt speichern
                </button>
            </div>

        </form>
    <?php endforeach; ?>

</section>
