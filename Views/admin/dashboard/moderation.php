<?php
/**
 * Moderations-Dashboard
 *
 * Variablen:
 * - $reportsPerDay (array)
 * - $modActions   (array)
 * - $openReports  (int)
 * - $openAppeals  (int)
 * - $cronStatus   (array|null)
 */

$reportsPerDay = $reportsPerDay ?? [];
$modActions    = $modActions ?? [];
$openReports   = (int)($openReports ?? 0);
$openAppeals   = (int)($openAppeals ?? 0);
$cronStatus    = $cronStatus ?? null;
?>

<link rel="stylesheet" href="/assets/css/admin-new.css">

<h1>Moderations-Dashboard</h1>

<!-- =========================
   QUICK STATS
========================= -->
<div class="stats-grid">

    <div class="stat-card danger">
        <span class="stat-label">Offene Reports</span>
        <span class="stat-value"><?= $openReports ?></span>
        <a href="/admin/reports?status=open" title="Offene Reports anzeigen"></a>
    </div>

    <div class="stat-card warning">
        <span class="stat-label">Offene Appeals</span>
        <span class="stat-value"><?= $openAppeals ?></span>
        <a href="/admin/appeals?status=open" title="Offene Appeals anzeigen"></a>
    </div>

    <div class="stat-card <?= ($cronStatus && $cronStatus['status'] === 'success') ? 'success' : 'error' ?>">
        <span class="stat-label">Cron: Moderation Cleanup</span>

        <?php if ($cronStatus): ?>
            <span class="stat-value"><?= strtoupper($cronStatus['status']) ?></span>
            <small>
                <?= date('d.m.Y H:i', strtotime($cronStatus['created_at'])) ?>
                · <?= (int)($cronStatus['runtime_ms'] ?? 0) ?> ms
            </small>
        <?php else: ?>
            <span class="stat-value">NIE</span>
        <?php endif; ?>
    </div>

</div>

<!-- =========================
   CHARTS
========================= -->
<section class="dashboard-charts">

    <div class="chart-card">
        <h2>Reports pro Tag (letzte 14 Tage)</h2>
        <canvas id="reportsChart" height="120"></canvas>
    </div>

    <div class="chart-card">
        <h2>Moderator-Aktivität</h2>
        <canvas id="modsChart" height="120"></canvas>
    </div>

</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* =========================
   REPORTS PRO TAG
========================= */
const reportsPerDay = <?= json_encode(array_reverse($reportsPerDay)) ?>;

if (reportsPerDay.length > 0) {
    new Chart(document.getElementById('reportsChart'), {
        type: 'line',
        data: {
            labels: reportsPerDay.map(r => r.day),
            datasets: [{
                label: 'Reports',
                data: reportsPerDay.map(r => r.count),
                tension: 0.35,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} Reports`
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

/* =========================
   MOD ACTIONS
========================= */
const modActions = <?= json_encode($modActions) ?>;

if (modActions.length > 0) {
    new Chart(document.getElementById('modsChart'), {
        type: 'bar',
        data: {
            labels: modActions.map(m => m.created_by),
            datasets: [{
                label: 'Aktionen',
                data: modActions.map(m => m.count)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} Aktionen`
                    }
                }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

/* =========================
   LIVE REFRESH (30 SEK.)
========================= */
setInterval(async () => {
    try {
        const res = await fetch('/api/moderation_stats');
        if (!res.ok) return;

        const data = await res.json();

        const reportsEl = document.querySelector('.stat-card.danger .stat-value');
        const appealsEl = document.querySelector('.stat-card.warning .stat-value');

        if (reportsEl) reportsEl.innerText = data.openReports;
        if (appealsEl) appealsEl.innerText = data.openAppeals;

    } catch (e) {
        // bewusst still
    }
}, 30000);
</script>
