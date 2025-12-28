<?php
/**
 * Engels811 Network – Games
 * DIREKTE DATEN-EINBINDUNG (ohne API)
 */

// Passe den Pfad an deine Struktur an!
require_once __DIR__ . '/../../app/Core/Database.php';

try {
    $gamesRaw = Database::fetchAll(
        "SELECT
            id, name, slug, provider, category,
            CAST(COALESCE(hours_override, hours, 0) AS DECIMAL(10,1)) AS hours,
            cover, is_top, last_played
         FROM games
         WHERE confirmed = 1
         ORDER BY is_top DESC, hours DESC, name ASC"
    );
    
    $gamesJson = json_encode($gamesRaw, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $gamesJson = '[]';
}
?>

<section class="section section-soft">
    <div class="container">

        <h1 class="section-title">Alle <span>Spiele</span></h1>

        <div class="games-toolbar">
            <input id="game-search" type="text" placeholder="Spiel suchen…" class="games-search">
            
            <select id="game-sort" class="games-sort">
                <option value="hours_desc">Stunden ↓</option>
                <option value="hours_asc">Stunden ↑</option>
                <option value="name_asc">Name A–Z</option>
                <option value="last_played">Zuletzt gespielt</option>
            </select>

            <div id="category-filters" class="category-filters"></div>
        </div>

        <div id="games-grid" class="games-grid games-grid-5"></div>

    </div>
</section>

<script>
let ALL_GAMES = <?= $gamesJson ?>;
let FILTER = { search: '', sort: 'hours_desc', category: 'all' };

console.log('✅ Games geladen:', ALL_GAMES.length);

/* =========================================================
   CATEGORIES RENDERN
========================================================= */
function renderCategories() {
    const categories = ['all', ...new Set(ALL_GAMES.map(g => g.category || 'Other'))].sort();
    const wrap = document.getElementById('category-filters');
    
    wrap.innerHTML = categories.map(cat => 
        `<button data-cat="${cat}" class="${cat === 'all' ? 'active' : ''}">${cat}</button>`
    ).join('');
}

/* =========================================================
   GAMES RENDERN
========================================================= */
function renderGames() {
    let games = [...ALL_GAMES];

    // Filter: Search
    if (FILTER.search) {
        const q = FILTER.search.toLowerCase();
        games = games.filter(g => g.name.toLowerCase().includes(q));
    }

    // Filter: Category
    if (FILTER.category !== 'all') {
        games = games.filter(g => (g.category || 'Other') === FILTER.category);
    }

    // Sortierung
    games.sort((a, b) => {
        switch (FILTER.sort) {
            case 'name_asc': return a.name.localeCompare(b.name);
            case 'hours_asc': return a.hours - b.hours;
            case 'last_played': return (b.last_played || 0) - (a.last_played || 0);
            default: return b.hours - a.hours;
        }
    });

    const grid = document.getElementById('games-grid');
    
    if (!games.length) {
        grid.innerHTML = '<div class="card center">Keine Spiele gefunden.</div>';
        return;
    }

    const total = games.reduce((s, g) => s + Number(g.hours || 0), 0);
    grid.innerHTML = '';

    games.forEach((g, i) => {
        const progress = total ? Math.min(100, (Number(g.hours) / total) * 100) : 0;

        grid.insertAdjacentHTML('beforeend', `
            <article class="game-card glass ${i === 0 ? 'hero' : ''}" 
                     onclick="location.href='/games/${g.slug}'">
                
                <span class="game-badge provider">${g.provider}</span>
                <span class="game-badge rank">#${i + 1}</span>
                ${Number(g.is_top) === 1 ? '<span class="game-badge top">TOP</span>' : ''}

                <div class="game-cover">
                    <img src="${g.cover || '/assets/img/game-placeholder.jpg'}" 
                         alt="${g.name}" loading="lazy">
                    
                    <div class="game-hover-stats">
                        <strong>${Number(g.hours).toFixed(1)} Std</strong>
                        <small>${g.category || '—'}</small>
                        <small>
                            Zuletzt: 
                            ${g.last_played 
                                ? new Date(g.last_played * 1000).toLocaleDateString('de-DE')
                                : '–'}
                        </small>
                    </div>
                </div>

                <h3>${g.name}</h3>
                
                <div class="game-meta">
                    <span><strong>${Number(g.hours).toFixed(1)}</strong> Std</span>
                    <span>${g.category || '—'}</span>
                </div>
                
                <div class="game-progress">
                    <div class="game-progress-bar" style="width:${progress}%"></div>
                </div>
            </article>
        `);
    });
}

/* =========================================================
   INIT
========================================================= */
renderCategories();
renderGames();

/* =========================================================
   EVENT LISTENERS
========================================================= */
document.getElementById('game-search').addEventListener('input', e => {
    FILTER.search = e.target.value;
    renderGames();
});

document.getElementById('game-sort').addEventListener('change', e => {
    FILTER.sort = e.target.value;
    renderGames();
});

document.addEventListener('click', e => {
    if (e.target.dataset.cat) {
        // Alle Buttons deaktivieren
        document.querySelectorAll('#category-filters button')
            .forEach(btn => btn.classList.remove('active'));
        
        // Aktuellen Button aktivieren
        e.target.classList.add('active');
        FILTER.category = e.target.dataset.cat;
        renderGames();
    }
});
</script>