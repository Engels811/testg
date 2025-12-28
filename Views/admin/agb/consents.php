<?php
/**
 * Admin – AGB Zustimmungen
 * Zeigt User, Version und Datum
 */
?>

<section class="page-wrapper">

    <div class="page-head center">
        <span class="page-icon">✅</span>
        <h1 class="page-title accent">AGB-Zustimmungen</h1>
        <p class="page-subtitle">User & akzeptierte Versionen</p>
    </div>

    <?php if (empty($consents)): ?>
        <div class="card center">
            <p class="text-muted">Noch keine AGB-Zustimmungen vorhanden.</p>
        </div>
    <?php else: ?>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>AGB-Version</th>
                        <th>Akzeptiert am</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consents as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['username']) ?></td>
                            <td><?= htmlspecialchars($c['version']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($c['accepted_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</section>
