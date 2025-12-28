<h2>Gesperrte Accounts</h2>

<table>
  <tr>
    <th>User</th>
    <th>E-Mail</th>
    <th>Letzter Login</th>
    <th>Aktion</th>
  </tr>

  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= htmlspecialchars($u['username']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= $u['last_login_at'] ?: '—' ?></td>
      <td>
        <a href="/admin/unlock-user?id=<?= $u['id'] ?>">Entsperren</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
