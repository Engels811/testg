<?php
$current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
?>

<aside class="dashboard-sidebar">
    <nav>
        <a href="/dashboard" class="<?= $current === 'dashboard' ? 'active' : '' ?>">
            🏠 Übersicht
        </a>

        <a href="/dashboard/profile" class="<?= str_starts_with($current, 'dashboard/profile') ? 'active' : '' ?>">
            👤 Profil
        </a>

        <a href="/dashboard/content" class="<?= $current === 'dashboard/content' ? 'active' : '' ?>">
            📂 Inhalte
        </a>

        <a href="/dashboard/security" class="<?= $current === 'dashboard/security' ? 'active' : '' ?>">
            🔐 Sicherheit
        </a>
    </nav>
</aside>
