<label>Anbieter</label>
<select name="provider" required>

    <option value="steam" <?= ($game['provider'] ?? '') === 'steam' ? 'selected' : '' ?>>Steam</option>
    <option value="epic" <?= ($game['provider'] ?? '') === 'epic' ? 'selected' : '' ?>>Epic Games</option>
    <option value="rockstar" <?= ($game['provider'] ?? '') === 'rockstar' ? 'selected' : '' ?>>Rockstar</option>
    <option value="fivem" <?= ($game['provider'] ?? '') === 'fivem' ? 'selected' : '' ?>>FiveM</option>
    <option value="ubisoft" <?= ($game['provider'] ?? '') === 'ubisoft' ? 'selected' : '' ?>>Ubisoft</option>
    <option value="ea" <?= ($game['provider'] ?? '') === 'ea' ? 'selected' : '' ?>>EA</option>
    <option value="battlenet" <?= ($game['provider'] ?? '') === 'battlenet' ? 'selected' : '' ?>>Battle.net</option>
    <option value="gog" <?= ($game['provider'] ?? '') === 'gog' ? 'selected' : '' ?>>GOG</option>
    <option value="custom" <?= ($game['provider'] ?? '') === 'custom' ? 'selected' : '' ?>>Custom</option>

</select>
