<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Admin – Engels811 Network' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Admin + Base CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="/assets/css/games/game-grid.css">
    <link rel="stylesheet" href="/assets/css/games/game-cards.css">
    <link rel="stylesheet" href="/assets/css/games/game-badges.css">
    <link rel="stylesheet" href="/assets/css/games/game-effects.css">
</head>
<body class="admin-body">

<?php
// TEMPORÄR: normalen Header verwenden
require BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="admin-main">
    <?= $content ?>
</main>

<?php
// optional Footer
require BASE_PATH . '/app/Views/layouts/footer.php';
?>

<!-- Übergabe des CSRF-Tokens an JavaScript -->
<script>
    window.CSRF_TOKEN = "<?= Security::csrf() ?>"; // CSRF-Token für JavaScript verfügbar machen
</script>

</body>
</html>
