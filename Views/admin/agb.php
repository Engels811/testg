<form method="post" action="/admin/cms/save">
<?= Security::csrfField() ?>

<?php foreach ($sections as $s): ?>
    <div class="card">
        <h3><?= htmlspecialchars($s['title']) ?></h3>
        <textarea name="sections[<?= $s['id'] ?>]"
                  rows="6"><?= htmlspecialchars($s['content']) ?></textarea>
    </div>
<?php endforeach; ?>

<button class="btn-primary">AGB speichern</button>
</form>
