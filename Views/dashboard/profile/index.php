<?php
/**
 * Dashboard – Profil
 * - Avatar (Upload + Auswahl)
 * - Username ändern
 * - E-Mail ändern (mit Bestätigung)
 * - Änderungs-Log
 *
 * @var array $user
 * @var array $avatars
 * @var array $logs
 */

ob_start();
?>

<!-- =========================
     FLASH MELDUNGEN
========================= -->
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert-success">
        <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert-error">
        <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (!empty($user['pending_email'])): ?>
    <div class="alert-info">
        📧 Neue E-Mail-Adresse
        <strong><?= htmlspecialchars($user['pending_email'], ENT_QUOTES, 'UTF-8') ?></strong>
        wartet auf Bestätigung.
    </div>
<?php endif; ?>

<!-- =========================
     PROFILE HERO
========================= -->
<header class="profile-hero">
    <div class="profile-hero-inner">

        <div class="profile-avatar-xl">
            <img
                id="avatarPreview"
                src="/uploads/avatars/<?= htmlspecialchars($user['avatar'] ?? 'default.png', ENT_QUOTES, 'UTF-8') ?>"
                alt="Avatar"
            >
            <span class="profile-status online"></span>
        </div>

        <div class="profile-hero-meta">
            <h1><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></h1>

            <span class="role-badge role-<?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?>
            </span>

            <p class="profile-subtitle">Profil &amp; Sicherheit</p>
        </div>

    </div>
</header>

<div class="dashboard-grid">

    <!-- ================= AVATAR ================= -->
    <div class="card">

        <h3>🖼️ Avatar</h3>

        <form
            method="post"
            action="/dashboard/profile/avatar"
            enctype="multipart/form-data"
        >
            <?= Security::csrfField() ?>

            <input
                type="file"
                name="avatar"
                accept="image/png,image/jpeg"
                onchange="previewAvatar(this); enableAvatarBtn();"
                required
            >

            <button
                id="avatarSubmitBtn"
                class="btn-primary"
                disabled
            >
                Avatar hochladen
            </button>
        </form>

        <?php if (!empty($avatars)): ?>
            <h4 style="margin-top:16px">Avatar-Historie</h4>

            <div class="avatar-history">
                <?php foreach ($avatars as $a): ?>
                    <form method="post" action="/dashboard/profile/avatar/select">
                        <?= Security::csrfField() ?>

                        <input
                            type="hidden"
                            name="avatar"
                            value="<?= htmlspecialchars($a['filename'], ENT_QUOTES, 'UTF-8') ?>"
                        >

                        <button
                            type="submit"
                            class="avatar-thumb"
                            title="Avatar auswählen"
                        >
                            <img
                                src="/uploads/avatars/<?= htmlspecialchars($a['filename'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="Avatar"
                            >
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- ================= ACCOUNT ================= -->
    <div class="card">

        <h3>📄 Accountdaten</h3>

        <form method="post" action="/dashboard/profile/update">
            <?= Security::csrfField() ?>

            <label for="username">Benutzername</label>
            <input
                id="username"
                type="text"
                name="username"
                value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>"
                required
                minlength="3"
                maxlength="32"
            >

            <label for="email">E-Mail</label>
            <input
                id="email"
                type="email"
                name="email"
                value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>"
                required
            >

            <button type="submit" class="btn-primary">
                Profil speichern
            </button>
        </form>
    </div>

    <!-- ================= LOG ================= -->
    <div class="card">

        <h3>🕘 Änderungs-Log</h3>

        <?php if (empty($logs)): ?>
            <p>Keine Änderungen vorhanden</p>
        <?php else: ?>
            <ul class="profile-log">
                <?php foreach ($logs as $log): ?>
                    <li>
                        <strong><?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                        <small><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>

</div>

<!-- =========================
     JS
========================= -->
<script>
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    if (file.size > 2 * 1024 * 1024) {
        alert('Avatar darf maximal 2 MB groß sein.');
        input.value = '';
        return;
    }

    if (!['image/jpeg', 'image/png'].includes(file.type)) {
        alert('Nur JPG oder PNG erlaubt.');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('avatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function enableAvatarBtn() {
    document.getElementById('avatarSubmitBtn').disabled = false;
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/dashboard/_layout.php';
