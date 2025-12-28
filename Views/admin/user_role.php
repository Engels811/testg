<h1 class="page-title accent">👤 Benutzerrolle ändern</h1>

<div class="admin-card">
    <strong><?= htmlspecialchars($user['username']) ?></strong>
</div>

<form method="post" action="/admin/users/role/save" class="admin-card">

    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

    <select name="role_id" required>
        <?php foreach ($roles as $role): ?>
            <option value="<?= $role['id'] ?>"
                <?= $role['id'] == $user['role_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($role['name']) ?> (Level <?= $role['level'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <button class="btn-accent small">Rolle speichern</button>
</form>
