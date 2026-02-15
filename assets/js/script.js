let cursor, cursorDot;

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.skill-progress').forEach(bar => {
        const target = bar.getAttribute('data-progress') || '0';
        bar.style.width = target + '%';
    });
    
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;

            fetch('https://portfolio.samuel-mencke.com/send_mail.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    message: message
                })
            })
                .then(res => res.json())
                .then(data => console.log('Antwort:', data));

            const submitButton = contactForm.querySelector('.submit-button');
            const originalText = submitButton.querySelector('.button-text').textContent;

            submitButton.querySelector('.button-text').textContent = 'sending...';
            submitButton.disabled = true;
            submitButton.style.opacity = '0.7';

            setTimeout(() => {
                submitButton.querySelector('.button-text').textContent = 'message sent!';
                submitButton.style.background = '#4ade80';

                contactForm.reset();

                setTimeout(() => {
                    submitButton.querySelector('.button-text').textContent = originalText;
                    submitButton.disabled = false;
                    submitButton.style.opacity = '1';
                    submitButton.style.background = '';
                }, 2000);
            }, 1500);
        });
    }
// Dark Mode Toggle mit localStorage
    const darkModeToggle = document.getElementById('darkModeToggle');
    const toggleText = darkModeToggle.querySelector('.toggle-text');
    const body = document.body;

    function applyDarkMode(enabled) {
        if (enabled) {
            body.classList.add('dark-mode');
            if (toggleText) toggleText.textContent = 'light';
        } else {
            body.classList.remove('dark-mode');
            if (toggleText) toggleText.textContent = 'dark';
        }
    }

    // Lade Einstellung vom localStorage
    const darkModeSetting = localStorage.getItem('dark-mode') === 'true';
    applyDarkMode(darkModeSetting);

    darkModeToggle.addEventListener('click', () => {
        const isDark = body.classList.toggle('dark-mode');
        localStorage.setItem('dark-mode', isDark ? 'true' : 'false');
        if (toggleText) toggleText.textContent = isDark ? 'light' : 'dark';
    });

    cursor = document.querySelector('.cursor');
    cursorDot = document.querySelector('.cursor-dot');

    let mouseX = 0, mouseY = 0;
    let cursorX = 0, cursorY = 0;
    let dotX = 0, dotY = 0;
let cursorHalfWidth, cursorHalfHeight, dotHalfWidth, dotHalfHeight;

    function updateCursorSize() {
    cursorHalfWidth = cursor.offsetWidth / 2;
    cursorHalfHeight = cursor.offsetHeight / 2;
    dotHalfWidth = cursorDot.offsetWidth / 2;
    dotHalfHeight = cursorDot.offsetHeight / 2;
    }

    window.addEventListener('resize', updateCursorSize);
    updateCursorSize();

document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
});

function animate() {
    if (!cursor || !cursorDot) return;
    
    cursorX += (mouseX - cursorX) * 0.1;
    cursorY += (mouseY - cursorY) * 0.1;

    dotX += (mouseX - dotX) * 0.8;
    dotY += (mouseY - dotY) * 0.8;

        // Exakte Zentrierung mit den berechneten Hälften
        cursor.style.transform = `translate3d(${cursorX - cursorHalfWidth}px, ${cursorY - cursorHalfHeight}px, 0)`;
        cursorDot.style.transform = `translate3d(${dotX - dotHalfWidth}px, ${dotY - dotHalfHeight}px, 0)`;

    requestAnimationFrame(animate);
}
if (cursor && cursorDot) {
    animate();
}

document.querySelectorAll('a, .project, .misc-item').forEach(el => {
    el.addEventListener('mouseenter', () => {
        if (cursor) {
            cursor.style.transform = 'scale(1.5)';
            cursor.style.borderColor = '#999';
        }
    });

    el.addEventListener('mouseleave', () => {
        if (cursor) {
            cursor.style.transform = 'scale(1)';
            cursor.style.borderColor = '';
        }
    });
});

const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -200px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running';
        }
    });
}, observerOptions);

document.querySelectorAll('.fade-in').forEach(el => {
    el.style.animationPlayState = 'paused';
    observer.observe(el);
});

