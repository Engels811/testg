<section class="section">
    <div class="container">

        <h1 class="section-title">👤 <span>Benutzerverwaltung</span></h1>

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

                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= (int)$user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <form method="post" action="/admin/users/update-role">
                                <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td>
                            <?php if ($user['id'] !== ($_SESSION['user']['id'] ?? 0)): ?>
                                <form method="post" action="/admin/users/delete"
                                      onsubmit="return confirm('Benutzer wirklich löschen?');">
                                    <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                    <button class="btn btn-danger small">Löschen</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>

        <a href="/admin" class="btn btn-secondary" style="margin-top:20px;">
            ← Zurück zum Dashboard
        </a>

    </div>
</section>
