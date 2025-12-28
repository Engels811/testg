<?php
/**
 * Engels811 Network – About
 * Inhalte:
 * - Hero
 * - Intro
 * - Top Spiele (neues Game-Card-System)
 * - Community Stats
 */
?>

<!-- =========================
 HERO
========================= -->
<section class="hero hero--content">
    <div class="hero-container">
        <h1>Über <span class="accent">Engels811</span></h1>
        <p>Streamer · Gamer · Content Creator</p>
    </div>
</section>

<!-- =========================
 INTRO
========================= -->
<section class="section">
    <div class="container about-grid">

        <div class="about-logo">
            <img
                src="https://i.ibb.co/ns1czZv9/Brennender-Wolf-und-Flammen-Sym33bole-removebg-preview.png"
                alt="Engels811 Logo">
        </div>

        <div class="about-content">
            <h2>Hey, ich bin <span class="accent">Engels811</span></h2>

            <p>
                Willkommen auf meiner offiziellen Plattform.
                Was als Hobby begann, ist heute ein digitales Zuhause
                für Gaming, Streams, Projekte und Community.
            </p>

            <div class="hero-buttons">
                <a href="https://twitch.tv/engels811" target="_blank" class="btn btn-primary">
                    Twitch
                </a>
                <a href="https://youtube.com/@engels811_ttv" target="_blank" class="btn btn-secondary">
                    YouTube
                </a>
            </div>
        </div>

    </div>
</section>

<!-- =========================
 TOP SPIELE – NEUES SYSTEM
========================= -->
<section class="section section-soft">
    <div class="container">

        <h2 class="section-title">Top <span>Spiele</span></h2>

        <div class="total-playtime">
            Gesamtspielzeit:
            <strong id="total-hours">–</strong>
        </div>

        <div id="top-games" class="games-grid hero"></div>

        <div class="top-games-button">
            <a href="/games" class="btn btn-secondary btn-large">
                Alle Spiele ansehen →
            </a>
        </div>

    </div>
</section>

<!-- =========================
 COMMUNITY STATS
========================= -->
<section class="section">
    <div class="container">

        <h2 class="section-title">Community<span>Stats</span></h2>

        <div class="grid grid-4">
            <div class="stat-card">
                <strong id="twitch-hours">–</strong>
                <small>Stream-Stunden</small>
            </div>

            <div class="stat-card">
                <strong id="twitch-followers">–</strong>
                <small>Follower</small>
            </div>

            <div class="stat-card">
                <strong id="twitch-videos">–</strong>
                <small>Videos</small>
            </div>

            <div class="stat-card">
                <strong id="twitch-live">–</strong>
                <small>Status</small>
            </div>
        </div>

    </div>
</section>

<!-- =========================
 SCRIPTS
========================= -->
<script>
/* =========================
   Twitch Stats
========================= */
fetch('/api/twitch-stats.php')
    .then(r => r.json())
    .then(d => {
        document.getElementById('twitch-followers').textContent = d.followers ?? '–';
        document.getElementById('twitch-videos').textContent   = d.videos ?? '–';
        document.getElementById('twitch-live').textContent     = d.live ? 'LIVE' : 'Offline';
        document.getElementById('twitch-hours').textContent    = d.hours ?? '–';
    })
    .catch(() => {});

/* =========================
   Top Games – FINAL & STABIL
========================= */
fetch('/api/games.php')
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(res => {

        if (!res || res.success !== true || !Array.isArray(res.data)) {
            throw new Error('Ungültige Games API Antwort');
        }

        const games = res.data
            .filter(g => Number(g.is_top) === 1)
            .slice(0, 7);

        if (!games.length) return;

        /* Gesamtspielzeit */
        const total = games.reduce((s, g) => s + Number(g.hours || 0), 0);
        document.getElementById('total-hours').textContent =
            Math.floor(total).toLocaleString() + ' Stunden';

        const grid = document.getElementById('top-games');
        grid.innerHTML = '';

        games.forEach((g, i) => {

            const progress = total
                ? Math.min(100, (Number(g.hours) / total) * 100)
                : 0;

            grid.insertAdjacentHTML('beforeend', `
                <article class="game-card glass ${i === 0 ? 'hero is-top' : ''}"
                         onclick="location.href='/games/${g.slug}'">

                    <span class="game-badge provider">${g.provider}</span>
                    <span class="game-badge rank">#${i + 1}</span>

                    ${Number(g.is_top) === 1
                        ? `<span class="game-badge top">TOP</span>`
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
                                    ? new Date(g.last_played * 1000).toLocaleDateString()
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
    })
    .catch(err => {
        console.error('Top Games Fehler:', err);
    });
</script>
