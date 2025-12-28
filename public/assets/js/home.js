async function loadFeaturedImages() {
    try {
        const response = await fetch('/api/bot/images.php?action=featured&limit=6');
        const data = await response.json();

        const container = document.getElementById('featuredImages');

        if (data.success && data.data.images.length > 0) {
            container.innerHTML = data.data.images.map(img => `
                <div class="gallery-item">
                    <img src="${img.image_url}" alt="${img.prompt}" loading="lazy">
                    <div class="gallery-overlay">
                        <h3>${img.prompt}</h3>
                        <p>${new Date(img.created_at).toLocaleDateString('de-DE')}</p>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="muted">Noch keine Bilder verfügbar.</p>';
        }
    } catch {
        document.getElementById('featuredImages').innerHTML = '<p class="error">Fehler beim Laden.</p>';
    }
}

async function loadLatestVideos() {
    try {
        const response = await fetch('/api/bot/videos.php?action=list&limit=3');
        const data = await response.json();

        const container = document.getElementById('latestVideos');

        if (data.success && data.data.videos.length > 0) {
            container.innerHTML = data.data.videos.map(video => `
                <div class="video-card">
                    <img src="https://img.youtube.com/vi/${video.video_id}/maxresdefault.jpg">
                    <h3>${video.title}</h3>
                    <a href="https://youtube.com/watch?v=${video.video_id}" target="_blank" class="btn btn-primary">
                        Video ansehen
                    </a>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p class="muted">Noch keine Videos verfügbar.</p>';
        }
    } catch {
        document.getElementById('latestVideos').innerHTML = '<p class="error">Fehler beim Laden.</p>';
    }
}

function loadStats() {
    document.getElementById('streamHours').textContent = '500+';
    document.getElementById('communityMembers').textContent = '1.234';
    document.getElementById('aiImagesCount').textContent = '50+';
    document.getElementById('videosCount').textContent = '25+';
}

document.addEventListener('DOMContentLoaded', () => {
    loadFeaturedImages();
    loadLatestVideos();
    loadStats();
});
