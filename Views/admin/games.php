<section class="section admin-games">
    <div class="container">

        <header class="admin-head">
            <h1 class="section-title">🎮 <span>Games verwalten</span></h1>
        </header>

        <div class="top-games-grid admin-games-grid">

            <?php foreach ($games as $g): ?>
                <div class="top-game-card admin-game-card">

                    <!-- COVER -->
                    <div class="game-cover">

                        <img
                            src="<?= htmlspecialchars($g['cover'] ?? '/assets/img/game-placeholder.jpg') ?>"
                            alt="<?= htmlspecialchars($g['name']) ?>"
                        >

                        <!-- ADMIN OVERLAY -->
                        <div class="admin-overlay">

                            <!-- CONFIRMED -->
                            <button
                                class="admin-toggle <?= $g['confirmed'] ? 'active' : '' ?>"
                                title="Bestätigt"
                                onclick="toggle('confirmed',<?= (int)$g['id'] ?>,this)"
                            >
                                ✔
                            </button>

                            <!-- TOP -->
                            <button
                                class="admin-toggle star <?= $g['is_top'] ? 'active' : '' ?>"
                                title="Top Game"
                                onclick="toggle('is_top',<?= (int)$g['id'] ?>,this)"
                            >
                                ★
                            </button>

                        </div>
                    </div>

                    <!-- TITLE -->
                    <h3><?= htmlspecialchars($g['name']) ?></h3>

                    <!-- META -->
                    <div class="top-game-meta">

                        <!-- CATEGORY -->
                        <select
                            class="admin-category"
                            onchange="setCat(this,<?= (int)$g['id'] ?>)"
                        >
                            <?php foreach (require BASE_PATH.'/config/game_categories.php' as $c): ?>
                                <option <?= $g['category'] === $c ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c) ?>
                                </option>
                            <?php endforeach ?>
                        </select>

                        <!-- HOURS -->
                        <span class="hours">
                            <?= (int)($g['hours_override'] ?? $g['hours'] ?? 0) ?> h
                        </span>

                    </div>

                </div>
            <?php endforeach; ?>

        </div>

    </div>
</section>

<script>
function toggle(field,id,btn){
    fetch('/admin/api/game-toggle.php',{
        method:'POST',
        body:new URLSearchParams({id,field})
    });
    btn.classList.toggle('active');
}

function setCat(sel,id){
    fetch('/admin/api/game-category.php',{
        method:'POST',
        body:new URLSearchParams({id,cat:sel.value})
    });
}
</script>
