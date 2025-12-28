<?php
/**
 * Dashboard – Sicherheit
 *
 * @var array $user
 * @var array $sessions
 * @var array $loginHistory
 * @var array $devices
 */

ob_start();
?>

<header class="dashboard-head">
    <h1>🔐 Sicherheit</h1>
    <p>Passwort, Sitzungen, Login-Schutz & Zwei-Faktor-Authentifizierung</p>
</header>

<div class="dashboard-grid dashboard-grid-security">

    <!-- =========================
         PASSWORT
    ========================== -->
    <div class="card">
        <h3>🔑 Passwort ändern</h3>

        <form method="post" action="/dashboard/security/password">
            <?= Security::csrfField() ?>

            <label>Aktuelles Passwort</label>
            <input type="password" name="current_password" required>

            <label>Neues Passwort</label>
            <input type="password" name="new_password" minlength="8" required>

            <label>Neues Passwort wiederholen</label>
            <input type="password" name="repeat_password" minlength="8" required>

            <button class="btn-primary">
                Passwort speichern
            </button>
        </form>
    </div>

    <!-- =========================
         LOGIN-SICHERHEIT
    ========================== -->
    <div class="card">
        <h3>📧 Login-Sicherheitsmeldungen</h3>

        <form method="post" action="/dashboard/security/login-alerts">
            <?= Security::csrfField() ?>

            <label class="checkbox">
                <input
                    type="checkbox"
                    name="login_alerts_enabled"
                    value="1"
                    <?= !empty($user['login_alerts_enabled']) ? 'checked' : '' ?>
                >
                Sicherheits-E-Mail bei neuem Gerät oder IP
            </label>

            <button class="btn-primary btn-small">
                Einstellungen speichern
            </button>
        </form>

        <p class="muted">
            Du erhältst nur dann eine E-Mail, wenn ein neues Gerät oder Standort erkannt wird.
        </p>
    </div>

    <!-- =========================
         SESSIONS (BREIT)
    ========================== -->
    <div class="card sessions-card">
        <h3>📍 Aktive Sitzungen</h3>

        <?php if (empty($sessions)): ?>
            <p class="muted">Keine aktiven Sitzungen.</p>
        <?php else: ?>
            <div class="login-scrollbox">
                <ul class="dashboard-list">
                    <?php foreach ($sessions as $s): ?>
                        <?php $isCurrent = ($s['session_id'] === session_id()); ?>

                        <li class="<?= $isCurrent ? 'session-current' : '' ?>">
                            <span>
                                <?= htmlspecialchars($s['ip']) ?><br>

                                <small class="muted">
                                    <?= htmlspecialchars(substr($s['user_agent'], 0, 90)) ?>
                                </small><br>

                                <small class="muted">
                                    <?= htmlspecialchars($s['created_at']) ?>
                                </small>

                                <?php if ($isCurrent): ?>
                                    <span class="session-badge">🔴 Aktuelle Sitzung</span>
                                <?php endif; ?>
                            </span>

                            <?php if (!$isCurrent): ?>
                                <form method="post" action="/dashboard/security/session/logout">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="session_id"
                                           value="<?= htmlspecialchars($s['session_id']) ?>">
                                    <button class="btn-danger btn-small">
                                        🚫 Beenden
                                    </button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <!-- =========================
         GERÄTE
    ========================== -->
    <div class="card">
        <h3>💻 Gemerkte Geräte</h3>

        <?php if (empty($devices)): ?>
            <p class="muted">Keine gespeicherten Geräte.</p>
        <?php else: ?>
            <ul class="dashboard-list">
                <?php foreach ($devices as $d): ?>
                    <li>
                        <span>
                            <small class="muted">
                                Hinzugefügt am <?= date('d.m.Y H:i', strtotime($d['created_at'])) ?>
                            </small>
                        </span>

                        <form method="post" action="/dashboard/security/device/remove">
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="device_id" value="<?= (int)$d['id'] ?>">
                            <button class="btn-danger btn-small">
                                Entfernen
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- =========================
         LOGIN-HISTORIE
    ========================== -->
    <div class="card">
        <h3>🕒 Letzte Logins</h3>

        <?php if (empty($loginHistory)): ?>
            <p class="muted">Keine Login-Daten vorhanden.</p>
        <?php else: ?>
            <ul class="dashboard-list">
                <?php foreach ($loginHistory as $l): ?>
                    <li>
                        <small class="muted">
                            <?= date('d.m.Y H:i', strtotime($l['created_at'])) ?>
                        </small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- =========================
         2FA
    ========================== -->
    <div class="card">
        <h3>🔐 Zwei-Faktor-Authentifizierung</h3>

        <?php if (!empty($user['twofa_enabled'])): ?>
            <p class="status-active">✅ 2FA aktiv</p>

            <form method="post" action="/dashboard/security/2fa/disable">
                <?= Security::csrfField() ?>
                <button class="btn-danger">
                    2FA deaktivieren
                </button>
            </form>
        <?php else: ?>
            <p class="muted">
                Zusätzlicher Schutz durch Einmalcode.
            </p>

            <a href="/dashboard/security/2fa" class="btn-primary btn-full">
                🔐 2FA aktivieren
            </a>
        <?php endif; ?>
    </div>

</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/Views/dashboard/_layout.php';
