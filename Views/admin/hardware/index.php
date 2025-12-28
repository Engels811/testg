<section class="page-wrapper">

    <h1 class="page-title accent">🖥️ Hardware verwalten</h1>

    <!-- SETUP SWITCH -->
    <form method="get" class="category-filter">
        <?php foreach ($setups as $s): ?>
            <a
                href="/admin/hardware?setup=<?= (int)$s['id'] ?>"
                class="<?= ($activeSetup === (int)$s['id']) ? 'active' : '' ?>"
            >
                <?= htmlspecialchars($s['title']) ?>
            </a>
        <?php endforeach; ?>
    </form>

    <!-- ADD / EDIT FORM -->
    <form action="/admin/hardware/save" method="post" class="upload-box">

        <input type="hidden" name="id" value="">
        <input type="hidden" name="setup_id" value="<?= (int)$activeSetup ?>">

        <input name="icon" placeholder="Icon (z.B. 🎮)">
        <input name="title" placeholder="Titel (GPU)">
        <input name="name" placeholder="Name (RTX 4080)">
        <input name="details" placeholder="Details (16GB GDDR6X)">

        <select name="category">
            <option value="pc">PC</option>
            <option value="monitors">Monitore</option>
            <option value="audio">Audio</option>
            <option value="camera_lighting">Kamera & Licht</option>
            <option value="extras">Extras</option>
        </select>

        <button class="btn-accent">Speichern</button>
    </form>

    <!-- ITEM LIST -->
    <ul id="sortable" class="admin-sort-list">
        <?php foreach ($items as $item): ?>
            <li data-id="<?= (int)$item['id'] ?>">
                <span><?= $item['icon'] ?> <?= htmlspecialchars($item['title']) ?> – <?= htmlspecialchars($item['name']) ?></span>

                <form action="/admin/hardware/delete" method="post">
                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                    <button class="btn-danger small">✕</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

</section>
