document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('faviconInput');
    const preview = document.getElementById('faviconPreview');

    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
});
