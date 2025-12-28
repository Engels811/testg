<?php
$pageTitle = 'Live Stream';
$pageDescription = 'Engels811 Live auf Twitch - Jetzt zuschauen!';
$currentPage = 'live';
?>

<section class="section">
    <div class="container">

        <!-- STREAM + CHAT LAYOUT -->
        <div class="stream-layout">

            <!-- MAIN: PLAYER / OFFLINE -->
            <div class="stream-main">

                <!-- PLAYER -->
                <div class="stream-embed" id="streamPlayer">
                    <iframe
                        src="https://player.twitch.tv/?channel=engels811&parent=engels811-ttv.de&parent=wiki.engels811-ttv.de&parent=www.engels811-ttv.de"
                        allowfullscreen
                        scrolling="no">
                    </iframe>
                </div>

                <!-- OFFLINE BANNER -->
                <div class="stream-offline" id="offlineBanner">
                    <div class="offline-overlay">
                        <h2>⚫ Stream Offline</h2>
                        <p>
                            Der Stream ist aktuell nicht live.<br>
                            Folge mir auf Twitch oder Discord, um nichts zu verpassen.
                        </p>

                        <div class="hero-buttons">
                            <a href="https://twitch.tv/engels811" target="_blank" class="btn btn-primary">
                                Twitch öffnen
                            </a>
                            <a href="https://discord.gg/engels811" target="_blank" class="btn btn-secondary">
                                Discord beitreten
                            </a>
                        </div>
                    </div>
                </div>

                <!-- STATUS -->
                <div class="stream-info">
                    <div id="streamStatus" class="status-loading">
                        Lade Stream-Status …
                    </div>
                </div>

            </div>

            <!-- CHAT -->
            <aside class="stream-chat">
                <h3>💬 Live Chat</h3>
                <iframe
                    src="https://www.twitch.tv/embed/engels811/chat?parent=engels811-ttv.de&parent=wiki.engels811-ttv.de&parent=www.engels811-ttv.de&darkpopout"
                    frameborder="0">
                </iframe>
            </aside>

        </div>

        <!-- STREAM SCHEDULE -->
        <div style="margin-top:3rem;">
            <h2 class="section-title">📅 Stream <span>Schedule</span></h2>

            <div class="grid grid-3">
                <div class="card">
                    <h3>Montag</h3>
                    <p class="time">00:00 – 00:00 Uhr</p>
                    <p class="desc">Streaming Pause</p>
                </div>

                <div class="card">
                    <h3>Dienstag</h3>
                    <p class="time">19:00 - 22:00 Uhr</p>
                    <p class="desc">🎮 Community-Stream / Just Chatting / Casual Gaming</p>
                </div>

                <div class="card">
                    <h3>Mittwoch</h3>
                    <p class="time">20:00 - 23:00 Uhr</p>
                    <p class="desc">🔥 Main Game / Progress / Story</p>
                </div>

                <div class="card">
                    <h3>Donnerstag</h3>
                    <p class="time">19:00 - 21:30 Uhr</p>
                    <p class="desc">🧠 Special Content (Challenges, Mods, Dev-Talk, AI, Technik)</p>
                </div>

                <div class="card">
                    <h3>Freitag</h3>
                    <p class="time">21:00 - 01:00 Uhr</p>
                    <p class="desc">🍻 Late-Night Stream</p>
                </div>

                <div class="card">
                    <h3>Samstag</h3>
                    <p class="time">16:00 - 20:00 Uhr</p>
                    <p class="desc">🎉 Event-Stream / Collabs / Community Games</p>
                </div>

                <div class="card">
                    <h3>Sonntag</h3>
                    <p class="time">18:00 - 21:00 Uhr</p>
                    <p class="desc">😌 Chill-Stream / Recap / Talk</p>
                </div>

                <div class="card highlight">
                    <h3>Spontan</h3>
                    <p class="time">Überraschungen! 🎉</p>
                    <p class="desc">Follow für Benachrichtigungen</p>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
async function checkStreamStatus() {
    const playerWrapper = document.getElementById('streamPlayer');
    const iframe        = playerWrapper.querySelector('iframe');
    const offline       = document.getElementById('offlineBanner');
    const status        = document.getElementById('streamStatus');

    let apiLive = false;

    /* =========================
       1. Twitch API prüfen
    ========================= */
    try {
        const res  = await fetch('/api/twitch-status.php?ts=' + Date.now(), {
            cache: 'no-store'
        });
        const data = await res.json();
        apiLive = data.live === true;
    } catch (e) {
        // API nicht erreichbar → ignorieren
    }

    /* =========================
       2. Player-Fallback
       (entscheidend bei FSK 18)
    ========================= */
    const playerLoaded = iframe && iframe.contentWindow;

    /* =========================
       3. Finale Entscheidung
    ========================= */
    if (apiLive || playerLoaded) {
        playerWrapper.style.display = 'block';
        offline.style.display = 'none';
        status.textContent = '🔴 Stream ist LIVE';
        status.className = 'status-live';
    } else {
        playerWrapper.style.display = 'none';
        offline.style.display = 'block';
        status.textContent = '⚫ Stream aktuell offline';
        status.className = 'status-offline';
    }
}

/* =========================
   Initial + Auto-Refresh
========================= */
document.addEventListener('DOMContentLoaded', () => {
    checkStreamStatus();

    // Nachladen absichern (Player braucht manchmal Zeit)
    setTimeout(checkStreamStatus, 3000);
    setTimeout(checkStreamStatus, 8000);
});

setInterval(checkStreamStatus, 60000);
</script>