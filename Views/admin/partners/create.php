<section class="admin-wrapper narrow">

    <h1>➕ Partner anlegen</h1>

    <form method="post"
          action="/admin/partners/store"
          class="admin-form">

        <!-- CSRF (kommt aus dem Controller!) -->
        <input type="hidden" name="csrf" value="<?= $csrf ?>">

        <label>Name</label>
        <input type="text" name="name" required>

        <label>Beschreibung</label>
        <textarea name="description"></textarea>

        <label>Logo URL</label>
        <input type="url" name="logo">

        <label>Partner Link</label>
        <input type="url" name="url">

        <label>
            <input type="checkbox" name="is_active" checked>
            Aktiv
        </label>

        <button class="btn-accent">Speichern</button>
        <a href="/admin/partners" class="btn-muted">Abbrechen</a>

    </form>

</section>
