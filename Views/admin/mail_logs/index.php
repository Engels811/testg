<?php /** @var array $logs */ ?>

<section class="page-wrapper">

    <div class="page-head">
        <h1>📬 Mail-Logs</h1>
        <p>Versendete System-E-Mails</p>
    </div>

    <div class="card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>E-Mail</th>
                    <th>Betreff</th>
                    <th>Typ</th>
                    <th>Status</th>
                    <th>Fehler</th>
                    <th>Datum</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr class="status-<?= $log['status'] ?>">
                    <td>#<?= (int)$log['id'] ?></td>
                    <td><?= htmlspecialchars($log['email']) ?></td>
                    <td><?= htmlspecialchars($log['subject']) ?></td>
                    <td><?= htmlspecialchars($log['type']) ?></td>
                    <td>
                        <?= $log['status'] === 'sent'
                            ? '✅ Gesendet'
                            : '❌ Fehler' ?>
                    </td>
                    <td style="max-width:260px;">
                        <?= htmlspecialchars($log['error_message'] ?? '-') ?>
                    </td>
                    <td><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</section>
