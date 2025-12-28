<?php
/** @var array $log */
?>

<h1 class="page-title">🔍 Audit-Detail</h1>

<p>
    <strong>Admin:</strong>
    <?= htmlspecialchars($log['actor_username'] ?? 'System', ENT_QUOTES, 'UTF-8') ?><br>

    <strong>Aktion:</strong>
    <?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8') ?><br>

    <strong>Target:</strong>
    <?= htmlspecialchars($log['entity'], ENT_QUOTES, 'UTF-8') ?>
    <?= !empty($log['entity_id']) ? '#' . (int)$log['entity_id'] : '' ?><br>

    <strong>Zeit:</strong>
    <?= date('d.m.Y H:i:s', strtotime($log['created_at'])) ?>
</p>

<hr>

<div class="audit-diff">

    <div>
        <h3>Vorher</h3>
        <pre><?= htmlspecialchars(
            json_encode(
                json_decode($log['old_data'] ?? 'null', true),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?></pre>
    </div>

    <div>
        <h3>Nachher</h3>
        <pre><?= htmlspecialchars(
            json_encode(
                json_decode($log['new_data'] ?? 'null', true),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?></pre>
    </div>

</div>

<a href="/admin/audit" class="btn btn-secondary">← Zurück</a>
