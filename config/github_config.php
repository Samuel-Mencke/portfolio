<?php
/**
 * GitHub API Configuration
 * 
 * Set your GitHub username and optional token here.
 * Token can also be set via server environment variable GITHUB_TOKEN
 */

// Load environment variables
require_once __DIR__ . '/env_loader.php';

// GitHub Username from environment or default
if (!defined('GITHUB_USERNAME')) {
    $username = getenv('GITHUB_USERNAME') ?: 'samuel-mencke';
    define('GITHUB_USERNAME', $username);
}

// GitHub Personal Access Token (optional, increases rate limit)
// Can be set via environment variable for security
if (!defined('GITHUB_TOKEN')) {
    $envToken = getenv('GITHUB_TOKEN') ?: null;
    define('GITHUB_TOKEN', $envToken);
}

// Whitelist of repositories to display in portfolio
// Leave empty to show all public repos
if (!defined('GITHUB_REPO_WHITELIST')) {
    define('GITHUB_REPO_WHITELIST', [
        // Add specific repo names here if you want to filter
        // e.g., 'portfolio', 'project-name', etc.
    ]);
}

// GitHub API Base URL
if (!defined('GITHUB_API_BASE_URL')) {
    define('GITHUB_API_BASE_URL', 'https://api.github.com');
}
