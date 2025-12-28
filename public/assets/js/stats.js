document.addEventListener("DOMContentLoaded", () => {
    const ids = {
        streamHours: "streamHours",
        communityMembers: "communityMembers",
        aiImagesCount: "aiImagesCount",
        videosCount: "videosCount"
    };

    // Prüfen, ob mindestens ein Stats-Element existiert
    const hasStats = Object.values(ids).some(id => document.getElementById(id));
    if (!hasStats) return; // Seite ohne Stats → Script sauber beenden

    fetch("/api/stats")
        .then(res => {
            if (!res.ok) {
                throw new Error("Stats API nicht erreichbar");
            }
            return res.json();
        })
        .then(data => {
            Object.entries(ids).forEach(([key, id]) => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = data[key] ?? "0";
                }
            });
        })
        .catch(err => {
            console.error("Stats Fehler:", err);
            Object.values(ids).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = "—";
            });
        });
});
