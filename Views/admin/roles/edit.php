<h1>🔧 Rolle bearbeiten: <?= htmlspecialchars($role['name']) ?></h1>

<form method="post" action="/owner/roles/update">
<?= Security::csrf() ?>
<input type="hidden" name="role_id" value="<?= $role['id'] ?>">

<?php foreach ($permissions as $perm): ?>
<label class="permission-row">
    <input type="checkbox"
           name="permissions[]"
           value="<?= $perm['id'] ?>"
           <?= in_array($perm['id'], $assignedIds, true) ? 'checked' : '' ?>>
    <?= htmlspecialchars($perm['key_name']) ?>
</label>
<?php endforeach; ?>

<button class="btn btn-accent">Speichern</button>
</form>
