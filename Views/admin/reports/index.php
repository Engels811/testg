<?php
/**
 * Admin – Reports Übersicht
 *
 * Erwartete Variablen:
 * @var array $reports
 */
$reports = $reports ?? [];

// Debugging: Gibt die abgerufenen Reports aus, um sicherzustellen, dass sie richtig geladen wurden
var_dump($reports);
?>

<section class="page-wrapper">

    <div class="page-head">
        <h1>Reports</h1>
        <p>Gemeldete Inhalte aus Galerie, Forum und Videos</p>
    </div>

    <div class="card">

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Typ</th>
                    <th>Inhalt</th>
                    <th>Grund</th>
                    <th>Status</th>
                    <th>Bearbeiter</th>
                    <th>Datum</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>

            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="8" class="muted center">
                        Keine Reports vorhanden
                    </td>
                </tr>
            <?php else: ?>

                <?php foreach ($reports as $r): ?>
                    <tr class="status-<?= htmlspecialchars($r['status'], ENT_QUOTES, 'UTF-8') ?>">

                        <td>#<?= (int)$r['id'] ?></td>

                        <td>
                            <?= htmlspecialchars(ucfirst($r['content_type']), ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($r['content_type'], ENT_QUOTES, 'UTF-8') ?>
                            #<?= (int)$r['content_id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(ucfirst($r['reason']), ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                str_replace('_', ' ', ucfirst($r['status'])),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?php if (!empty($r['assigned_to'])): ?>
                                User #<?= (int)$r['assigned_to'] ?>
                            <?php else: ?>
                                <em>–</em>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= date('d.m.Y H:i', strtotime($r['created_at'])) ?>
                        </td>

                        <td>
                            <a
                                href="/admin/reports/<?= (int)$r['id'] ?>"
                                class="btn small"
                            >
                                Öffnen
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>
        </table>

    </div>

</section>
