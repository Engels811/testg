<section class="admin-wrapper">
    <header class="admin-header">
        <h1>💬 Forum moderieren</h1>
        <p>Threads verwalten, anheften, sperren oder löschen</p>
    </header>

    <?php if (empty($threads)): ?>
        <div class="admin-empty">
            Keine Threads vorhanden.
        </div>
    <?php else: ?>

        <div class="admin-thread-cards">
            <?php foreach ($threads as $t): ?>
                <div class="admin-thread-card">
                    <!-- TITEL + STICKY -->
                    <div class="admin-thread-title">
                        <?= htmlspecialchars($t['title'] ?? 'Unbekannt', ENT_QUOTES, 'UTF-8') ?>
                        <?php if (!empty($t['is_sticky'])): ?>
                            <span class="thread-sticky-badge">📌 Sticky</span>
                        <?php endif; ?>
                    </div>

                    <!-- KATEGORIE -->
                    <div class="admin-thread-category">
                        <?= htmlspecialchars($t['category'] ?? 'Unbekannt', ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <!-- AUTOR -->
                    <div class="admin-thread-author">
                        <?= htmlspecialchars($t['username'] ?? 'Unbekannt', ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <!-- STATUS -->
                    <div class="admin-thread-status">
                        <?= !empty($t['is_locked'])
                            ? '<span class="locked">Gesperrt</span>'
                            : '<span class="open">Offen</span>' ?>
                    </div>

                    <!-- AKTIONEN -->
                    <div class="admin-thread-actions">
                        <!-- STICKY TOGGLE -->
                        <form method="post" action="/admin/forum/toggle-sticky">
                            <?= Security::csrfField(); ?> <!-- CSRF Token -->
                            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                            <button class="admin-thread-btn admin-thread-btn-secondary">
                                <?= !empty($t['is_sticky']) ? '📌 Entfernen' : '📌 Anheften' ?>
                            </button>
                        </form>

                        <!-- LOCK TOGGLE -->
                        <form method="post" action="/admin/forum/toggle-lock">
                            <?= Security::csrfField(); ?> <!-- CSRF Token -->
                            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                            <button class="admin-thread-btn admin-thread-btn-secondary">
                                <?= !empty($t['is_locked']) ? '🔓 Entsperren' : '🔒 Sperren' ?>
                            </button>
                        </form>

                        <!-- DELETE -->
                        <form method="post" action="/admin/forum/delete" onsubmit="return confirm('Thread wirklich löschen?');">
                            <?= Security::csrfField(); ?> <!-- CSRF Token -->
                            <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                            <button class="admin-thread-btn admin-thread-btn-danger">
                                🗑 Löschen
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</section>
