<section class="page-wrapper">

    <h2><?= htmlspecialchars($page['title']) ?></h2>

    <form method="post" action="/admin/cms/save">
        <input type="hidden" name="slug" value="<?= $page['slug'] ?>">

        <label>Version</label>
        <input type="text" name="version" value="<?= $page['version'] ?>">

        <label>Inhalt (HTML erlaubt)</label>
        <textarea name="content" rows="20"><?= htmlspecialchars($page['content']) ?></textarea>

        <button class="btn-accent">Speichern</button>
    </form>

</section>
