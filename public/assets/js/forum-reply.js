/**
 * FORUM REPLY SYSTEM
 * Engels811 Network
 */

(function() {
    'use strict';

    const textarea = document.querySelector('.forum-reply-card textarea');
    if (!textarea) return;

    // =========================================
    // QUOTE BUTTON HANDLER
    // =========================================
    
    document.querySelectorAll('.quote-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const username = this.dataset.username;
            const postId = this.dataset.postId;
            const postElement = document.getElementById('post-' + postId);
            
            if (!postElement) return;
            
            // Get post content
            const contentElement = postElement.querySelector('.forum-post-content');
            if (!contentElement) return;
            
            const content = contentElement.textContent.trim();
            
            // Format quote
            const quote = `> ${username} schrieb:\n> ${content.split('\n').join('\n> ')}\n\n`;
            
            // Insert into textarea
            const currentValue = textarea.value;
            const cursorPos = textarea.selectionStart || 0;
            
            textarea.value = 
                currentValue.substring(0, cursorPos) +
                quote +
                currentValue.substring(cursorPos);
            
            // Focus textarea and position cursor
            textarea.focus();
            const newCursorPos = cursorPos + quote.length;
            textarea.setSelectionRange(newCursorPos, newCursorPos);
            
            // Scroll to reply form
            document.querySelector('.forum-reply-wrapper').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        });
    });

    // =========================================
    // FILE UPLOAD PREVIEW
    // =========================================
    
    const fileInput = document.querySelector('input[name="attachments[]"]');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const files = this.files;
            if (files.length === 0) return;
            
            let preview = document.querySelector('.file-preview');
            
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'file-preview';
                this.parentElement.after(preview);
            }
            
            preview.innerHTML = '';
            
            Array.from(files).forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    img.style.cssText = 'max-width: 150px; max-height: 150px; margin: 8px; border-radius: 8px;';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    // =========================================
    // KEYBOARD SHORTCUTS
    // =========================================
    
    textarea.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Enter = Submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            this.form.submit();
        }
        
        // Tab = Insert spaces (not focus change)
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = this.selectionStart;
            const end = this.selectionEnd;
            
            this.value = 
                this.value.substring(0, start) +
                '    ' +
                this.value.substring(end);
            
            this.selectionStart = this.selectionEnd = start + 4;
        }
    });

    // =========================================
    // AUTO-SAVE DRAFT (localStorage)
    // =========================================
    
    const draftKey = 'forum_reply_draft_' + window.location.pathname;
    
    // Load draft
    const savedDraft = localStorage.getItem(draftKey);
    if (savedDraft && textarea.value === '') {
        textarea.value = savedDraft;
    }
    
    // Save draft on input
    let saveTimer;
    textarea.addEventListener('input', function() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(() => {
            if (this.value.trim() !== '') {
                localStorage.setItem(draftKey, this.value);
            }
        }, 1000);
    });
    
    // Clear draft on submit
    textarea.form.addEventListener('submit', function() {
        localStorage.removeItem(draftKey);
    });

})();