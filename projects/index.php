<?php
// index.php im Ordner: ...\Portfolio\projects\

require_once __DIR__ . '/../projects_repository.php';

// Repository initialisieren
try {
    $repo = new ProjectsRepository();
} catch (RuntimeException $e) {
    die('DB connection error: ' . htmlspecialchars($e->getMessage()));
}

// Projekte laden
$project = null;
if (isset($_GET['project'])) {
    $slug = $_GET['project'];
    $project = $repo->getProjectBySlug($slug);
}
$projects = $repo->getAllProjects();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio Projects</title>
    <meta name="description" content="Portfolio projects by Samuel Mencke" />

    <!-- Korrekte Pfade zu CSS -->
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
    <!-- Custom Cursor -->
    <div class="cursor"></div>
    <div class="cursor-dot"></div>
    <div class="reading-progress"></div>
    <div class="site-container">
    <nav class="navigation">
        <div class="nav-container">
            <?php if ($project): ?>
                    <a href="/projects/" class="back-link">&#8592; Back to all projects</a>
                <a href="#contact" class="nav-link">contact me</a>
            <?php else: ?>
                <a href="/" class="back-link">&#8592; Back to main page</a>
                    <a href="#contact" class="nav-link">contact me</a>
            <?php endif; ?>
        </div>
    </nav>
    <button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle dark mode">
        <span class="toggle-text">dark</span>
    </button>
    <main>
        <?php if ($project): ?>
            <section id="project-detail" class="section fade-in">
                    <div class="project-detail-card" style="max-width: 700px; margin: 0 auto; background: rgba(128,128,128,0.1); border: 1px solid rgba(128,128,128,0.2); border-radius: 12px; padding: 25px; display: flex; flex-direction: column; align-items: stretch;">
                        <h2 class="section-title project-title" style="margin-bottom: 10px; border-bottom: none; font-size: 2.1rem; text-align: left; padding-left: 0;">
                            <?= htmlspecialchars($project['title']) ?>
                        </h2>
                        <?php if (!empty($project['technology'])): ?>
                            <div class="project-tech" style="gap: 10px; margin-bottom: 0; margin-top: 0; position: static; justify-content: flex-start; display: flex; flex-wrap: wrap;">
                                <?php foreach (explode(',', $project['technology']) as $tech): ?>
                                    <span class="project-tech-badge" style="font-size: 1.05rem; color: var(--secondary-text); padding: 0 8px; border-radius: 8px; font-weight: 500; margin-bottom: 4px;"> <?= htmlspecialchars(trim(strtolower($tech))) ?> </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                <?php
                $imagePath = '';
                if (!empty($project['image'])) {
                    $img = trim($project['image']);
                    $img = strpos($img, '/') === false ? 'images/' . ltrim($img, '/') : $img;
                    $imagePath = '/' . ltrim($img, '/');
                }
                ?>
                <?php if ($imagePath): ?>
                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($project['title']) ?>" class="project-detail-image" loading="lazy" style="display: block; width: 100%; max-width: 100%; margin: 24px 0 32px 0; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.10);">
                <?php endif; ?>
                        <div class="project-detail" style="max-width: 600px; margin: 0 auto 24px auto; text-align: left;">
                            <p style="font-size: 1.13rem; color: var(--secondary-text); line-height: 1.7; margin-bottom: 0;"> <?= nl2br(htmlspecialchars($project['description'])) ?> </p>
                        </div>
                        <div class="project-links" style="display: flex; gap: 20px; justify-content: flex-start; margin-top: 18px;">
                    <?php if (!empty($project['github'])): ?>
                                <a class="project-link" href="<?= htmlspecialchars($project['github']) ?>" target="_blank" style="font-size: 1.08rem; padding: 12px 32px; font-weight: 600;">
                                    <span class="contact-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"/></svg>
                                    </span>
                                    GitHub
                                </a>
                    <?php endif; ?>
                    <?php if (!empty($project['website'])): ?>
                                <a class="project-link demo-link" href="<?= htmlspecialchars($project['website']) ?>" target="_blank" style="font-size: 1.08rem; padding: 12px 32px; font-weight: 600;">
                                    <span class="contact-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z"/></svg></span>
                                    Website
                                </a>
                    <?php endif; ?>
                        </div>
                </div>
            </section>
        <?php else: ?>
            <section id="projects" class="section fade-in">
                <h2 class="section-title">projects</h2>
                <div class="projects-grid">
                    <?php foreach ($projects as $p): ?>
                        <div class="project">
                            <div class="project-header">
                                <div class="project-name">
                                    <a href="/projects/<?= htmlspecialchars($p['slug']) ?>">
                                        <?= htmlspecialchars(strtolower($p['title'])) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="project-description">
                                <?= nl2br(htmlspecialchars(strtolower($p['description']))) ?>
                            </div>
                            <div class="project-links">
                                <?php if (!empty($p['github'])): ?>
                                    <a class="project-link" href="<?= htmlspecialchars(strtolower($p['github'])) ?>" target="_blank" rel="noopener noreferrer">
                                        <span class="contact-icon"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"/></svg></span>
                                        GitHub
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($p['website'])): ?>
                                    <a class="project-link demo-link" href="<?= htmlspecialchars(strtolower($p['website'])) ?>" target="_blank" rel="noopener noreferrer">
                                        <span class="contact-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16"><path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z"/></svg></span>
                                        Website
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        <section id="contact" class="section fade-in">
            <h2 class="section-title">contact me</h2>
            <div class="contact-section">
                <div class="contact-info">
                        <p>have a project in mind or just want to chat about tech? I'd love to hear from you!</p>
                    <div class="contact-links">
                        <a href="mailto:contact@samuel-mencke.com" class="contact-link">
                            <span class="contact-icon">✉</span>
                            <span>contact@samuel-mencke.com</span>
                        </a>
                        <a href="https://github.com/samuel-mencke" class="contact-link" target="_blank">
                            <span class="contact-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"/></svg></span>
                            <span>github.com/samuel-mencke</span>
                        </a>
                    </div>
                </div>
                <form class="contact-form" id="contactForm">
                    <div class="form-group">
                        <input type="text" id="name" name="name" placeholder="your name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" placeholder="your email" required>
                    </div>
                    <div class="form-group">
                        <textarea id="message" name="message" placeholder="your message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="submit-button">
                        <span class="button-text">send message</span>
                    </button>
                </form>
            </div>
        </section>
    </main>
    <footer class="footer fade-in">
        <p>copyright 2025. made by samuel mencke.</p>
    </footer>
    </div>

    <!-- Korrekte Pfade zu JS -->
    <script src="../assets/js/script.js"></script>

<style>
/* Entfernt: .project-title { margin-left: 400px !important; width: max-content !important; } */
</style>

</body>
</html>
