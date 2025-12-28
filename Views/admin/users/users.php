<?php
/** @var array $users */
/** @var array $roles */
?>

<h1 class="page-title accent">👤 Benutzerverwaltung</h1>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert error">
        <?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert success">
        <?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<div class="admin-card">

    <table class="admin-table">
        <thead>
        <tr>
            <th>Benutzer</th>
            <th>E-Mail</th>
            <th>Rolle</th>
            <th>Level</th>
            <th>Aktionen</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($users as $u): ?>
            <tr>

                <td><?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>

                <!-- ROLLE ÄNDERN -->
                <td>
                    <?php if (
                        Permission::has('admin.users.manage')
                        && $u['id'] !== ($_SESSION['user']['id'] ?? 0)
                        && ($u['role'] ?? 0) < ($_SESSION['user']['role'] ?? 0)
                    ): ?>
                        <form method="post" action="/admin/users/update-role">
                            <?= Security::csrfField() ?>

                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">

                            <select
                                name="role"
                                onchange="if (confirm('Rolle wirklich ändern?')) this.form.submit();"
                            >
                                <?php foreach ($roles as $role): ?>
                                    <option
                                        value="<?= (int)$role['id'] ?>"
                                        <?= (int)$role['id'] === (int)$u['role'] ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?>                                                         
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    <?php else: ?>
                        <?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>
                    <?php endif; ?>
                </td>

                <td><?= (int)$u['role'] ?></td>

                <!-- DELETE -->
                <td>
                    <?php if (
                        Permission::has('admin.users.manage')
                        && $u['id'] !== ($_SESSION['user']['id'] ?? 0)
                        && ($u['role'] ?? 0) < ($_SESSION['user']['role'] ?? 0)
                    ): ?>
                        <form
                            method="post"
                            action="/admin/users/delete"
                            onsubmit="return confirm('Benutzer wirklich löschen?');"
                        >
                            <?= Security::csrfField() ?>
                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                            <button class="btn btn-danger small">🗑️ Löschen</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>

            </tr>
        <?php endforeach; ?>

        <?php if (empty($users)): ?>
            <tr>
                <td colspan="5" class="empty-state">
                    Keine Benutzer gefunden.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>

</div>
