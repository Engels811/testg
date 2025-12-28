document.addEventListener("DOMContentLoaded", () => {
    const lightbox = document.getElementById("hardwareLightbox");
    const lightboxImg = document.getElementById("lightboxImage");

    if (!lightbox || !lightboxImg) return;

    document.querySelectorAll(".slideshow-track img").forEach(img => {
        img.addEventListener("click", () => {
            lightboxImg.src = img.src;
            lightbox.classList.add("active");
        });
    });

    lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox || e.target.classList.contains("lightbox-close")) {
            lightbox.classList.remove("active");
            lightboxImg.src = "";
        }
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            lightbox.classList.remove("active");
            lightboxImg.src = "";
        }
    });
});
