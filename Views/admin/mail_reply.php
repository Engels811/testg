<form method="post" action="/admin/mail/send" class="admin-card">

    <h3>📨 Antwort senden</h3>

    <input type="email" name="email" placeholder="Empfänger E-Mail" required>
    <input type="text" name="name" placeholder="Name (optional)">

    <textarea id="adminMessage" name="message" required></textarea>

    <div style="display:flex;gap:12px;margin-top:16px;">
        <button class="btn-accent">📤 Senden</button>
        <button type="button" class="btn-secondary" onclick="previewMail()">👁 Vorschau</button>
    </div>

</form>

<!-- MODAL: Vorschau -->
<div id="mailPreviewModal" style="display:none;">
    <iframe id="mailPreviewFrame" style="width:100%;height:600px;border:0;"></iframe>
</div>

<script>
function previewMail() {
    const message = tinymce.get('adminMessage').getContent();

    fetch('/admin/mail/preview', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ message })
    })
    .then(r => r.text())
    .then(html => {
        document.getElementById('mailPreviewFrame').srcdoc = html;
        document.getElementById('mailPreviewModal').style.display = 'block';
    });
}
</script>
