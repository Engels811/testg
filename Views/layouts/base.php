<?php
declare(strict_types=1);

/**
 * Base Layout
 *
 * Erwartete Variablen:
 * @var string $title
 * @var string $pageDescription
 * @var string $content
 */

$title = $title ?? 'Engels811 Network';
$pageDescription = $pageDescription ?? '';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">

    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">

    <!-- =========================
         GLOBAL LIBS
    ========================= -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- =========================
         GLOBAL STYLES
    ========================= -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/agb.css">
    <link rel="stylesheet" href="/assets/css/hardware.css">
    <link rel="stylesheet" href="/assets/css/pages/privacy.css">
    <link rel="stylesheet" href="/assets/css/pages/impressum.css">
    <link rel="stylesheet" href="/assets/css/brand-logo.css">

    <!-- =========================
         FORUM STYLES (nur wenn nötig)
    ========================= -->
    <?php if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/forum')): ?>
        <link rel="stylesheet" href="/assets/css/forum/forum.css">
    <?php endif; ?>

    <!-- =========================
         GAMES
    ========================= -->
    <link rel="stylesheet" href="/assets/css/games/game-grid.css">
    <link rel="stylesheet" href="/assets/css/games/game-cards.css">
    <link rel="stylesheet" href="/assets/css/games/game-badges.css">
    <link rel="stylesheet" href="/assets/css/games/game-effects.css">
</head>

<?php
/* =========================
   BODY KLASSEN
========================= */
$bodyClasses = [];

if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/forum')) {
    $bodyClasses[] = 'forum-page';
}
?>

<body class="<?= implode(' ', $bodyClasses) ?>">

<?php
require BASE_PATH . '/app/Views/layouts/header.php';
require BASE_PATH . '/app/Views/partials/live-banner.php';
?>

<!-- =========================
     MAIN CONTENT
========================= -->
<main>
    <?= $content ?>
</main>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>

<!-- =========================
     GLOBAL SCRIPTS
========================= -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const burger = document.getElementById('burgerBtn');
    const mobileNav = document.getElementById('mobileNav');

    if (burger && mobileNav) {
        burger.addEventListener('click', () => {
            mobileNav.classList.toggle('active');
        });
    }
});
</script>

<script>
    window.CSRF_TOKEN = "<?= Security::csrf() ?>";
</script>

<!-- =========================
     FEATURE SCRIPTS
========================= -->
<script src="/assets/js/gallery.js" defer></script>
<script src="/assets/js/forum-reactions.js" defer></script>
<script src="/assets/js/stats.js" defer></script>
<script src="/assets/js/community.js" defer></script>

</body>
</html>
