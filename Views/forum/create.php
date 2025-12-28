<?php
/**
 * Forum – Neues Thema erstellen
 *
 * Erwartete Variablen:
 * @var array       $category
 * @var string|null $error
 * @var array       $old
 */

$categoryTitle = $category['title'] ?? '';
$categorySlug  = $category['slug'] ?? '';

$error = $error ?? null;
$old   = is_array($old ?? null) ? $old : [];
?>

<section class="forum-wrapper">
    <div class="forum-container">

        <!-- =========================================================
             HEADER / HERO
        ========================================================= -->
        <header class="forum-header forum-hero">

            <span class="forum-eyebrow">Forum</span>

            <h1 class="forum-title">
                Neues Thema erstellen
            </h1>

            <p class="forum-subtitle">
                Kategorie: <?= htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div class="forum-breadcrumb">
                <a href="/forum">Forum</a>
                <span>›</span>
                <a href="/forum/<?= htmlspecialchars($categorySlug, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <span>›</span>
                <span>Neues Thema</span>
            </div>

        </header>

        <!-- =========================================================
             FEHLERMELDUNG
        ========================================================= -->
        <?php if ($error): ?>
            <div class="forum-error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- =========================================================
             FORM
        ========================================================= -->
        <form
            method="post"
            action="/forum/<?= htmlspecialchars($categorySlug, ENT_QUOTES, 'UTF-8') ?>/store"
            class="forum-form"
        >
            <!-- CSRF-Token einfügen -->
            <input type="hidden" name="csrf" value="<?= Security::csrf() ?>">

            <label>
                Titel
                <input
                    type="text"
                    name="title"
                    maxlength="120"
                    required
                    value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Gib deinem Thema einen Titel"
                >
            </label>

            <label>
                Beitrag
                <textarea
                    name="content"
                    rows="10"
                    required
                    placeholder="Schreibe deinen Beitrag"
                ><?= htmlspecialchars($old['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </label>

            <div class="forum-form-actions">
                <button type="submit" class="forum-btn">
                    Thema erstellen
                </button>

                <a
                    href="/forum/<?= htmlspecialchars($categorySlug, ENT_QUOTES, 'UTF-8') ?>"
                    class="forum-btn secondary"
                >
                    Abbrechen
                </a>
            </div>
        </form>

    </div>
</section>