document.querySelectorAll('.project').forEach(project => {
    project.addEventListener('click', (e) => {
        if (e.target.tagName !== 'A') {
            const link = project.querySelector('.project-name a');
            if (link) {
                const ripple = document.createElement('div');
                const rect = project.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(26, 26, 26, 0.05);
                    border-radius: 50%;
                    transform: scale(0);
                    pointer-events: none;
                    z-index: 1;
                `;

                project.style.position = 'relative';
                project.appendChild(ripple);

                ripple.animate([
                    { transform: 'scale(0)', opacity: 1 },
                    { transform: 'scale(2)', opacity: 0 }
                ], {
                    duration: 600,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
                });

                setTimeout(() => {
                    ripple.remove();
                        const href = link.getAttribute('href');
                        // Prüfe, ob der Link intern ist
                        const isInternal = href.startsWith('/') || href.startsWith('projects') || href.startsWith('./') || href.startsWith('../');
                        if (isInternal) {
                            window.location.href = link.href;
                        } else {
                    window.open(link.href, '_blank');
                        }
                }, 300);
            }
        }
    });
});

setTimeout(() => {
    const typingIndicator = document.querySelector('.typing-indicator');
    if (typingIndicator) {
        typingIndicator.style.opacity = '0';
        setTimeout(() => typingIndicator.remove(), 500);
    }
}, 3000);

document.addEventListener('DOMContentLoaded', () => {
    const contactLinks = document.querySelectorAll('.contact a');
    contactLinks.forEach((link, index) => {
        link.style.opacity = '0';
        link.style.transform = 'translateY(20px)';

        setTimeout(() => {
            link.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            link.style.opacity = '1';
            link.style.transform = 'translateY(0)';
        }, 1000 + (index * 150));
    });
});

if (window.innerWidth <= 768) {
    if (cursor) cursor.style.display = 'none';
    if (cursorDot) cursorDot.style.display = 'none';
    document.body.style.cursor = 'auto';
    document.querySelectorAll('*').forEach(el => {
        el.style.cursor = 'auto';
    });
}

window.addEventListener('scroll', () => {
    const scrollTop = document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight;
    const clientHeight = document.documentElement.clientHeight;
    const progress = (scrollTop / (scrollHeight - clientHeight)) * 100;
    const readingProgress = document.querySelector('.reading-progress');
    if (readingProgress) readingProgress.style.width = `${progress}%`;
});

document.querySelectorAll('a[href^="http"]').forEach(link => {
    link.addEventListener('mouseenter', () => {
        if (window.innerWidth > 768) {
            const preview = document.createElement('div');
            preview.className = 'link-preview';
            preview.textContent = new URL(link.href).hostname.replace('www.', '');
            document.body.appendChild(preview);

            const updatePosition = (e) => {
                preview.style.left = `${e.clientX + 15}px`;
                preview.style.top = `${e.clientY + 15}px`;
            };

            link.addEventListener('mousemove', updatePosition);
            link.addEventListener('mouseleave', () => {
                preview.remove();
                link.removeEventListener('mousemove', updatePosition);
            });
        }
    });
});

const currentYearElement = document.getElementById('currentYear');
if (currentYearElement) {
    currentYearElement.textContent = new Date().getFullYear();
}

document.querySelectorAll('.demo-button').forEach(button => {
    button.addEventListener('click', (e) => {
        e.stopPropagation();
        const demoUrl = button.getAttribute('data-demo');
        
        if (demoUrl) {
            button.style.transform = 'translateY(0) scale(0.95)';
            setTimeout(() => {
                button.style.transform = '';
                window.open(demoUrl, '_blank');
            }, 150);
        }
    });

    button.addEventListener('mouseenter', () => {
        cursor.style.transform = 'scale(2)';
        cursor.style.borderColor = '#999';
        cursor.style.background = 'rgba(0, 0, 0, 0.1)';
    });

    button.addEventListener('mouseleave', () => {
        cursor.style.transform = 'scale(1)';
        cursor.style.borderColor = '';
        cursor.style.background = 'transparent';
    });
});

document.querySelectorAll('a, .project, .misc-item, .demo-button').forEach(el => {
    el.addEventListener('mouseenter', () => {
        if (!el.classList.contains('demo-button')) {
            cursor.style.transform = 'scale(1.5)';
            cursor.style.borderColor = '#999';
        }
    });

    el.addEventListener('mouseleave', () => {
        if (!el.classList.contains('demo-button')) {
            cursor.style.transform = 'scale(1)';
            cursor.style.borderColor = '';
        }
    });
});

    document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
            link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                const offset = 100;
                const targetPosition = targetSection.offsetTop - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                navLinks.forEach(nl => nl.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    function updateActiveNavLink() {
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
    }

    window.addEventListener('scroll', updateActiveNavLink);

    const enhancedElements = document.querySelectorAll('.nav-link, .contact-link, .submit-button, .skill-item');
    enhancedElements.forEach(el => {
        el.addEventListener('mouseenter', () => {
            if (cursor) {
                cursor.style.transform = 'scale(1.5)';
                cursor.style.borderColor = '#999';
            }
        });

        el.addEventListener('mouseleave', () => {
            if (cursor) {
                cursor.style.transform = 'scale(1)';
                cursor.style.borderColor = '';
            }
        });
    });

    function parallaxEffect() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.section');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            const yPos = -(scrolled * speed / 10);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }

    let ticking = false;
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(parallaxEffect);
            ticking = true;
        }
    }

    window.addEventListener('scroll', () => {
        requestTick();
        ticking = false;
    });

    const nameElement = document.querySelector('.name');
    if (nameElement) {
        const nameText = nameElement.textContent;
        const typingIndicator = nameElement.querySelector('.typing-indicator');
        
        if (typingIndicator) {
            setInterval(() => {
                typingIndicator.style.opacity = typingIndicator.style.opacity === '0' ? '1' : '0';
            }, 1000);
        }
    }

    const projects = document.querySelectorAll('.project');
    projects.forEach(project => {
            project.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
            project.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    const fadeInElements = Array.from(document.querySelectorAll('.fade-in'));
    const fadeInObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const index = fadeInElements.indexOf(entry.target);
                entry.target.style.animationDelay = (index * 0.25) + 's';
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    fadeInElements.forEach(el => {
        el.style.animationPlayState = 'paused';
        fadeInObserver.observe(el);
    });
});

if (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2) {
    document.documentElement.style.setProperty('--animation-duration', '0.6s');
}

let konamiCode = [];
const konamiSequence = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];

    document.addEventListener('keydown', function (e) {
    konamiCode.push(e.keyCode);
    
    if (konamiCode.length > konamiSequence.length) {
        konamiCode.shift();
    }
    
    if (konamiCode.toString() === konamiSequence.toString()) {
        document.body.style.background = 'linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57, #ff9ff3)';
        document.body.style.backgroundSize = '400% 400%';
        document.body.style.animation = 'rainbow 3s ease infinite';
        
        if (!document.querySelector('#rainbow-style')) {
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
        
        konamiCode = [];
    }
});

// GSAP Animationen für verschiedene Fade-In-Varianten
// Stelle sicher, dass GSAP eingebunden ist
// <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>

function animateOnScrollGSAP() {
    // Hauptbereiche
    const mainSections = [
        document.querySelector('.fade-in-left'),
        document.querySelector('.fade-in'),
        document.querySelector('.fade-in-right'),
        document.querySelector('.fade-in-scale')
    ].filter(Boolean);

    mainSections.forEach(section => {
        if (!section) return;
        section.style.opacity = 0;
        section.removeAttribute('data-animated');
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            let animIn = { opacity: 1, duration: 2.0, ease: 'power3.out' };
            let animOut = { opacity: 0, duration: 1.5, ease: 'power3.in' };
            const alreadyIn = entry.target.getAttribute('data-animated') === 'in';
            const alreadyOut = entry.target.getAttribute('data-animated') === 'out';
            if (entry.isIntersecting && !alreadyIn) {
                entry.target.setAttribute('data-animated', 'in');
                if (entry.target.id === 'skills' || entry.target.id === 'contact') {
                    gsap.fromTo(entry.target, { opacity: 0 }, { opacity: 1, duration: 2.0, ease: 'power3.out' });
                } else if (entry.target.classList.contains('fade-in-left')) {
                    animIn.x = 0;
                    gsap.fromTo(entry.target, { x: -60, opacity: 0 }, animIn);
                } else if (entry.target.classList.contains('fade-in-right')) {
                    animIn.x = 0;
                    gsap.fromTo(entry.target, { x: 60, opacity: 0 }, animIn);
                } else if (entry.target.classList.contains('fade-in-scale')) {
                    animIn.scale = 1;
                    const contactSection = entry.target.querySelector('.contact-section');
                    if (contactSection) {
                        gsap.fromTo(contactSection, { scale: 0.92, opacity: 0 }, { scale: 1, opacity: 1, duration: 2.0, ease: 'power3.out' });
                    } else {
                        gsap.fromTo(entry.target, { scale: 0.92, opacity: 0 }, animIn);
                    }
                } else {
                    animIn.y = 0;
                    gsap.fromTo(entry.target, { y: 40, opacity: 0 }, animIn);
                }
                // Stagger für Unterelemente
                if (entry.target.id === 'projects') {
                    gsap.fromTo(
                        entry.target.querySelectorAll('.project'),
                        { y: 40, opacity: 0 },
                        { y: 0, opacity: 1, duration: 2.0, ease: 'power3.out', stagger: 0.35, delay: 0.4 }
                    );
                }
                if (entry.target.id === 'skills') {
                    gsap.fromTo(
                        entry.target.querySelectorAll('.skill-category'),
                        { opacity: 0 },
                        { opacity: 1, duration: 2.0, ease: 'power3.out', stagger: 0.36, delay: 0.4 }
                    );
                }
            } else if (!entry.isIntersecting && !alreadyOut) {
                entry.target.setAttribute('data-animated', 'out');
                if (entry.target.id === 'skills' || entry.target.id === 'contact') {
                    gsap.to(entry.target, { opacity: 0, duration: 1.5, ease: 'power3.in' });
                } else if (entry.target.classList.contains('fade-in-left')) {
                    gsap.to(entry.target, { x: -60, opacity: 0, ...animOut });
                } else if (entry.target.classList.contains('fade-in-right')) {
                    gsap.to(entry.target, { x: 60, opacity: 0, ...animOut });
                } else if (entry.target.classList.contains('fade-in-scale')) {
                    const contactSection = entry.target.querySelector('.contact-section');
                    if (contactSection) {
                        gsap.to(contactSection, { scale: 0.92, opacity: 0, duration: 1.5, ease: 'power3.in' });
                    } else {
                        gsap.to(entry.target, { scale: 0.92, opacity: 0, ...animOut });
                    }
                } else {
                    gsap.to(entry.target, { y: 40, opacity: 0, duration: 1.5, ease: 'power3.in', stagger: 0.2 });
                }
                if (entry.target.id === 'projects') {
                    gsap.to(
                        entry.target.querySelectorAll('.project'),
                        { y: 40, opacity: 0, duration: 1.5, ease: 'power3.in', stagger: 0.2 }
                    );
                }
                if (entry.target.id === 'skills') {
                    gsap.to(
                        entry.target.querySelectorAll('.skill-category'),
                        { opacity: 0, duration: 1.5, ease: 'power3.in', stagger: 0.2 }
                    );
                }
            }
        });
    }, { threshold: 0.1 });
    mainSections.forEach(el => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.gsap) {
        animateOnScrollGSAP();
    }
});

// --- GSAP-Intro-Animation ---
document.addEventListener('DOMContentLoaded', () => {
    // Alle animierbaren Elemente in Reihenfolge sammeln
    const animated = [
        ...document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .fade-in-scale, .section')
    ];
    animated.forEach(el => {
        if (el.id === 'contact' || el.id === 'skills') {
            el.style.opacity = 0;
            el.style.transform = 'none';
        } else {
            el.style.opacity = 0;
            el.style.transform = 'translateY(16px)';
        }
    });
    if (window.gsap) {
        animated.forEach((el, i) => {
            if (el.id === 'contact' || el.id === 'skills') {
                gsap.to(el, {
                    opacity: 1,
                    duration: 1.2,
                    delay: i * 0.18,
                    ease: 'power1.out',
                });
            } else {
                gsap.to(el, {
                    opacity: 1,
                    y: 0,
                    duration: 1.2,
                    delay: i * 0.18,
                    ease: 'power1.out',
                });
            }
        });
    } else {
        // Fallback: sofort sichtbar
        animated.forEach(el => {
            el.style.opacity = 1;
            el.style.transform = 'none';
        });
    }
});

const mobileDarkModeToggle = document.getElementById('mobileDarkModeToggle');
const mobileDarkModeIcon = document.getElementById('mobileDarkModeIcon');
if (mobileDarkModeToggle && mobileDarkModeIcon) {
    function updateMobileIcon(isDark) {
        mobileDarkModeIcon.textContent = isDark ? '☀️' : '🌙';
    }
    // Initial Icon setzen
    updateMobileIcon(body.classList.contains('dark-mode'));
    mobileDarkModeToggle.addEventListener('click', () => {
        const isDark = body.classList.toggle('dark-mode');
        localStorage.setItem('dark-mode', isDark ? 'true' : 'false');
        if (toggleText) toggleText.textContent = isDark ? 'light' : 'dark';
        updateMobileIcon(isDark);
    });
}
});