/**
 * Engels811 Network – Live Status Checker
 * Ruft /api/twitch-status.php ab und steuert das LIVE-Badge im Header
 */

document.addEventListener('DOMContentLoaded', () => {
    const badge = document.getElementById('liveBadge');
    if (!badge) return;

    const API_URL = '/api/twitch-status.php';

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
                console.warn('[LiveStatus] API not reachable:', response.status);
                return;
            }

            const data = await response.json();

            if (data && data.live === true) {
                badge.style.display = 'inline-flex';
                badge.classList.add('is-live');

                // Optional: Tooltip mit Titel
                if (data.title) {
                    badge.title = data.title;
                }
            } else {
                badge.style.display = 'none';
                badge.classList.remove('is-live');
                badge.removeAttribute('title');
            }

        } catch (err) {
            console.warn('[LiveStatus] Check failed', err);
        }
    }

    // Initialer Check
    checkLiveStatus();

    // Alle 60 Sekunden neu prüfen
    setInterval(checkLiveStatus, 60 * 1000);
});
