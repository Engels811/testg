<?php
$notifications = UserNotification::unread(Auth::user()->username);
?>

<div class="notif-wrapper">
    <button class="notif-btn">
        🔔 <span class="badge"><?= count($notifications) ?></span>
    </button>

    <div class="notif-dropdown">
        <?php if (empty($notifications)): ?>
            <p class="empty">Keine neuen Benachrichtigungen</p>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <div class="notif-item <?= $n['type'] ?>">
                    <strong><?= htmlspecialchars($n['title']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($n['message'])) ?></p>
                    <small><?= date('d.m.Y H:i', strtotime($n['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
