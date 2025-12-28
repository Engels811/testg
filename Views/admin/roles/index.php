<h1>🔐 Rollen</h1>

<form method="post" action="/owner/roles/store">
<?= Security::csrf() ?>
<input name="name" placeholder="Name" required>
<input name="level" type="number" placeholder="Level" required>
<input name="description" placeholder="Beschreibung">
<button class="btn btn-accent small">Rolle erstellen</button>
</form>

<table class="admin-table">
<tr>
    <th>Name</th>
    <th>Level</th>
    <th></th>
</tr>

<?php foreach ($roles as $role): ?>
<tr>
    <td><?= htmlspecialchars($role['name']) ?></td>
    <td><?= (int)$role['level'] ?></td>
    <td>
        <a href="/owner/roles/edit/<?= $role['id'] ?>" class="btn btn-secondary small">
            Bearbeiten
        </a>
    </td>
</tr>
<?php endforeach; ?>
</table>
