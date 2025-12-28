<section class="page-wrapper">

    <!-- ===============================
         HEADER
    =============================== -->
    <div class="page-head center">
        <span class="page-icon">🖥️</span>
        <h1 class="page-title accent">Hardware</h1>
        <p class="page-subtitle">
            <?= htmlspecialchars($setup['title'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <!-- ===============================
         SETUP SWITCHER
    =============================== -->
    <?php if (!empty($setups)): ?>
        <div class="category-filter">
            <?php foreach ($setups as $s): ?>
                <a
                    href="/hardware/<?= htmlspecialchars($s['slug'], ENT_QUOTES, 'UTF-8') ?>"
                    class="<?= ($s['id'] === $setup['id']) ? 'active' : '' ?>"
                >
                    <?= htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ===============================
         PC SPECS
    =============================== -->
    <?php if (!empty($hardware['pc'])): ?>
        <section class="content-block">
            <h2 class="section-title">🖥️ PC <span>Specs</span></h2>

            <div class="hardware-grid">
                <?php foreach ($hardware['pc'] as $spec): ?>
                    <div class="spec-card">
                        <div class="spec-icon">
                            <?= htmlspecialchars($spec['icon'] ?? '⚙️', ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div class="spec-title">
                            <?= htmlspecialchars($spec['title'], ENT_QUOTES, 'UTF-8') ?>
                        </div>

                        <div class="spec-detail">
                            <?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?><br>
                            <?php if (!empty($spec['details'])): ?>
                                <span class="text-muted">
                                    <?= htmlspecialchars($spec['details'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===============================
         MONITORS / AUDIO / CAMERA
    =============================== -->
    <section class="content-block alt-bg">
        <div class="media-grid">

            <?php
            $lists = [
                '🖥️ Monitore'       => $hardware['monitors'] ?? [],
                '🎙️ Audio'         => $hardware['audio'] ?? [],
                '📷 Kamera & Licht' => $hardware['camera_lighting'] ?? [],
            ];
            ?>

            <?php foreach ($lists as $title => $items): ?>
                <?php if (!empty($items)): ?>
                    <div class="media-card">
                        <div class="media-card-body">
                            <h3><?= $title ?></h3>
                            <ul class="hardware-list">
                                <?php foreach ($items as $item): ?>
                                    <li>
                                        <?= htmlspecialchars(
                                            is_array($item) ? $item['name'] : $item,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </section>

    <!-- ===============================
         EXTRAS
    =============================== -->
    <?php if (!empty($hardware['extras'])): ?>
        <section class="content-block">
            <h2 class="section-title">🎮 Extras</h2>

            <div class="media-grid">
                <div class="media-card">
                    <div class="media-card-body">
                        <ul class="hardware-list">
                            <?php foreach ($hardware['extras'] as $extra): ?>
                                <li>
                                    <?= htmlspecialchars(
                                        is_array($extra) ? $extra['name'] : $extra,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

</section>
