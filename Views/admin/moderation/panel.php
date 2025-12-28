<?php
/**
 * Moderations-Panel
 *
 * Variablen:
 * - $deletedPosts (array)
 * - $logs (array)
 */
?>

<section class="admin-panel moderation-panel">

    <h1>Moderations-Panel</h1>

    <!-- ===================== -->
    <!-- AUSGEBLENDETE BEITRÄGE -->
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
                        <td>#<?= (int)$post['id'] ?></td>

                        <td>
                            <?= htmlspecialchars($post['username'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= nl2br(
                                htmlspecialchars(
                                    mb_strimwidth($post['content'], 0, 120, '…'),
                                    ENT_QUOTES,
                                    'UTF-8'
                                )
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($post['deleted_at'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <form
                                method="post"
                                action="/forum/post/<?= (int)$post['id'] ?>/restore"
                                onsubmit="return confirm('Beitrag wirklich wiederherstellen?');"
                            >
                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= htmlspecialchars(Csrf::token(), ENT_QUOTES, 'UTF-8') ?>"
                                >

                                <button type="submit" class="btn success">
                                    Wiederherstellen
                                </button>
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
                        <td>
                            <?= htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($log['moderator'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($log['target_type'], ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            #<?= (int)$log['target_id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($log['reason'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</section>
