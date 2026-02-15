    </div><!-- End site-container -->
    
    <!-- Mobile Dark Mode Toggle -->
    <button id="mobileDarkModeToggle" class="mobile-darkmode-toggle" aria-label="Toggle dark mode">
        <span id="mobileDarkModeIcon">🌙</span>
    </button>
    
    <!-- Project Detail Modal -->
    <div id="readmeModal" class="project-modal">
        <div class="project-modal-backdrop"></div>
        <div class="project-modal-content">
            <!-- Close Button -->
            <button class="project-modal-close" aria-label="Close modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            
            <!-- Scrollable Content Container -->
            <div class="project-modal-scroll">
                <!-- Sticky Image Section -->
                <div id="projectImageCarousel" class="project-image-section">
                    <div class="project-image-container">
                        <div class="carousel-track"></div>
                    </div>
                    <button class="carousel-nav carousel-prev" aria-label="Previous image">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button class="carousel-nav carousel-next" aria-label="Next image">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                    <div class="carousel-dots"></div>
                </div>
                
                <!-- Content Section -->
                <div class="project-content-wrapper">
                    <div class="project-content-inner">
                        <!-- Project Header -->
                        <div class="project-info-header">
                            <div class="project-info-title">
                                <h2 id="projectModalTitle">Project Name</h2>
                                <div class="project-info-meta">
                                    <span id="projectModalLanguage" class="project-meta-item">
                                        <span class="meta-dot"></span>
                                        <span class="meta-text">Language</span>
                                    </span>
                                    <span id="projectModalStars" class="project-meta-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <span class="meta-text">0</span>
                                    </span>
                                    <span id="projectModalTopics" class="project-topics"></span>
                                </div>
                            </div>
                            <div class="project-header-actions">
                                <a id="projectModalForkLink" href="#" target="_blank" rel="noopener noreferrer" class="project-fork-btn hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="6" y1="3" x2="6" y2="15"></line>
                                        <circle cx="18" cy="6" r="3"></circle>
                                        <circle cx="6" cy="18" r="3"></circle>
                                        <path d="M18 9a9 9 0 0 1-9 9"></path>
                                    </svg>
                                    My Fork
                                </a>
                                <a id="projectModalLink" href="#" target="_blank" rel="noopener noreferrer" class="project-github-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                    View on GitHub
                                </a>
                            </div>
                        </div>
                        
                        <!-- README Content -->
                        <div id="projectReadmeContent" class="project-readme-content">
                            <div class="readme-loading">
                                <div class="loading-spinner"></div>
                                <p>Loading project details...</p>
                            </div>
                        </div>
                        
                        <!-- Bottom Spacer for better scrolling -->
                        <div class="project-content-spacer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="assets/js/three-scene.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/three-projects.js"></script>
</body>
</html>
