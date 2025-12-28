<section class="admin-wrapper narrow">

    <h1>➕ Playlist hinzufügen</h1>

    <form method="post" action="/admin/playlists/store" class="admin-form">

        <input type="hidden" name="csrf" value="<?= $csrf ?>">

        <label>Titel</label>
        <input type="text" name="title" required>

        <label>Beschreibung</label>
        <textarea name="description"></textarea>

        <label>Plattform</label>
        <select name="platform" required>
            <option value="spotify">Spotify</option>
            <option value="youtube_music">YouTube Music</option>
            <option value="youtube">YouTube</option>
        </select>

        <label>Embed / URL</label>
        <input type="text" name="embed_url" required>

        <label>Kategorie</label>
        <input type="text" name="category" value="general">

        <label>Sortierung</label>
        <input type="number" name="sort_order" value="0">

        <label>
            <input type="checkbox" name="is_active" checked>
            Aktiv
        </label>

        <button class="btn-accent">Speichern</button>
        <a href="/admin/playlists" class="btn-muted">Abbrechen</a>

    </form>

</section>
