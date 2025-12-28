<?php
/**
 * ADMIN – BENUTZERVERWALTUNG
 * - Rollen ändern
 * - User löschen (außer sich selbst)
 * - CSRF-geschützt
 *
 * Erwartet:
 * $users (array)
 */

/* =========================
   DEFENSIVE INITIALISIERUNG
   (verhindert Warnings bei
   falschem Render-Aufruf)
========================= */
$users = $users ?? [];
?>

<section class="section admin-section">
    <div class="container">

        <!-- =========================
             HEADER
        ========================== -->
        <header class="section-head">
            <h1 class="section-title">
                👤 <span>Benutzerverwaltung</span>
            </h1>

            <p class="section-subtitle">
                Benutzerkonten und Rollen verwalten
            </p>
        </header>

        <!-- =========================
             USER TABLE
        ========================== -->
        <div class="card" style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>E-Mail</th>
                        <th>Rolle</th>
                        <th>Erstellt</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="center muted">
                            Keine Benutzer gefunden
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>

                            <!-- ID -->
                            <td><?= (int)$user['id'] ?></td>

                            <!-- USERNAME -->
                            <td>
                                <strong>
                                    <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>
                            </td>

                            <!-- EMAIL -->
                            <td>
                                <?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <!-- ROLE -->
                            <td>
                                <form
                                    method="post"
                                    action="/admin/users/update-role"
                                    class="inline-form"
                                >
                                    <input type="hidden" name="csrf" value="<?= Security::csrf() ?>">
                                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">

                                    <select
                                        name="role"
                                        onchange="this.form.submit()"
                                    >
                                        <option value="user"
                                            <?= $user['role'] === 'user' ? 'selected' : '' ?>>
                                            User
                                        </option>

                                        <option value="admin"
                                            <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                                            Admin
                                        </option>
                                    </select>
                                </form>
                            </td>

                            <!-- CREATED -->
                            <td>
                                <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?>
                            </td>

                            <!-- ACTIONS -->
                            <td>
                                <?php if ($user['id'] !== ($_SESSION['user']['id'] ?? 0)): ?>
                                    <form
                                        method="post"
                                        action="/admin/users/delete"
                                        onsubmit="return confirm('Benutzer wirklich löschen?');"
                                        class="inline-form"
                                    >
                                        <input type="hidden" name="csrf" value="<?= Security::csrf() ?>">
                                        <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">

                                        <button class="btn btn-danger small">
                                            Löschen
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

        <!-- =========================
             FOOTER ACTIONS
        ========================== -->
        <div style="margin-top:20px;">
            <a href="/admin" class="btn btn-secondary">
                ← Zurück zum Dashboard
            </a>
        </div>

    </div>
</section>
