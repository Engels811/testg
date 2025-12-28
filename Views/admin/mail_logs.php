<h2>📜 Mail-Log</h2>

<?php foreach ($logs as $log): ?>
<div class="admin-card">

    <strong><?= htmlspecialchars($log['subject']) ?></strong><br>
    An: <?= htmlspecialchars($log['recipient_email']) ?><br>
    Gesendet am: <?= $log['created_at'] ?><br>
    Status: <?= $log['status'] ?>

    <details style="margin-top:10px;">
        <summary>Inhalt anzeigen</summary>
        <div style="margin-top:10px;background:#111;padding:12px;border-radius:8px;">
            <?= nl2br(htmlspecialchars($log['message'])) ?>
        </div>
    </details>

</div>
<?php endforeach; ?>
