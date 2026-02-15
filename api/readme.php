<?php
/**
 * API Endpoint to fetch repository details, README, and images from GitHub
 * Filters out badges, logos, and non-screenshot images
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/env_loader.php';

$owner = $_GET['owner'] ?? '';
$repo = $_GET['repo'] ?? '';

if (empty($owner) || empty($repo)) {
    http_response_code(400);
    echo json_encode(['error' => 'Owner and repo required']);
    exit;
}

$token = getenv('GITHUB_TOKEN') ?: null;
$apiBase = 'https://api.github.com';

// URL patterns to exclude (badges, logos, icons)
$excludePatterns = [
    '/shields\.io/i',
    '/badge/i',
    '/npmjs\.com\/.*\/v\//i',
    '/img\.shields\.io/i',
    '/travis-ci\.org/i',
    '/travis-ci\.com/i',
    '/codecov\.io/i',
    '/coveralls\.io/i',
    '/github\.com\/.*\/workflows\//i',
    '/github\.com\/.*\/actions\//i',
    '/badgen\.net/i',
    '/forthebadge\.com/i',
    '/david-dm\.org/i',
    '/snyk\.io/i',
    '/dependabot\.com/i',
    '/renovatebot\.com/i',
    '/greenkeeper\.io/i',
    '/nodei\.co/i',
    '/opencollective\.com/i',
    '/version-badge/i',
    '/logo/i',
    '/icon/i',
    '/favicon/i',
    '/github.*logo/i',
    '/npm.*logo/i',
    '/npm.*icon/i',
    '/made-with/i',
    '/built-with/i',
    '/powered-by/i',
    '/license.*badge/i',
    '/license-button/i',
];

// Filename patterns to exclude
$excludeFilenamePatterns = [
    '/^logo/i',
    '/^icon/i',
    '/^favicon/i',
    '/^badge/i',
    '/button/i',
    '/^npm/i',
    '/^github/i',
    '/banner/i',
];

function makeGitHubRequest($url, $token) {
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Portfolio-Site/1.0',
                'Accept: application/vnd.github.v3+json'
            ],
            'timeout' => 15,
            'follow_location' => true
        ],
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true
        ]
    ];
    
    if ($token) {
        $options['http']['header'][] = 'Authorization: Bearer ' . $token;
    }
    
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        return null;
    }
    
    return json_decode($response, true);
}

// Check if URL should be excluded
function shouldExcludeUrl($url, $excludePatterns, $excludeFilenamePatterns) {
    // Check URL patterns
    foreach ($excludePatterns as $pattern) {
        if (preg_match($pattern, $url)) {
            return true;
        }
    }
    
    // Check filename patterns
    $filename = basename(parse_url($url, PHP_URL_PATH) ?? '');
    foreach ($excludeFilenamePatterns as $pattern) {
        if (preg_match($pattern, $filename)) {
            return true;
        }
    }
    
    return false;
}

// Check if filename looks like a screenshot or demo image
function isLikelyScreenshot($filename) {
    $screenshotPatterns = [
        '/screenshot/i',
        '/demo/i',
        '/preview/i',
        '/showcase/i',
        '/example/i',
        '/mockup/i',
        '/ui/i',
        '/interface/i',
        '/app/i',
        '/capture/i',
        '/img_?\d+/i',
        '/image_?\d+/i',
        '/pic_?\d+/i',
        '/photo_?\d+/i',
    ];
    
    foreach ($screenshotPatterns as $pattern) {
        if (preg_match($pattern, $filename)) {
            return true;
        }
    }
    
    return false;
}

// Fetch repository details
$repoDetails = makeGitHubRequest("{$apiBase}/repos/{$owner}/{$repo}", $token);

// Check if repo is private
$isPrivate = $repoDetails['private'] ?? false;

// Fetch all languages for this repository
$languages = [];
if (isset($repoDetails['languages_url'])) {
    $languagesData = makeGitHubRequest($repoDetails['languages_url'], $token);
    if ($languagesData && is_array($languagesData)) {
        // Sort by bytes (most used first)
        arsort($languagesData);
        $languages = array_keys($languagesData);
    }
}

// Fetch README content
$readmeData = makeGitHubRequest("{$apiBase}/repos/{$owner}/{$repo}/readme", $token);

// Collect all images (ONLY from README - no code files)
$images = [];
$seenUrls = [];

// Helper to add unique images with filtering
function addImage(&$images, &$seenUrls, $url, $excludePatterns, $excludeFilenamePatterns, $alt = '') {
    if (empty($url) || in_array($url, $seenUrls)) return;
    
    // Skip excluded URLs
    if (shouldExcludeUrl($url, $excludePatterns, $excludeFilenamePatterns)) {
        return;
    }
    
    $seenUrls[] = $url;
    $images[] = ['url' => $url, 'alt' => $alt];
}

// Check if this is the portfolio repo (use local images only)
$isPortfolioRepo = ($repo === 'portfolio');

// Extract images ONLY from README (no code files, no repo browsing)
if ($readmeData && isset($readmeData['content'])) {
    $readmeContent = base64_decode($readmeData['content']);
    
    // Skip README image extraction for portfolio repo (we'll use local images instead)
    if (!$isPortfolioRepo) {
        // Match markdown images ![alt](url)
        preg_match_all('/!\[([^\]]*)\]\(([^)]+)\)/', $readmeContent, $mdImages, PREG_SET_ORDER);
        foreach ($mdImages as $match) {
            $url = $match[2];
            // Convert relative GitHub URLs to absolute
            if (strpos($url, 'http') !== 0) {
                $url = "https://raw.githubusercontent.com/{$owner}/{$repo}/main/" . ltrim($url, './');
            }
            addImage($images, $seenUrls, $url, $excludePatterns, $excludeFilenamePatterns, $match[1]);
        }
        
        // Match HTML images <img src="url" />
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $readmeContent, $htmlImages, PREG_SET_ORDER);
        foreach ($htmlImages as $match) {
            $url = $match[1];
            if (strpos($url, 'http') !== 0) {
                $url = "https://raw.githubusercontent.com/{$owner}/{$repo}/main/" . ltrim($url, '/');
            }
            addImage($images, $seenUrls, $url, $excludePatterns, $excludeFilenamePatterns, '');
        }
    }
}

// For the portfolio repository itself, use local images
// Check by repo name 'portfolio' regardless of owner (works with any fork)
if ($repo === 'portfolio') {
    // Use local images from /images folder
    $localImages = [
        ['name' => 'startpage.png', 'alt' => 'Start Page'],
        ['name' => 'aboutme.png', 'alt' => 'About Me'],
        ['name' => 'projektpage.png', 'alt' => 'Projects Page'],
        ['name' => 'skillspage.png', 'alt' => 'Skills Page'],
        ['name' => 'contact-mepage.png', 'alt' => 'Contact Page']
    ];
    
    foreach ($localImages as $img) {
        $localPath = __DIR__ . '/../images/' . $img['name'];
        if (file_exists($localPath)) {
            // Build absolute URL from current request
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $protocol . '://' . $host;
            $imageUrl = $baseUrl . '/images/' . $img['name'];
            addImage($images, $seenUrls, $imageUrl, $excludePatterns, $excludeFilenamePatterns, $img['alt']);
        }
    }
} else {
    // For other repos, look for images in repository (GitHub)
    if (empty($images)) {
        $repoContents = makeGitHubRequest("{$apiBase}/repos/{$owner}/{$repo}/contents", $token);
        
        if ($repoContents && is_array($repoContents)) {
            $imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
            
            foreach ($repoContents as $item) {
                if ($item['type'] !== 'file') continue;
                
                $ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $imageExtensions)) continue;
                
                $url = $item['download_url'] ?? "https://raw.githubusercontent.com/{$owner}/{$repo}/main/{$item['name']}";
                
                if (shouldExcludeUrl($url, $excludePatterns, $excludeFilenamePatterns)) {
                    continue;
                }
                
                addImage($images, $seenUrls, $url, $excludePatterns, $excludeFilenamePatterns, $item['name']);
            }
            
            // Look for screenshots/ or images/ directories
            $preferredDirs = ['screenshots', 'images', 'assets', 'img'];
            foreach ($repoContents as $item) {
                if ($item['type'] !== 'dir') continue;
                
                $dirName = strtolower($item['name']);
                if (!in_array($dirName, $preferredDirs)) continue;
                
                $dirContents = makeGitHubRequest($item['url'], $token);
                if (!$dirContents || !is_array($dirContents)) continue;
                
                foreach ($dirContents as $file) {
                    if ($file['type'] !== 'file') continue;
                    
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $imageExtensions)) continue;
                    
                    $url = $file['download_url'] ?? $file['html_url'];
                    
                    if (shouldExcludeUrl($url, $excludePatterns, $excludeFilenamePatterns)) {
                        continue;
                    }
                    
                    addImage($images, $seenUrls, $url, $excludePatterns, $excludeFilenamePatterns, $file['name']);
                }
            }
        }
    }
}

// Convert README to HTML
$htmlContent = '';
if (isset($readmeContent)) {
    $htmlContent = parseMarkdownToHtml($readmeContent);
}

// Prepare response
$response = [
    'repo' => [
        'name' => $repoDetails['name'] ?? $repo,
        'full_name' => $repoDetails['full_name'] ?? "{$owner}/{$repo}",
        'description' => $repoDetails['description'] ?? '',
        'html_url' => $repoDetails['html_url'] ?? "https://github.com/{$owner}/{$repo}",
        'homepage' => $repoDetails['homepage'] ?? null,
        'language' => $repoDetails['language'] ?? 'Unknown',
        'languages' => $languages,
        'stargazers_count' => $repoDetails['stargazers_count'] ?? 0,
        'forks_count' => $repoDetails['forks_count'] ?? 0,
        'created_at' => $repoDetails['created_at'] ?? null,
        'updated_at' => $repoDetails['updated_at'] ?? null,
        'topics' => $repoDetails['topics'] ?? []
    ],
    'content' => $htmlContent,
    'images' => $images,
    'owner' => $owner
];

echo json_encode($response);

function parseMarkdownToHtml($markdown) {
    $html = $markdown;
    
    // Remove YAML frontmatter
    $html = preg_replace('/^---\s*\n.*?\n---\s*\n/s', '', $html);
    
    // Headers
    $html = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $html);
    $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
    $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
    $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
    
    // Bold and italic
    $html = preg_replace('/\*\*\*(.+?)\*\*\*/s', '<strong><em>$1</em></strong>', $html);
    $html = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $html);
    $html = preg_replace('/__(.+?)__/s', '<strong>$1</strong>', $html);
    $html = preg_replace('/_(.+?)_/s', '<em>$1</em>', $html);
    
    // Code blocks with language
    $html = preg_replace_callback('/```(\w+)?\n(.*?)```/s', function($matches) {
        $lang = $matches[1] ?? '';
        $code = htmlspecialchars($matches[2]);
        return "<pre><code class=\"language-{$lang}\">{$code}</code></pre>";
    }, $html);
    
    // Inline code
    $html = preg_replace('/`(.+?)`/', '<code>$1</code>', $html);
    
    // Links
    $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>', $html);
    
    // Remove images (they'll be shown in gallery)
    $html = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '', $html);
    $html = preg_replace('/<img[^>]+>/i', '', $html);
    
    // Lists
    $html = preg_replace('/^\s*[-*+] (.+)$/m', '<li>$1</li>', $html);
    $html = preg_replace('/(<li>.*<\/li>\n?)+/', '<ul>$0</ul>', $html);
    
    // Numbered lists
    $html = preg_replace('/^\s*\d+\. (.+)$/m', '<li>$1</li>', $html);
    
    // Blockquotes
    $html = preg_replace('/^> (.+)$/m', '<blockquote>$1</blockquote>', $html);
    
    // Horizontal rules
    $html = preg_replace('/^---+$/m', '<hr>', $html);
    
    // Tables (basic support)
    $html = preg_replace('/^\|(.+)\|$/m', '<tr><td>$1</td></tr>', $html);
    $html = preg_replace('/<td>([^|]+)\|([^<]+)<\/td>/', '<td>$1</td><td>$2</td>', $html);
    
    // Paragraphs
    $paragraphs = preg_split('/\n\n+/', $html);
    $result = [];
    foreach ($paragraphs as $para) {
        $para = trim($para);
        if (empty($para)) continue;
        
        // Skip if already wrapped in HTML tags
        if (preg_match('/^<(h[1-6]|ul|ol|li|blockquote|pre|hr|table|tr)/', $para)) {
            $result[] = $para;
        } else {
            $result[] = '<p>' . $para . '</p>';
        }
    }
    
    $html = implode("\n\n", $result);
    
    // Clean up line breaks within paragraphs
    $html = preg_replace_callback('/<p>(.+?)<\/p>/s', function($matches) {
        $content = str_replace("\n", ' ', $matches[1]);
        return '<p>' . $content . '</p>';
    }, $html);
    
    return $html;
}
