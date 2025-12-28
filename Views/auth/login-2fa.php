<form method="post" action="/login/2fa">
    <?= Security::csrfField() ?>
    <input type="text" name="code" placeholder="Authenticator-Code" required>
    <button>Anmelden</button>
</form>
