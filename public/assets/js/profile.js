/* =========================================================
   PROFILE.JS – Avatar Preview & UX
========================================================= */

(function () {
    const fileInput = document.querySelector('input[type="file"][name="avatar"]');
    const preview   = document.getElementById('avatarPreview');
    const submitBtn = document.getElementById('avatarSubmitBtn');

    if (!fileInput || !preview || !submitBtn) return;

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;

        // Größe prüfen (2 MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Avatar darf maximal 2 MB groß sein.');
            fileInput.value = '';
            submitBtn.disabled = true;
            return;
        }

        // Typ prüfen
        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            alert('Nur JPG oder PNG erlaubt.');
            fileInput.value = '';
            submitBtn.disabled = true;
            return;
        }

        // Live Preview
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        submitBtn.disabled = false;
    });
})();
