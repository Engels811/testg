<section class="dashboard-wrapper">

    <h1>✏️ Profil bearbeiten</h1>

    <div class="dashboard-grid">

        <div class="card">
            <h3>📧 E-Mail ändern</h3>

            <form method="post" action="/dashboard/profile/update">
                <label>E-Mail-Adresse</label>
                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                    required
                >

                <button class="btn-primary">Speichern</button>
            </form>
        </div>

        <div class="card">
            <h3>🖼 Avatar</h3>
            <p class="muted">Avatar-Upload folgt (vorbereitet).</p>
        </div>

    </div>

</section>
