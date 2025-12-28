<?php
$current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- =====================================================
         GLOBAL STYLES
    ====================================================== -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- =====================================================
         DASHBOARD STYLES (MÜSSEN DANACH KOMMEN!)
    ====================================================== -->
    <link rel="stylesheet" href="/assets/css/profile.css">
    <link rel="stylesheet" href="/assets/css/dashboard-overview.css">

</head>

<body class="dashboard-body">

<!-- =========================================================
     DASHBOARD LAYOUT SHELL
========================================================= -->
<div class="dashboard-shell">

    <!-- =========================
         SIDEBAR
    ========================= -->
    <aside class="dashboard-sidebar">

        <div class="sidebar-head">
            <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong>
            <span>User Dashboard</span>
        </div>

        <nav class="sidebar-nav">
            <a href="/dashboard"
               class="<?= $current === 'dashboard' ? 'active' : '' ?>">
                🏠 Übersicht
            </a>

            <a href="/dashboard/profile"
               class="<?= str_starts_with($current, 'dashboard/profile') ? 'active' : '' ?>">
                👤 Profil
            </a>

            <a href="/dashboard/content"
               class="<?= $current === 'dashboard/content' ? 'active' : '' ?>">
                📂 Inhalte
            </a>

            <a href="/dashboard/security"
               class="<?= $current === 'dashboard/security' ? 'active' : '' ?>">
                🔐 Sicherheit
            </a>
        </nav>

    </aside>

    <!-- =========================
         MAIN CONTENT
    ========================= -->
    <main class="dashboard-main">
        <?= $content ?>
    </main>

</div>

</body>
</html>
