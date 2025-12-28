<section class="admin-wrapper">

    <!-- =========================
         HEADER
    ========================== -->
    <header class="admin-head">
        <div>
            <h1 class="admin-title">🤝 Partner</h1>
            <p class="admin-subtitle">
                Partner & Kooperationen verwalten
            </p>
        </div>

        <a href="/admin/partners/create" class="btn-accent btn-glow">
            ＋ Neuer Partner
        </a>
    </header>

    <!-- =========================
         LISTE
    ========================== -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>

        <?php if (empty($partners)): ?>
            <tr>
                <td colspan="3" style="text-align:center; opacity:.6;">
                    Noch keine Partner angelegt
                </td>
            </tr>
        <?php endif; ?>

        <?php foreach ($partners as $p): ?>
            <tr>

                <!-- NAME -->
                <td>
                    <strong>
                        <?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </td>

                <!-- STATUS -->
                <td>
                    <span class="<?= $p['is_active'] ? 'status-active' : 'status-inactive' ?>">
                        <?= $p['is_active'] ? 'Aktiv' : 'Inaktiv' ?>
                    </span>
                </td>

                <!-- AKTIONEN -->
                <td class="admin-actions">

                    <!-- TOGGLE -->
                    <form method="post"
                          action="/admin/partners/toggle"
                          style="display:inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn-small" title="Aktiv / Inaktiv">
                            Toggle
                        </button>
                    </form>

                    <!-- DELETE -->
                    <form method="post"
                          action="/admin/partners/delete"
                          onsubmit="return confirm('Partner wirklich löschen?')"
                          style="display:inline">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="btn-danger" title="Löschen">
                            ✕
                        </button>
                    </form>

                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

</section>
