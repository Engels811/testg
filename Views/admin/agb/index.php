<section class="page-wrapper">

    <div class="page-head center">
        <span class="page-icon">📜</span>
        <h1 class="page-title accent">AGB bearbeiten</h1>
        <p class="page-subtitle">Jeder Abschnitt separat editierbar</p>
    </div>

    <div class="media-stack">

        <?php foreach ($sections as $section): ?>
            <div class="media-card">

                <h3><?= htmlspecialchars($section['title']) ?></h3>

                <form method="post" action="/admin/cms/agb/update">
                    <input type="hidden" name="id" value="<?= $section['id'] ?>">
                    <input type="hidden" name="csrf" value="<?= Csrf::token() ?>">

                    <textarea
                        name="content"
                        rows="8"
                        style="width:100%; resize:vertical;"
                    ><?= htmlspecialchars($section['content']) ?></textarea>

                    <div style="margin-top:12px; text-align:right;">
                        <button class="btn btn-primary small">
                            Abschnitt speichern
                        </button>
                    </div>
                </form>

            </div>
        <?php endforeach; ?>

    </div>

</section>
