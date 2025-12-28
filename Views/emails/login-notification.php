<h2>⚠️ Neuer Login erkannt</h2>

<p>Hallo <?= htmlspecialchars($username) ?>,</p>

<p>
Es wurde ein Login von einem neuen Gerät oder Standort festgestellt.
</p>

<ul>
    <li><strong>IP:</strong> <?= htmlspecialchars($ip) ?></li>
    <li><strong>Gerät:</strong> <?= htmlspecialchars($userAgent) ?></li>
</ul>

<p>
Wenn du das nicht warst, sichere bitte sofort dein Konto.
</p>

<p>
<a href="<?= BASE_URL ?>/account/security">
→ Sicherheitseinstellungen öffnen
</a>
</p>
