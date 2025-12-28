/**
 * Engels811 Network – Community Scripts
 * - Live-Status (Twitch)
 * - Erweiterbar für Community-Features
 */

document.addEventListener('DOMContentLoaded', () => {

    /* =====================================================
       LIVE STATUS (Twitch)
    ===================================================== */

    const liveBadge = document.getElementById('liveBadge');
    const API_URL   = '/api/twitch-status.php';

    async function checkLiveStatus() {
        try {
            const response = await fetch(API_URL, {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                console.warn('[Community] Live API not reachable:', response.status);
                return;
            }

            const data = await response.json();

            if (data.live === true) {
                if (liveBadge) {
                    liveBadge.style.display = 'inline-flex';
                    liveBadge.classList.add('is-live');

                    if (data.title) {
                        liveBadge.title = data.title;
                    }
                }

                document.body.classList.add('stream-live');

            } else {
                if (liveBadge) {
                    liveBadge.style.display = 'none';
                    liveBadge.classList.remove('is-live');
                    liveBadge.removeAttribute('title');
                }

                document.body.classList.remove('stream-live');
            }

        } catch (err) {
            console.warn('[Community] Live status check failed', err);
        }
    }

    // Initial prüfen
    checkLiveStatus();

    // Alle 60 Sekunden erneut prüfen
    setInterval(checkLiveStatus, 60 * 1000);

});
