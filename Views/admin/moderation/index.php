<section class="admin-panel moderation-panel">

    <h1>Moderations-Panel</h1>

    <!-- ===================== -->
    <!-- GELÖSCHTE BEITRÄGE -->
    <!-- ===================== -->
    <h2>Ausgeblendete Beiträge</h2>

    <?php if (empty($deletedPosts)): ?>
        <p>Keine ausgeblendeten Beiträge.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Inhalt</th>
                    <th>Gelöscht am</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deletedPosts as $post): ?>
                    <tr>
                        <td>#<?= $post['id'] ?></td>
                        <td><?= htmlspecialchars($post['username']) ?></td>
                        <td><?= nl2br(htmlspecialchars(mb_strimwidth($post['content'], 0, 120, '…'))) ?></td>
                        <td><?= $post['deleted_at'] ?></td>
                        <td>
                            <form method="post" action="/forum/post/<?= $post['id'] ?>/restore">
                                <input type="hidden" name="_csrf" value="<?= Csrf::token() ?>">
                                <button class="btn success">Wiederherstellen</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- ===================== -->
    <!-- MODERATIONS-LOG -->
    <!-- ===================== -->
    <h2>Moderations-Log</h2>

    <?php if (empty($logs)): ?>
        <p>Keine Log-Einträge vorhanden.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Zeit</th>
                    <th>Moderator</th>
                    <th>Aktion</th>
                    <th>Ziel</th>
                    <th>ID</th>
                    <th>Grund</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['created_at'] ?></td>
                        <td><?= htmlspecialchars($log['moderator']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['target_type']) ?></td>
                        <td>#<?= $log['target_id'] ?></td>
                        <td><?= htmlspecialchars($log['reason'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</section>
