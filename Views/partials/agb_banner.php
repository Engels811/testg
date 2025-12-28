<?php if (!empty($_SESSION['user']) && empty($_SESSION['user']['agb_ok'])): ?>
<div class="agb-banner">
    <p>
        ⚠️ Bitte akzeptiere unsere
        <a href="/agb">AGB</a> und
        <a href="/datenschutz">Datenschutzerklärung</a>
    </p>
    <a href="/agb/accept" class="btn btn-primary small">Akzeptieren</a>
</div>
<?php endif; ?>
