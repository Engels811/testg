<?php
/**
 * Live Banner Component - Engels811 Network
 * Schwarz/Rot Fire Theme
 */

// Nicht auf Live-Seite anzeigen
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($currentPath, '/live') !== false || strpos($currentPath, '/stream') !== false) {
    return;
}
?>

<style>
/* =========================
   LIVE BANNER – FIRE THEME
========================= */

.live-banner {
    position: fixed;
    top: 64px;
    left: 0;
    right: 0;
    display: none;
    height: 65px;

    background:
        linear-gradient(135deg, rgba(20,20,20,.98) 0%, rgba(40,20,20,.98) 50%, rgba(20,20,20,.98) 100%),
        radial-gradient(ellipse at center, rgba(255,50,50,.15) 0%, transparent 70%);

    border-top: 1px solid rgba(255,50,50,.3);
    border-bottom: 3px solid #ff3232;

    box-shadow:
        0 8px 32px rgba(255,50,50,.3),
        0 0 60px rgba(255,50,50,.15);

    z-index: 900;
    animation: slideDown .4s cubic-bezier(.4,0,.2,1);
}

@keyframes slideDown {
    from { transform: translateY(-100%); opacity: 0; }
    to   { transform: translateY(0); opacity: 1; }
}

.live-banner-inner {
    max-width: 1200px;
    height: 100%;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    color: #fff;
}

.live-dot {
    width: 14px;
    height: 14px;
    background: #ff3232;
    border-radius: 50%;
    box-shadow: 0 0 20px rgba(255,50,50,1);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%,100% { transform: scale(1); }
    50%     { transform: scale(1.3); }
}

.live-badge {
    padding: 6px 14px;
    background: linear-gradient(135deg,#ff3232,#cc0000);
    border-radius: 6px;
    font-size: .75rem;
    font-weight: 900;
    letter-spacing: 1.5px;
}

.live-title {
    font-weight: 800;
    font-size: 1.05rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.live-meta {
    font-size: .85rem;
    opacity: .9;
    display: flex;
    gap: 14px;
}

.live-cta {
    margin-left: auto;
    padding: 11px 26px;
    background: linear-gradient(135deg,#ff3232,#cc0000);
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 800;
    transition: transform .25s;
}

.live-cta:hover { transform: translateY(-2px); }

body.live-active main {
    margin-top: 65px;
}
</style>

<div id="liveBanner" class="live-banner">
    <div class="live-banner-inner">

        <div class="live-indicator">
            <span class="live-dot"></span>
            <span class="live-badge">LIVE</span>
        </div>

        <div class="live-content">
            <div class="live-title" id="liveTitle">🔥 Live auf Twitch</div>
            <div class="live-meta">
                <span id="liveGame">🎮 Just Chatting</span>
                <span id="liveViewers">👁️ 0 Zuschauer</span>
            </div>
        </div>

        <a href="/live" class="live-cta">⚡ Zum Stream</a>

    </div>
</div>

<script>
(function () {
    const banner    = document.getElementById('liveBanner');
    const titleEl   = document.getElementById('liveTitle');
    const gameEl    = document.getElementById('liveGame');
    const viewersEl = document.getElementById('liveViewers');

    if (!banner) return;

    const API_URL = '/api/twitch-status.php';

    async function checkLiveStatus() {
        try {
            const res = await fetch(API_URL + '?_=' + Date.now(), {
                cache: 'no-store',
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) return hideBanner();

            const data = await res.json();

            if (data.live === true) {
                updateBanner(data);
                showBanner();
            } else {
                hideBanner();
            }

        } catch {
            hideBanner();
        }
    }

    function updateBanner(data) {
        titleEl.textContent   = data.title ?? '🔥 Live auf Twitch';
        gameEl.textContent    = '🎮 ' + (data.game_name ?? 'Unbekannt');
        viewersEl.textContent = '👁️ ' + (data.viewer_count ?? 0) + ' Zuschauer';
    }

    function showBanner() {
        banner.style.display = 'block';
        document.body.classList.add('live-active');
    }

    function hideBanner() {
        banner.style.display = 'none';
        document.body.classList.remove('live-active');
    }

    checkLiveStatus();
    setInterval(checkLiveStatus, 60000);
})();
</script>
