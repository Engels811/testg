<?php
/**
 * Engels811 Network – Alle Spiele
 * IDENTISCHES DESIGN wie About-Seite
 */

$gamesJson = json_encode($games ?? [], JSON_UNESCAPED_UNICODE);
?>

<section class="section section-soft">
    <div class="container">

        <h1 class="section-title">Alle <span>Spiele</span></h1>

        <!-- TOOLBAR -->
        <div class="games-toolbar">
            <input id="game-search" placeholder="Spiel suchen…" class="games-search">

            <select id="sort" class="games-sort">
                <option value="hours_desc">Stunden ↓</option>
                <option value="hours_asc">Stunden ↑</option>
                <option value="name">Name A–Z</option>
                <option value="last">Zuletzt gespielt</option>
            </select>

            <div id="category-filters" class="category-filters"></div>
        </div>

        <!-- GRID (5 SPIELE NEBENEINANDER) -->
        <div id="games-grid" class="games-grid hero games-grid-5"></div>

    </div>
</section>

<style>
/* 5 Spiele nebeneinander */
.games-grid-5 {
    grid-template-columns: repeat(5, 1fr) !important;
    gap: 1.5rem;
}

/* Responsive Anpassungen */
@media (max-width: 1600px) {
    .games-grid-5 {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

@media (max-width: 1200px) {
    .games-grid-5 {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .games-grid-5 {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 480px) {
    .games-grid-5 {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
/* =========================================================
   STATE
========================================================= */
let ALL_GAMES = <?= $gamesJson ?>;
console.log('✅ Games geladen:', ALL_GAMES.length);

let FILTER = { search:'', cat:'all', sort:'hours_desc' };

/* =========================================================
   HELPERS
========================================================= */
const daysAgo = ts => {
    if (!ts) return null;
    return Math.floor((Date.now()/1000 - ts) / 86400);
};

/* =========================================================
   INIT
========================================================= */
ALL_GAMES = ALL_GAMES.map(g => ({
    ...g,
    hours: Number(g.hours) || 0,
    category: g.category || 'Other',
    is_top: Number(g.is_top) || 0
}));

renderCategories();
renderGames();

/* =========================================================
   CATEGORIES
========================================================= */
function renderCategories() {
    const wrap = document.getElementById('category-filters');
    const cats = ['all', ...new Set(ALL_GAMES.map(g => g.category))].sort();

    wrap.innerHTML = cats.map(c =>
        `<button data-cat="${c}" class="${c==='all'?'active':''}">${c}</button>`
    ).join('');
}

/* =========================================================
   RENDER
========================================================= */
function renderGames() {
    let list = [...ALL_GAMES];

    // Filter
    if (FILTER.cat !== 'all')
        list = list.filter(g => g.category === FILTER.cat);

    if (FILTER.search)
        list = list.filter(g =>
            g.name.toLowerCase().includes(FILTER.search)
        );

    // Sortierung
    list.sort((a,b)=>{
        if (FILTER.sort === 'name') return a.name.localeCompare(b.name);
        if (FILTER.sort === 'last') return (b.last_played||0)-(a.last_played||0);
        if (FILTER.sort === 'hours_asc') return a.hours-b.hours;
        return b.hours-a.hours;
    });

    const grid = document.getElementById('games-grid');
    
    if (!list.length) {
        grid.innerHTML = '<div class="card center">Keine Spiele gefunden.</div>';
        return;
    }

    // Total für Progress-Bar
    const total = list.reduce((s, g) => s + Number(g.hours || 0), 0);
    grid.innerHTML = '';

    list.forEach((g, i) => {
        const progress = total ? Math.min(100, (Number(g.hours) / total) * 100) : 0;

        grid.insertAdjacentHTML('beforeend', `
            <article class="game-card glass ${i === 0 ? 'hero is-top' : ''}"
                     onclick="location.href='/games/${g.slug}'">

                <span class="game-badge provider">${g.provider}</span>
                <span class="game-badge rank">#${i + 1}</span>

                ${g.is_top === 1
                    ? '<span class="game-badge top">TOP</span>'
                    : ''}

                <div class="game-cover">
                    <img
                        src="${g.cover || '/assets/img/game-placeholder.jpg'}"
                        alt="${g.name}"
                        loading="lazy">

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
   EVENTS
========================================================= */
document.getElementById('game-search').oninput = e => {
    FILTER.search = e.target.value.toLowerCase();
    renderGames();
};

document.getElementById('sort').onchange = e => {
    FILTER.sort = e.target.value;
    renderGames();
};

document.addEventListener('click', e => {
    if (e.target.dataset.cat) {
        document.querySelectorAll('#category-filters button')
            .forEach(b => b.classList.remove('active'));

        e.target.classList.add('active');
        FILTER.cat = e.target.dataset.cat;
        renderGames();
    }
});
</script>