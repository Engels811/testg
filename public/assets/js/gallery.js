/**
 * Engels811 Network – Gallery Lightbox
 * - Lightbox
 * - Slideshow (Prev / Next)
 * - Keyboard & Swipe
 * - Report integration
 */

document.addEventListener('DOMContentLoaded', () => {

    console.log('[Gallery] gallery.js loaded');

    /* =====================================================
       ELEMENTE
    ===================================================== */

    const lightbox   = document.getElementById('lightbox');
    const imgEl      = lightbox?.querySelector('.lightbox-image');
    const closeBtn   = lightbox?.querySelector('.lightbox-close');
    const prevBtn    = lightbox?.querySelector('.lightbox-nav.prev');
    const nextBtn    = lightbox?.querySelector('.lightbox-nav.next');

    const reportIdEl = document.getElementById('report-content-id');

    if (!lightbox || !imgEl || !closeBtn) return;

    /* =====================================================
       GALLERY STATE
    ===================================================== */

    const images = Array.from(document.querySelectorAll('.lightbox-trigger'));
    let currentIndex = 0;

    /* =====================================================
       HELPERS
    ===================================================== */

    function showImageByIndex(index) {
        if (!images[index]) return;

        const img = images[index];
        imgEl.src = img.dataset.full;

        if (reportIdEl && img.dataset.id) {
            reportIdEl.value = img.dataset.id;
        }

        currentIndex = index;
    }

    function openLightbox(index) {
        showImageByIndex(index);
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        imgEl.src = '';
        document.body.style.overflow = '';
    }

    function nextImage() {
        const next = (currentIndex + 1) % images.length;
        showImageByIndex(next);
    }

    function prevImage() {
        const prev = (currentIndex - 1 + images.length) % images.length;
        showImageByIndex(prev);
    }

    /* =====================================================
       CLICK EVENTS
    ===================================================== */

    images.forEach((img, index) => {
        img.addEventListener('click', (e) => {
            e.preventDefault();
            openLightbox(index);
        });
    });

    closeBtn.addEventListener('click', closeLightbox);

    prevBtn?.addEventListener('click', prevImage);
    nextBtn?.addEventListener('click', nextImage);

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightbox();
    });

    /* =====================================================
       KEYBOARD
    ===================================================== */

    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;

        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') prevImage();
    });

    /* =====================================================
       SWIPE (MOBILE)
    ===================================================== */

    let touchStartX = 0;

    imgEl.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    imgEl.addEventListener('touchend', (e) => {
        const deltaX = e.changedTouches[0].screenX - touchStartX;

        if (Math.abs(deltaX) > 50) {
            deltaX < 0 ? nextImage() : prevImage();
        }
    });
});
