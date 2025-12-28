<section class="admin-wrapper">

    <h1>🎥 Twitch Import Logs</h1>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Zeit</th>
                <th>Status</th>
                <th>Importiert</th>
                <th>Nachricht</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                    <td><?= htmlspecialchars($log['status']) ?></td>
                    <td><?= (int)$log['imported'] ?></td>
                    <td><?= htmlspecialchars($log['message']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</section>
