/**
 * FORUM LIGHTBOX
 * Engels811 Network
 */

(function() {
    'use strict';

    // =========================================
    // LIGHTBOX CREATION
    // =========================================
    
    const createLightbox = () => {
        const lightbox = document.createElement('div');
        lightbox.className = 'forum-lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-backdrop"></div>
            <div class="lightbox-content">
                <button class="lightbox-close" aria-label="Schließen">✕</button>
                <img class="lightbox-image" src="" alt="">
                <div class="lightbox-controls">
                    <button class="lightbox-prev" aria-label="Vorheriges Bild">‹</button>
                    <button class="lightbox-next" aria-label="Nächstes Bild">›</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(lightbox);
        return lightbox;
    };

    // =========================================
    // LIGHTBOX INSTANCE
    // =========================================
    
    let lightbox = document.querySelector('.forum-lightbox');
    if (!lightbox) {
        lightbox = createLightbox();
    }

    const backdrop = lightbox.querySelector('.lightbox-backdrop');
    const image = lightbox.querySelector('.lightbox-image');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');

    let currentImages = [];
    let currentIndex = 0;

    // =========================================
    // OPEN LIGHTBOX
    // =========================================
    
    const openLightbox = (imgSrc, allImages, startIndex) => {
        currentImages = allImages;
        currentIndex = startIndex;
        
        image.src = imgSrc;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Show/hide navigation
        if (currentImages.length > 1) {
            prevBtn.style.display = 'block';
            nextBtn.style.display = 'block';
        } else {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        }
    };

    // =========================================
    // CLOSE LIGHTBOX
    // =========================================
    
    const closeLightbox = () => {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        image.src = '';
        currentImages = [];
        currentIndex = 0;
    };

    // =========================================
    // NAVIGATION
    // =========================================
    
    const showPrevious = () => {
        if (currentImages.length === 0) return;
        currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
        image.src = currentImages[currentIndex];
    };

    const showNext = () => {
        if (currentImages.length === 0) return;
        currentIndex = (currentIndex + 1) % currentImages.length;
        image.src = currentImages[currentIndex];
    };

    // =========================================
    // EVENT LISTENERS
    // =========================================
    
    // Close button
    closeBtn.addEventListener('click', closeLightbox);
    backdrop.addEventListener('click', closeLightbox);
    
    // Navigation
    prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showPrevious();
    });
    
    nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showNext();
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;
        
        switch(e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                showPrevious();
                break;
            case 'ArrowRight':
                showNext();
                break;
        }
    });

    // =========================================
    // TRIGGER SETUP
    // =========================================
    
    document.querySelectorAll('.lightbox-trigger').forEach((trigger, index) => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get all images in the same post
            const post = this.closest('.forum-post');
            const postImages = post 
                ? Array.from(post.querySelectorAll('.lightbox-trigger')).map(img => img.src)
                : [this.src];
            
            const imgIndex = postImages.indexOf(this.src);
            
            openLightbox(this.src, postImages, imgIndex);
        });
        
        // Add hover effect
        trigger.style.cursor = 'zoom-in';
        trigger.title = 'Zum Vergrößern klicken';
    });

    // =========================================
    // LIGHTBOX STYLES (inject if not present)
    // =========================================
    
    if (!document.getElementById('forum-lightbox-styles')) {
        const style = document.createElement('style');
        style.id = 'forum-lightbox-styles';
        style.textContent = `
            .forum-lightbox {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
                display: none;
                align-items: center;
                justify-content: center;
            }
            
            .forum-lightbox.active {
                display: flex;
            }
            
            .lightbox-backdrop {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(10px);
            }
            
            .lightbox-content {
                position: relative;
                max-width: 95vw;
                max-height: 95vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .lightbox-image {
                max-width: 100%;
                max-height: 95vh;
                object-fit: contain;
                border-radius: 8px;
                box-shadow: 0 20px 80px rgba(255, 26, 26, 0.5);
            }
            
            .lightbox-close {
                position: absolute;
                top: -50px;
                right: 0;
                background: rgba(255, 26, 26, 0.2);
                border: 1px solid rgba(255, 26, 26, 0.4);
                color: #fff;
                font-size: 28px;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .lightbox-close:hover {
                background: rgba(255, 26, 26, 0.4);
                transform: rotate(90deg);
            }
            
            .lightbox-controls {
                position: absolute;
                width: 100%;
                display: flex;
                justify-content: space-between;
                padding: 0 20px;
                pointer-events: none;
            }
            
            .lightbox-prev,
            .lightbox-next {
                pointer-events: all;
                background: rgba(255, 26, 26, 0.2);
                border: 1px solid rgba(255, 26, 26, 0.4);
                color: #fff;
                font-size: 36px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .lightbox-prev:hover,
            .lightbox-next:hover {
                background: rgba(255, 26, 26, 0.4);
                transform: scale(1.1);
            }
            
            @media (max-width: 768px) {
                .lightbox-close {
                    top: 10px;
                    right: 10px;
                    width: 40px;
                    height: 40px;
                    font-size: 24px;
                }
                
                .lightbox-prev,
                .lightbox-next {
                    width: 50px;
                    height: 50px;
                    font-size: 28px;
                }
                
                .lightbox-controls {
                    padding: 0 10px;
                }
            }
        `;
        document.head.appendChild(style);
    }

})();