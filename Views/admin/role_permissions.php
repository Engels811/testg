<h1 class="page-title accent">🔐 Rechte für Rolle</h1>

<div class="admin-card">
    Rolle: <strong><?= htmlspecialchars($role['name']) ?></strong>
</div>

<form method="post" action="/admin/roles/permissions/save" class="admin-card">

    <input type="hidden" name="role_id" value="<?= $role['id'] ?>">

    <?php foreach ($perms as $perm): ?>
        <label style="display:block;margin-bottom:8px;">
            <input type="checkbox" name="permissions[]"
                   value="<?= $perm['id'] ?>"
                <?= in_array($perm['id'], $assigned, true) ? 'checked' : '' ?>>
            <?= htmlspecialchars($perm['description'] ?? $perm['key_name']) ?>
        </label>
    <?php endforeach; ?>

    <button class="btn-accent small">Rechte speichern</button>
</form>
