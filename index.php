<?php
/**
 * Main Portfolio Page
 * Features: Three.js 3D Background, GitHub API Integration, Modular Architecture
 */

// Load environment variables
require_once __DIR__ . '/config/env_loader.php';

// Configuration and Services
require_once __DIR__ . '/github_api.php';

// Page metadata
$pageTitle = 'Samuel Mencke - Portfolio';

// Calculate age automatically (born 20.10.2010)
$birthDate = new DateTime('2010-10-20');
$today = new DateTime();
$age = $birthDate->diff($today)->y;
$pageDescription = "Portfolio of Samuel Mencke, a {$age}-year-old developer from Germany passionate about web development and 3D experiences.";

// Get GitHub username from environment
$githubUsername = getenv('GITHUB_USERNAME') ?: 'Samuel-Mencke';

// Fetch GitHub repositories
$githubService = new GitHubAPIService();
$githubProjects = $githubService->fetchRepositories();

// Projects are now separated in owned and contributed arrays
$ownedProjects = $githubProjects['owned'] ?? [];
$contributedProjects = $githubProjects['contributed'] ?? [];

// Collect all unique technologies/languages from all projects
$allTechnologies = [];
foreach (array_merge($ownedProjects, $contributedProjects) as $project) {
    // Add all languages, not just primary
    if (!empty($project['languages'])) {
        foreach ($project['languages'] as $lang) {
            $allTechnologies[$lang] = true;
        }
    } elseif (!empty($project['language']) && $project['language'] !== 'Unknown') {
        $allTechnologies[$project['language']] = true;
    }
}
$allTechnologies = array_keys($allTechnologies);
sort($allTechnologies);

// Include header partial
include __DIR__ . '/partials/header.php';
?>

<?php include __DIR__ . '/partials/navbar.php'; ?>

<!-- Header / Hero Section -->
<header class="header section">
    <h1 class="name">Samuel Mencke</h1>
    <p class="title"><?= $age ?>-year-old developer from Germany</p>
    <div class="contact">
        <a href="mailto:contact@samuel-mencke.com">mail</a>
        <a href="https://github.com/samuel-mencke" target="_blank" rel="noopener noreferrer">github</a>
    </div>
</header>

