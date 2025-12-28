<h1>🧩 Permissions</h1>

<form method="post" action="/owner/permissions/store">
<?= Security::csrf() ?>
<input name="key_name" placeholder="owner.xyz.action" required>
<input name="description" placeholder="Beschreibung">
<button class="btn btn-accent small">Erstellen</button>
</form>

<table class="admin-table">
<tr>
    <th>Key</th>
    <th>Beschreibung</th>
</tr>

<?php foreach ($permissions as $p): ?>
<tr>
    <td><?= htmlspecialchars($p['key_name']) ?></td>
    <td><?= htmlspecialchars($p['description']) ?></td>
</tr>
<?php endforeach; ?>
</table>
