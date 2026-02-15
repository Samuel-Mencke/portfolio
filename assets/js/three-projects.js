/**
 * Three.js Project Cards
 * Creates 3D-styled project cards with CSS 3D transforms
 * Enhanced with Three.js effects
 */

class ProjectCards3D {
    constructor() {
        this.containers = document.querySelectorAll('.projects-3d-container');
        this.cards = [];
        this.mouse = { x: 0, y: 0 };
        this.isTouchDevice = window.matchMedia('(pointer: coarse)').matches;
        
        this.init();
    }
    
    init() {
        if (this.containers.length === 0) return;
        
        // Select cards from ALL containers
        this.containers.forEach(container => {
            const cards = Array.from(container.querySelectorAll('.project-card-3d, .contributed-card'));
            this.cards = this.cards.concat(cards);
        });
        
        this.setupCards();
        this.addEventListeners();
        this.addScrollAnimations();
    }
    
    setupCards() {
        this.cards.forEach((card, index) => {
            // Set initial 3D styles
            card.style.transformStyle = 'preserve-3d';
            card.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '0';
            card.style.transform = 'translateY(50px) rotateX(10deg)';
            
            // Add depth elements
            this.addCardDepth(card);
            
            // Staggered animation on load
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) rotateX(0)';
            }, 200 + (index * 150));
        });
    }
    
    addCardDepth(card) {
        // Add shine effect overlay
        const shine = document.createElement('div');
        shine.className = 'card-shine';
        shine.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: inherit;
            z-index: 10;
        `;
        card.appendChild(shine);
        
        // Store reference
        card.shineElement = shine;
        
        // Add border glow element
        const glow = document.createElement('div');
        glow.className = 'card-glow';
        glow.style.cssText = `
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(
                45deg,
                transparent 40%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 60%
            );
            border-radius: inherit;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        `;
        card.appendChild(glow);
        card.glowElement = glow;
    }
    
    addEventListeners() {
        if (this.isTouchDevice) {
            // Touch device - use simpler interactions
            this.cards.forEach(card => {
                card.addEventListener('touchstart', () => {
                    card.style.transform = 'scale(0.98) translateZ(10px)';
                });
                
                card.addEventListener('touchend', () => {
                    card.style.transform = 'scale(1) translateZ(0)';
                });
            });
            return;
        }
        
        // Desktop - full 3D tilt effect
        this.cards.forEach(card => {
            card.addEventListener('mouseenter', (e) => {
                this.onCardEnter(card, e);
            });
            
            card.addEventListener('mousemove', (e) => {
                this.onCardMove(card, e);
            });
            
            card.addEventListener('mouseleave', (e) => {
                this.onCardLeave(card);
            });
            
            // Click handler for opening README modal
            card.addEventListener('click', (e) => {
                // Don't open modal if clicking on links
                if (e.target.closest('a')) return;
                
                const fullName = card.dataset.fullname;
                const repoUrl = card.dataset.repoUrl;
                const userForkUrl = card.dataset.userForkUrl;
                const languages = card.dataset.languages;
                
                if (fullName && repoUrl) {
                    const [owner, repo] = fullName.split('/');
                    if (owner && repo && window.PortfolioApp) {
                        window.PortfolioApp.openReadmeModal(owner, repo, repoUrl, userForkUrl, languages);
                    }
                }
            });
        });
    }
    
    onCardEnter(card, e) {
        // Show glow
        if (card.glowElement) {
            card.glowElement.style.opacity = '1';
        }
    }
    
    onCardMove(card, e) {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        // Calculate rotation based on mouse position
        const rotateX = ((y - centerY) / centerY) * -10;
        const rotateY = ((x - centerX) / centerX) * 10;
        
        // Apply 3D transform
        card.style.transform = `
            perspective(1000px)
            rotateX(${rotateX}deg)
            rotateY(${rotateY}deg)
            translateZ(20px)
            scale(1.02)
        `;
        
        // Update shine position
        if (card.shineElement) {
            const shineOpacity = 0.3 + (Math.abs(rotateX) + Math.abs(rotateY)) / 100;
            card.shineElement.style.opacity = shineOpacity;
            card.shineElement.style.background = `
                linear-gradient(
                    ${135 + rotateY}deg,
                    rgba(255, 255, 255, 0.15) 0%,
                    rgba(255, 255, 255, 0.05) 50%,
                    rgba(255, 255, 255, 0) 100%
                )
            `;
        }
    }
    
    onCardLeave(card) {
        // Reset transform
        card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0) scale(1)';
        
        // Hide effects
        if (card.shineElement) {
            card.shineElement.style.opacity = '0';
        }
        if (card.glowElement) {
            card.glowElement.style.opacity = '0';
        }
    }
    
    addScrollAnimations() {
        // Use IntersectionObserver for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const index = this.cards.indexOf(card);
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0) rotateX(0)';
                    }, index * 100);
                    
                    observer.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        this.cards.forEach(card => observer.observe(card));
    }
    
    // Method to update card content dynamically
    updateCards(projects) {
        if (!this.container) return;
        
        // Clear existing cards
        this.container.innerHTML = '';
        
        // Create new cards for each project
        projects.forEach((project, index) => {
            const card = this.createProjectCard(project, index);
            this.container.appendChild(card);
        });
        
        // Re-initialize
        this.init();
    }
    
    createProjectCard(project, index) {
        const card = document.createElement('div');
        card.className = 'project-card-3d';
        card.innerHTML = `
            <div class="project-card-header">
                <h3 class="project-card-title">${this.escapeHtml(project.name)}</h3>
                <div class="project-card-stars">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    ${project.stargazers_count}
                </div>
            </div>
            <p class="project-card-description">${this.escapeHtml(project.description || 'No description available')}</p>
            <div class="project-card-footer">
                <span class="project-card-language">${this.escapeHtml(project.language || 'Unknown')}</span>
                <div class="project-card-links">
                    <a href="${project.html_url}" target="_blank" rel="noopener noreferrer" class="project-card-link">GitHub</a>
                    ${project.homepage ? `<a href="${project.homepage}" target="_blank" rel="noopener noreferrer" class="project-card-link demo">Live</a>` : ''}
                </div>
            </div>
        `;
        
        // Prevent card click when clicking links
        card.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
        
        return card;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.projectCards3D = new ProjectCards3D();
});
