// ==========================================================
// FORUM MODAL – DEBUG VERSION
// ==========================================================

console.log('=== FORUM MODAL DEBUG START ===');

// Check if modal exists
const modal = document.getElementById('threadModal');
console.log('Modal element:', modal);

if (!modal) {
    console.error('❌ Modal #threadModal not found in DOM!');
    console.log('Available elements with "modal":', document.querySelectorAll('[id*="modal"]'));
} else {
    console.log('✅ Modal found');
    console.log('Modal classes:', modal.className);
    console.log('Modal aria-hidden:', modal.getAttribute('aria-hidden'));
}

// Check for buttons
const buttons = document.querySelectorAll('[data-open-thread-modal]');
console.log('Open buttons found:', buttons.length);
buttons.forEach((btn, i) => {
    console.log(`Button ${i}:`, btn);
    console.log(`  - Category:`, btn.dataset.category);
    console.log(`  - Text:`, btn.textContent.trim());
});

(function () {
    'use strict';

    const modal = document.getElementById('threadModal');
    if (!modal) {
        console.error('❌ Modal initialization failed - element not found');
        return;
    }

    const form = modal.querySelector('#threadCreateForm');
    const categoryInput = modal.querySelector('#threadCategory');

    console.log('Form:', form);
    console.log('Category input:', categoryInput);

    if (!form || !categoryInput) {
        console.error('❌ Form elements missing');
        return;
    }

    let isOpen = false;

    // -------------------------
    // OPEN MODAL
    // -------------------------
    const openModal = (categorySlug) => {
        console.log('🔥 openModal called with:', categorySlug);
        
        if (isOpen) {
            console.warn('⚠️ Modal already open');
            return;
        }

        form.action = `/forum/${categorySlug}/store`;
        categoryInput.value = categorySlug;

        console.log('Setting form action to:', form.action);
        console.log('Setting category to:', categoryInput.value);

        modal.classList.remove('closing');
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');

        console.log('Modal classes after open:', modal.className);
        console.log('Modal display:', window.getComputedStyle(modal).display);
        console.log('Modal opacity:', window.getComputedStyle(modal).opacity);
        console.log('Modal z-index:', window.getComputedStyle(modal).zIndex);

        isOpen = true;

        // Focus first input
        const firstInput = form.querySelector('input[type="text"]');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    };

    // -------------------------
    // CLOSE MODAL
    // -------------------------
    const closeModal = () => {
        console.log('🔒 closeModal called');
        
        if (!isOpen) {
            console.warn('⚠️ Modal already closed');
            return;
        }

        modal.classList.add('closing');
        modal.classList.remove('active');

        setTimeout(() => {
            modal.classList.remove('closing');
            modal.setAttribute('aria-hidden', 'true');
            isOpen = false;
            
            // Reset form
            form.reset();
            console.log('✅ Modal closed and form reset');
        }, 350);
    };

    // -------------------------
    // EVENT LISTENERS
    // -------------------------

    // Open buttons
    document.addEventListener('click', (e) => {
        console.log('Click detected on:', e.target);
        
        const btn = e.target.closest('[data-open-thread-modal]');
        if (!btn) return;

        console.log('🎯 Open button clicked!', btn);
        e.preventDefault();

        const category = btn.dataset.category;
        console.log('Category from button:', category);
        
        if (!category) {
            console.error('❌ No category defined on button');
            return;
        }

        openModal(category);
    });

    // Close buttons
    modal.addEventListener('click', (e) => {
        if (e.target.closest('[data-close-thread-modal]')) {
            console.log('Close button clicked');
            closeModal();
        }
    });

    // Click outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            console.log('Clicked outside modal');
            closeModal();
        }
    });

    // ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            console.log('ESC pressed');
            closeModal();
        }
    });

    console.log('✅ ForumModal initialized successfully');
    console.log('=== FORUM MODAL DEBUG END ===');

})();
