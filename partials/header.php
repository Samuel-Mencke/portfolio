<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Samuel Mencke - Portfolio' ?></title>
    
    <!-- Meta Tags -->
    <meta name="description" content="<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : 'Portfolio of Samuel Mencke, a developer from Germany' ?>">
    <meta name="keywords" content="Samuel Mencke, developer, portfolio, PHP, JavaScript, Three.js, web development">
    <meta name="author" content="Samuel Mencke">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://samuel-mencke.com">
    <meta property="og:title" content="<?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Samuel Mencke - Portfolio' ?>">
    <meta property="og:description" content="<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : 'Portfolio of Samuel Mencke, a developer from Germany' ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="<?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Samuel Mencke - Portfolio' ?>">
    <meta property="twitter:description" content="<?= isset($pageDescription) ? htmlspecialchars($pageDescription) : 'Portfolio of Samuel Mencke, a developer from Germany' ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    
    <!-- GSAP -->
    <script src="assets/js/gsap.min.js"></script>
</head>
<body>
    <!-- Custom Cursor -->
    <div class="cursor"></div>
    <div class="cursor-dot"></div>
    
    <!-- Reading Progress Bar -->
    <div class="reading-progress"></div>
    
    <!-- 3D Background Canvas -->
    <canvas id="three-canvas" class="three-background"></canvas>
    
    <!-- Site Container -->
    <div class="site-container">
