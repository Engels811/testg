<?php
/** @var array $report */
$user = Auth::user();
?>

<section class="page-wrapper">

    <div class="page-head">
        <h1>Report #<?= (int)$report['id'] ?></h1>
        <p><?= ucfirst($report['content_type']) ?> · <?= ucfirst($report['reason']) ?></p>
    </div>

    <div class="grid two">

        <!-- Report Infos -->
        <div class="card">
            <h3>Report-Informationen</h3>

            <ul class="info-list">
                <li><strong>Typ:</strong> <?= $report['content_type'] ?></li>
                <li><strong>Inhalt:</strong> <?= $report['content_type'] ?> #<?= (int)$report['content_id'] ?></li>
                <li><strong>Grund:</strong> <?= ucfirst($report['reason']) ?></li>
                <li><strong>Kommentar:</strong><br><?= nl2br(htmlspecialchars($report['message'] ?? '–')) ?></li>
                <li><strong>Gemeldet von:</strong> <?= htmlspecialchars($report['reporter_name'] ?? 'Unbekannt') ?></li>
                <li><strong>Status:</strong> <?= ucfirst(str_replace('_',' ',$report['status'])) ?></li>
            </ul>
        </div>

        <!-- Aktionen -->
        <div class="card">
            <h3>Moderations-Aktionen</h3>

            <?php if (!$report['assigned_to']): ?>
                <form method="post" action="/admin/reports/<?= (int)$report['id'] ?>/assign">
                    <button class="btn primary">Report übernehmen</button>
                </form>
                <hr>
            <?php endif; ?>

            <form method="post" action="/admin/reports/<?= (int)$report['id'] ?>/update">

                <label>Status</label>
                <select name="status">
                    <?php foreach (['open','in_review','action_taken','rejected','closed'] as $s): ?>
                        <option value="<?= $s ?>" <?= $report['status']===$s?'selected':'' ?>>
                            <?= ucfirst(str_replace('_',' ',$s)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Aktion</label>
                <select name="action">
                    <?php foreach ([
                        'none' => 'Keine',
                        'warning' => 'Verwarnung',
                        'content_hidden' => 'Inhalt ausblenden',
                        'content_deleted' => 'Inhalt löschen'
                    ] as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $report['action']===$key?'selected':'' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button class="btn primary">Speichern</button>
            </form>

            <?php if ($user->role === 'superadmin'): ?>
                <hr>
                <h4>User-Sanktionen</h4>
                <button class="btn danger">User sperren</button>
                <button class="btn danger">User bannen</button>
            <?php endif; ?>

        </div>

    </div>

</section>