<main>
    <!-- About Section -->
    <section id="about" class="section">
        <h2 class="section-title">about me</h2>
        <p class="about">
            Hi, I'm Samuel Mencke. I'm 15 years old and from Germany.
            I'm mainly a web developer, but I also enjoy working on small game projects in my free time. 
            I like building websites, web apps, and simple automation tools. I'm always trying to improve 
            and learn new things, especially when it comes to programming.
            <br><br>
            I often work on personal projects and sometimes contribute to open source. 
            If you're interested in similar things or want to exchange ideas, feel free to message me.
        </p>
    </section>

    <!-- My Projects Section -->
    <section id="projects" class="section">
        <h2 class="section-title">my projects</h2>
        
        <div class="projects-3d-container">
            <?php 
            $ownedProjects = array_slice($githubProjects['owned'], 0, 6);
            foreach ($ownedProjects as $project): 
            ?>
                <div class="project-card-3d" 
                     data-fullname="<?= htmlspecialchars($project['full_name']) ?>" 
                     data-repo-url="<?= htmlspecialchars($project['html_url']) ?>"
                     data-languages="<?= htmlspecialchars(json_encode($project['languages'] ?? [$project['language'] ?? 'Unknown'])) ?>">
                    <div class="project-card-header">
                        <h3 class="project-card-title"><?= htmlspecialchars($project['name']) ?></h3>
                        <div class="project-card-badges">
                            <span class="project-badge owned">owner</span>
                            <div class="project-card-stars">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <?= $project['stargazers_count'] ?>
                            </div>
                        </div>
                    </div>
                    <p class="project-card-description"><?= htmlspecialchars($project['description']) ?></p>
                    <div class="project-card-footer">
                        <?php 
                        $languages = $project['languages'] ?? [];
                        $primaryLang = $languages[0] ?? $project['language'] ?? 'Unknown';
                        $langCount = count($languages);
                        ?>
                        <span class="project-card-language">
                            <?= htmlspecialchars($primaryLang) ?>
                            <?php if ($langCount > 1): ?>
                                <span class="lang-more">+<?= $langCount - 1 ?></span>
                            <?php endif; ?>
                        </span>
                        <div class="project-card-links">
                            <a href="<?= htmlspecialchars($project['html_url']) ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="project-card-link">
                                GitHub
                            </a>
                            <?php if (!empty($project['homepage'])): ?>
                                <a href="<?= htmlspecialchars($project['homepage']) ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="project-card-link demo">
                                    Live
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Contributed Projects Section -->
    <?php if (!empty($githubProjects['contributed'])): ?>
    <section id="contributed" class="section">
        <h2 class="section-title">contributed projects</h2>
        <p class="section-subtitle">Open source projects I've contributed to</p>
        
        <div class="projects-3d-container">
            <?php 
            $contributedProjects = array_slice($githubProjects['contributed'], 0, 6);
            foreach ($contributedProjects as $project): 
            ?>
                <div class="project-card-3d contributed-card" 
                     data-fullname="<?= htmlspecialchars($project['full_name']) ?>" 
                     data-repo-url="<?= htmlspecialchars($project['html_url']) ?>"
                     data-user-fork-url="<?= htmlspecialchars($project['user_fork_url'] ?? '') ?>"
                     data-languages="<?= htmlspecialchars(json_encode($project['languages'] ?? [$project['language'] ?? 'Unknown'])) ?>">
                    <div class="project-card-header">
                        <h3 class="project-card-title"><?= htmlspecialchars($project['name']) ?></h3>
                        <div class="project-card-badges">
                            <?php if ($project['is_fork_contribution'] ?? false): ?>
                                <span class="project-badge fork">fork</span>
                            <?php else: ?>
                                <span class="project-badge contributed">contributor</span>
                            <?php endif; ?>
                            <div class="project-card-stars">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <?= $project['stargazers_count'] ?>
                            </div>
                        </div>
                    </div>
                    <p class="project-card-fullname"><?= htmlspecialchars($project['full_name']) ?></p>
                    <p class="project-card-description"><?= htmlspecialchars($project['description']) ?></p>
                    <div class="project-card-footer">
                        <?php 
                        $languages = $project['languages'] ?? [];
                        $primaryLang = $languages[0] ?? $project['language'] ?? 'Unknown';
                        $langCount = count($languages);
                        ?>
                        <span class="project-card-language">
                            <?= htmlspecialchars($primaryLang) ?>
                            <?php if ($langCount > 1): ?>
                                <span class="lang-more">+<?= $langCount - 1 ?></span>
                            <?php endif; ?>
                        </span>
                        <div class="project-card-links">
                            <a href="<?= htmlspecialchars($project['html_url']) ?>" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="project-card-link">
                                GitHub
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Skills Section -->
    <section id="skills" class="section">
        <h2 class="section-title">skills</h2>
        <div class="skills-grid">
            <div class="skill-category">
                <h3>Programming Languages</h3>
                <div class="skill-item">
                    <span class="skill-name">HTML</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="95"></div>
                    </div>
                </div>
                <div class="skill-item">
                    <span class="skill-name">CSS</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="85"></div>
                    </div>
                </div>
                <div class="skill-item">
                    <span class="skill-name">JavaScript</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="75"></div>
                    </div>
                </div>
                <div class="skill-item">
                    <span class="skill-name">PHP</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="60"></div>
                    </div>
                </div>
                <div class="skill-item">
                    <span class="skill-name">Python</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="45"></div>
                    </div>
                </div>
            </div>
            <div class="skill-category">
                <h3>Frameworks & Tools</h3>
                <div class="skill-item">
                    <span class="skill-name">Three.js / WebGL</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="70"></div>
                    </div>
                </div>
                <div class="skill-item">
                    <span class="skill-name">Git</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="65"></div>
                    </div>
                </div>
                <div class="skill-item">
                    <span class="skill-name">VS Code</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="90"></div>
                    </div>
                </div>
                <div class="skill-item">
                    <span class="skill-name">GSAP / Animations</span>
                    <div class="skill-bar">
                        <div class="skill-progress" data-progress="75"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <h2 class="section-title">contact me</h2>
        <div class="contact-section">
            <div class="contact-info">
                <p>Have a project in mind or just want to chat about tech? I'd love to hear from you!</p>
                <div class="contact-links">
                    <a href="mailto:contact@samuel-mencke.com" class="contact-link">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <span>contact@samuel-mencke.com</span>
                    </a>
                    <a href="https://github.com/samuel-mencke" class="contact-link" target="_blank" rel="noopener noreferrer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                        <span>github.com/samuel-mencke</span>
                    </a>
                </div>
            </div>
            
            <form class="contact-form" id="contactForm">
                <div class="form-group">
                    <input type="text" id="name" name="name" placeholder="Your name" required autocomplete="name">
                </div>
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Your email" required autocomplete="email">
                </div>
                <div class="form-group">
                    <textarea id="message" name="message" placeholder="Your message" rows="5" required></textarea>
                </div>
                <button type="submit" class="submit-button">
                    Send message
                </button>
            </form>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="footer section">
    <p>copyright <?= date('Y') ?>. made by samuel mencke.</p>
</footer>

<?php include __DIR__ . '/partials/footer.php'; ?>
