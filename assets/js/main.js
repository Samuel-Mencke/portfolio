/**
 * Main Application Module
 * Initializes all components and handles coordination
 */

const PortfolioApp = {
    // State
    isDarkMode: false,
    cursor: null,
    cursorDot: null,
    readmeModal: null,
    
    /**
     * Initialize the application
     */
    init() {
        this.initDarkMode();
        this.initCursor();
        this.initScrollProgress();
        this.initSmoothScroll();
        this.initAnimations();
        this.initContactForm();
        this.initKonamiCode();
        this.initReadmeModal();
    },
    
    /**
     * Initialize Dark Mode
     */
    initDarkMode() {
        const body = document.body;
        const toggle = document.getElementById('darkModeToggle');
        const mobileToggle = document.getElementById('mobileDarkModeToggle');
        const mobileIcon = document.getElementById('mobileDarkModeIcon');
        const toggleText = toggle?.querySelector('.toggle-text');
        
        // Load from localStorage
        const savedMode = localStorage.getItem('dark-mode');
        this.isDarkMode = savedMode === 'true' || savedMode === null; // Default to dark
        
        this.applyDarkMode(this.isDarkMode);
        
        // Desktop toggle
        if (toggle) {
            toggle.addEventListener('click', () => {
                this.isDarkMode = !this.isDarkMode;
                this.applyDarkMode(this.isDarkMode);
                localStorage.setItem('dark-mode', this.isDarkMode);
            });
        }
        
        // Mobile toggle
        if (mobileToggle && mobileIcon) {
            mobileToggle.addEventListener('click', () => {
                this.isDarkMode = !this.isDarkMode;
                this.applyDarkMode(this.isDarkMode);
                localStorage.setItem('dark-mode', this.isDarkMode);
            });
        }
    },
    
    /**
     * Apply dark mode styles
     */
    applyDarkMode(isDark) {
        const body = document.body;
        const toggleText = document.querySelector('.toggle-text');
        const mobileIcon = document.getElementById('mobileDarkModeIcon');
        
        if (isDark) {
            body.classList.add('dark-mode');
            if (toggleText) toggleText.textContent = 'light';
            if (mobileIcon) mobileIcon.textContent = '☀️';
        } else {
            body.classList.remove('dark-mode');
            if (toggleText) toggleText.textContent = 'dark';
            if (mobileIcon) mobileIcon.textContent = '🌙';
        }
        
        // Update Three.js theme
        if (window.threeBackground) {
            window.threeBackground.updateTheme(isDark);
        }
    },
    
    /**
     * Initialize Custom Cursor
     */
    initCursor() {
        this.cursor = document.querySelector('.cursor');
        this.cursorDot = document.querySelector('.cursor-dot');
        
        // Disable on touch devices
        if (window.matchMedia('(pointer: coarse)').matches) {
            if (this.cursor) this.cursor.style.display = 'none';
            if (this.cursorDot) this.cursorDot.style.display = 'none';
            document.body.style.cursor = 'auto';
            return;
        }
        
        if (!this.cursor || !this.cursorDot) return;
        
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;
        let dotX = 0, dotY = 0;
        
        // Track mouse position
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
        
        // Animation loop
        const animate = () => {
            // Smooth follow
            cursorX += (mouseX - cursorX) * 0.1;
            cursorY += (mouseY - cursorY) * 0.1;
            dotX += (mouseX - dotX) * 0.3;
            dotY += (mouseY - dotY) * 0.3;
            
            // Apply transforms
            this.cursor.style.transform = `translate(${cursorX - 10}px, ${cursorY - 10}px)`;
            this.cursorDot.style.transform = `translate(${dotX - 2}px, ${dotY - 2}px)`;
            
            requestAnimationFrame(animate);
        };
        
        animate();
        
        // Hover effects
        const interactiveElements = document.querySelectorAll('a, button, .project, .project-card-3d');
        interactiveElements.forEach(el => {
            el.addEventListener('mouseenter', () => {
                this.cursor?.style.setProperty('--cursor-scale', '1.5');
            });
            el.addEventListener('mouseleave', () => {
                this.cursor?.style.setProperty('--cursor-scale', '1');
            });
        });
    },
    
    /**
     * Initialize Reading Progress Bar
     */
    initScrollProgress() {
        const progressBar = document.querySelector('.reading-progress');
        if (!progressBar) return;
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (scrollTop / docHeight) * 100;
            progressBar.style.width = `${progress}%`;
        });
    },
    
    /**
     * Initialize Smooth Scroll for navigation
     */
    initSmoothScroll() {
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = link.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    const offset = 100;
                    const targetPosition = targetSection.offsetTop - offset;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update active state
                    navLinks.forEach(nl => nl.classList.remove('active'));
                    link.classList.add('active');
                }
            });
        });
        
        // Update active nav on scroll
        window.addEventListener('scroll', () => {
            const sections = document.querySelectorAll('section[id]');
            const scrollPos = window.scrollY + 150;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        });
    },
    
    /**
     * Initialize Scroll Animations
     */
    initAnimations() {
        // Skill bars animation
        const skillBars = document.querySelectorAll('.skill-progress');
        
        const skillObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const progress = entry.target.getAttribute('data-progress');
                    entry.target.style.width = `${progress}%`;
                    skillObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        skillBars.forEach(bar => skillObserver.observe(bar));
        
        // Section fade-in animations
        const sections = document.querySelectorAll('.section');
        
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('section-visible');
                    sectionObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
        
        sections.forEach(section => sectionObserver.observe(section));
    },
    
    /**
     * Initialize Contact Form
     */
    initContactForm() {
        const form = document.getElementById('contactForm');
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = form.querySelector('.submit-button');
            const originalText = submitBtn?.textContent || 'Send message';
            
            // Get form data
            const formData = {
                name: document.getElementById('name')?.value,
                email: document.getElementById('email')?.value,
                message: document.getElementById('message')?.value
            };
            
            // Validate
            if (!formData.name || !formData.email || !formData.message) {
                alert('Please fill in all fields');
                return;
            }
            
            // Loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending...';
            }
            
            try {
                const response = await fetch('send_mail.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (submitBtn) {
                    submitBtn.textContent = 'Message sent!';
                    submitBtn.style.background = '#22c55e';
                }
                
                form.reset();
                
                setTimeout(() => {
                    if (submitBtn) {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        submitBtn.style.background = '';
                    }
                }, 3000);
                
            } catch (error) {
                console.error('Form submission error:', error);
                if (submitBtn) {
                    submitBtn.textContent = 'Error - Try again';
                    submitBtn.style.background = '#ef4444';
                }
                
                setTimeout(() => {
                    if (submitBtn) {
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        submitBtn.style.background = '';
                    }
                }, 3000);
            }
        });
    },
    
    /**
     * Initialize Konami Code Easter Egg
     */
    initKonamiCode() {
        const konamiCode = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 
                           'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 
                           'b', 'a'];
        let konamiIndex = 0;
        
        document.addEventListener('keydown', (e) => {
            if (e.key === konamiCode[konamiIndex]) {
                konamiIndex++;
                
                if (konamiIndex === konamiCode.length) {
                    this.triggerEasterEgg();
                    konamiIndex = 0;
                }
            } else {
                konamiIndex = 0;
            }
        });
    },
    
    triggerEasterEgg() {
        // Rainbow background effect
        document.body.style.background = 'linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57, #ff9ff3)';
        document.body.style.backgroundSize = '400% 400%';
        document.body.style.animation = 'rainbow 3s ease infinite';
        
        // Add keyframes if not exists
        if (!document.getElementById('rainbow-style')) {
            const style = document.createElement('style');
            style.id = 'rainbow-style';
            style.textContent = `
                @keyframes rainbow {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
            `;
            document.head.appendChild(style);
        }
        
        setTimeout(() => {
            document.body.style.background = '';
            document.body.style.animation = '';
        }, 5000);
    },
    
    /**
     * Initialize Project Modal
     */
    initReadmeModal() {
        this.projectModal = document.getElementById('readmeModal');
        const closeBtn = this.projectModal?.querySelector('.project-modal-close');
        const backdrop = this.projectModal?.querySelector('.project-modal-backdrop');
        this.modalScroll = this.projectModal?.querySelector('.project-modal-scroll');
        this.imageSection = this.projectModal?.querySelector('.project-image-section');
        
        if (!this.projectModal) return;
        
        // Close modal handlers
        closeBtn?.addEventListener('click', () => this.closeProjectModal());
        backdrop?.addEventListener('click', () => this.closeProjectModal());
        
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.projectModal.classList.contains('active')) {
                this.closeProjectModal();
            }
        });
        
        // Initialize carousel navigation
        this.initCarouselNavigation();
        
        // Initialize scroll behavior for image section
        this.initModalScrollBehavior();
    },
    
    /**
     * Initialize modal scroll behavior - hide image on scroll
     */
    initModalScrollBehavior() {
        if (!this.modalScroll || !this.imageSection) return;
        
        let lastScrollTop = 0;
        const imageHeight = 420; // Height of image section
        const scrollThreshold = 100; // Start hiding after 100px scroll
        
        this.modalScroll.addEventListener('scroll', () => {
            const scrollTop = this.modalScroll.scrollTop;
            
            // Hide image when scrolling down past threshold
            if (scrollTop > scrollThreshold) {
                const progress = Math.min((scrollTop - scrollThreshold) / (imageHeight - scrollThreshold), 1);
                this.imageSection.style.transform = `translateY(-${progress * 100}%)`;
                this.imageSection.style.opacity = 1 - progress;
            } else {
                this.imageSection.style.transform = 'translateY(0)';
                this.imageSection.style.opacity = 1;
            }
            
            lastScrollTop = scrollTop;
        });
    },
    
    /**
     * Initialize carousel navigation
     */
    initCarouselNavigation() {
        // Navigation buttons will be initialized when modal opens
        // because they are inside the modal
    },
    
    /**
     * Setup carousel navigation for current modal
     */
    setupCarouselNavigation() {
        const prevBtn = this.projectModal?.querySelector('.carousel-prev');
        const nextBtn = this.projectModal?.querySelector('.carousel-next');
        
        // Remove old listeners
        const newPrevBtn = prevBtn?.cloneNode(true);
        const newNextBtn = nextBtn?.cloneNode(true);
        
        if (prevBtn && newPrevBtn) {
            prevBtn.parentNode.replaceChild(newPrevBtn, prevBtn);
        }
        if (nextBtn && newNextBtn) {
            nextBtn.parentNode.replaceChild(newNextBtn, nextBtn);
        }
        
        // Add new listeners
        newPrevBtn?.addEventListener('click', () => this.navigateCarousel(-1));
        newNextBtn?.addEventListener('click', () => this.navigateCarousel(1));
        
        // Touch/swipe support
        const carousel = this.projectModal?.querySelector('.project-image-container');
        if (carousel) {
            let startX = 0;
            let isDragging = false;
            
            carousel.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isDragging = true;
            }, { passive: true });
            
            carousel.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
            }, { passive: true });
            
            carousel.addEventListener('touchend', (e) => {
                if (!isDragging) return;
                isDragging = false;
                
                const endX = e.changedTouches[0].clientX;
                const diff = startX - endX;
                
                if (Math.abs(diff) > 50) {
                    if (diff > 0) this.navigateCarousel(1);
                    else this.navigateCarousel(-1);
                }
            }, { passive: true });
        }
    },
    
    /**
     * Navigate carousel
     */
    navigateCarousel(direction) {
        if (!this.carouselState) return;
        
        const { currentIndex, totalSlides } = this.carouselState;
        let newIndex = currentIndex + direction;
        
        if (newIndex < 0) newIndex = totalSlides - 1;
        if (newIndex >= totalSlides) newIndex = 0;
        
        this.goToSlide(newIndex);
    },
    
    /**
     * Go to specific slide
     */
    goToSlide(index) {
        if (!this.carouselState) return;
        
        const track = this.projectModal?.querySelector('.carousel-track');
        const dots = this.projectModal?.querySelectorAll('.carousel-dot');
        
        if (!track) return;
        
        this.carouselState.currentIndex = index;
        track.style.transform = `translateX(-${index * 100}%)`;
        
        // Update dots
        if (dots) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }
    },
    
    /**
     * Open project modal for a repository
     */
    openReadmeModal(owner, repo, repoUrl, userForkUrl = null) {
        if (!this.projectModal) return;
        
        // Store fork URL
        this.currentForkUrl = userForkUrl;
        
        // Show modal
        this.projectModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Reset scroll position
        if (this.modalScroll) {
            this.modalScroll.scrollTop = 0;
        }
        
        // Reset image section visibility
        if (this.imageSection) {
            this.imageSection.style.transform = 'translateY(0)';
            this.imageSection.style.opacity = 1;
        }
        
        // Reset carousel
        this.carouselState = { currentIndex: 0, totalSlides: 0 };
        
        // Show loading state
        const contentEl = document.getElementById('projectReadmeContent');
        contentEl.innerHTML = `
            <div class="readme-loading">
                <div class="loading-spinner"></div>
                <p>Loading project details...</p>
            </div>
        `;
        
        // Reset carousel
        const carouselSection = document.getElementById('projectImageCarousel');
        carouselSection.classList.add('no-images');
        
        // Fetch project content
        this.fetchProjectContent(owner, repo);
    },
    
    /**
     * Close project modal
     */
    closeProjectModal() {
        if (!this.projectModal) return;
        
        this.projectModal.classList.remove('active');
        document.body.style.overflow = '';
        
        // Reset scroll position
        if (this.modalScroll) {
            this.modalScroll.scrollTop = 0;
        }
        
        // Reset image section
        if (this.imageSection) {
            this.imageSection.style.transform = 'translateY(0)';
            this.imageSection.style.opacity = 1;
        }
        
        // Reset fork URL
        this.currentForkUrl = null;
        
        // Hide fork link
        const forkLinkEl = document.getElementById('projectModalForkLink');
        if (forkLinkEl) {
            forkLinkEl.classList.add('hidden');
        }
    },
    
    /**
     * Fetch project content from API
     */
    async fetchProjectContent(owner, repo) {
        const contentEl = document.getElementById('projectReadmeContent');
        const titleEl = document.getElementById('projectModalTitle');
        const linkEl = document.getElementById('projectModalLink');
        const languageEl = document.getElementById('projectModalLanguage');
        const starsEl = document.getElementById('projectModalStars');
        const topicsEl = document.getElementById('projectModalTopics');
        const carouselSection = document.getElementById('projectImageCarousel');
        
        try {
            const response = await fetch(`api/readme.php?owner=${encodeURIComponent(owner)}&repo=${encodeURIComponent(repo)}`);
            const data = await response.json();
            
            if (!response.ok || data.error) {
                throw new Error(data.error || 'Failed to load project');
            }
            
            // Update project info
            if (data.repo) {
                if (titleEl) titleEl.textContent = data.repo.name;
                if (linkEl) linkEl.href = data.repo.html_url;
                if (languageEl) {
                    const langText = languageEl.querySelector('.meta-text');
                    if (langText) langText.textContent = data.repo.language;
                }
                if (starsEl) {
                    const starsText = starsEl.querySelector('.meta-text');
                    if (starsText) starsText.textContent = data.repo.stargazers_count;
                }
                
                // Update topics
                if (topicsEl && data.repo.topics && data.repo.topics.length > 0) {
                    topicsEl.innerHTML = data.repo.topics.slice(0, 5).map(topic => 
                        `<span class="topic-tag">${topic}</span>`
                    ).join('');
                } else if (topicsEl) {
                    topicsEl.innerHTML = '';
                }
            }
            
            // Setup carousel
            console.log('Images from API:', data.images);
            if (data.images && data.images.length > 0) {
                console.log('Setting up carousel with', data.images.length, 'images');
                this.setupCarousel(data.images);
                this.setupCarouselNavigation();
                carouselSection.classList.remove('no-images');
            } else {
                console.log('No images found, hiding carousel section');
                carouselSection.classList.add('no-images');
            }
            
            // Show fork link if available
            const forkLinkEl = document.getElementById('projectModalForkLink');
            if (forkLinkEl) {
                if (this.currentForkUrl) {
                    forkLinkEl.href = this.currentForkUrl;
                    forkLinkEl.classList.remove('hidden');
                } else {
                    forkLinkEl.classList.add('hidden');
                }
            }
            
            // Display content
            contentEl.innerHTML = data.content || `
                <div class="readme-error">
                    <div class="readme-error-icon">📝</div>
                    <p>No README available for this project.</p>
                </div>
            `;
            
        } catch (error) {
            contentEl.innerHTML = `
                <div class="readme-error">
                    <div class="readme-error-icon">⚠️</div>
                    <p>Could not load project details.</p>
                    <p style="font-size: 0.9rem; color: var(--tertiary-text); margin-top: 10px;">${error.message}</p>
                </div>
            `;
        }
    },
    
    /**
     * Setup image carousel
     */
    setupCarousel(images) {
        const track = this.projectModal?.querySelector('.carousel-track');
        const dotsContainer = this.projectModal?.querySelector('.carousel-dots');
        
        if (!track || !dotsContainer) return;
        
        // Create slides with error handling
        track.innerHTML = images.map((img, index) => {
            const safeAlt = img.alt || 'Screenshot ' + (index + 1);
            return '<div class="carousel-slide" data-index="' + index + '">' +
                '<img src="' + img.url + '" alt="' + safeAlt + '" loading="' + (index === 0 ? 'eager' : 'lazy') + '">' +
                '</div>';
        }).join('');
        
        // Add image error handlers after DOM insertion
        track.querySelectorAll('.carousel-slide img').forEach((img, index) => {
            img.addEventListener('error', function() {
                console.error('Failed to load image:', this.src);
                const slide = this.closest('.carousel-slide');
                const alt = images[index] && images[index].alt ? images[index].alt : 'Screenshot ' + (index + 1);
                slide.innerHTML = '<div class="carousel-error"><div class="carousel-error-icon"></div><div>' + alt + '</div><div class="carousel-error-text">Bild nicht verfügbar</div></div>';
            });
        });
        
        // Create dots
        dotsContainer.innerHTML = images.map((_, index) => 
            '<button class="carousel-dot ' + (index === 0 ? 'active' : '') + '" data-index="' + index + '" aria-label="Go to slide ' + (index + 1) + '"></button>'
        ).join('');
        
        // Add dot click handlers
        dotsContainer.querySelectorAll('.carousel-dot').forEach(dot => {
            dot.addEventListener('click', () => {
                const index = parseInt(dot.dataset.index);
                this.goToSlide(index);
            });
        });
        
        // Initialize state
        this.carouselState = {
            currentIndex: 0,
            totalSlides: images.length
        };
        
        // Reset position
        track.style.transform = 'translateX(0)';
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    PortfolioApp.init();
});

// Make PortfolioApp globally available
window.PortfolioApp = PortfolioApp;
