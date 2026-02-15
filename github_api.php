<?php
/**
 * GitHub API Service
 * Handles fetching repositories from GitHub API with separation of owned and contributed repos
 * Filters: Blacklist support, sorted by stars, no code display (README only)
 * For contributed repos: Shows original repo, not forks. Shows user's fork link if public.
 */

require_once __DIR__ . '/config/env_loader.php';

class GitHubAPIService {
    private string $username;
    private ?string $token;
    private string $apiUrl;
    private array $hiddenProjects;
    
    public function __construct() {
        $this->username = getenv('GITHUB_USERNAME') ?: 'Samuel-Mencke';
        $this->token = getenv('GITHUB_TOKEN') ?: null;
        $this->apiUrl = 'https://api.github.com';
        
        // Parse hidden projects from env (comma-separated)
        $hiddenEnv = getenv('HIDDEN_PROJECTS') ?: '';
        $this->hiddenProjects = array_map('trim', explode(',', $hiddenEnv));
        // Always hide username/username (profile README)
        $this->hiddenProjects[] = $this->username;
    }
    
    /**
     * Fetch both owned and contributed repositories, sorted by stars
     * 
     * @return array ['owned' => [...], 'contributed' => [...]]
     */
    public function fetchRepositories(): array {
        $owned = $this->fetchOwnedRepositories();
        $contributed = $this->fetchContributedRepositories();
        
        // Sort both arrays by stars (descending)
        usort($owned, function($a, $b) {
            return $b['stargazers_count'] <=> $a['stargazers_count'];
        });
        
        usort($contributed, function($a, $b) {
            return $b['stargazers_count'] <=> $a['stargazers_count'];
        });
        
        return [
            'owned' => $owned,
            'contributed' => $contributed
        ];
    }
    
    /**
     * Check if repository should be hidden
     */
    private function isHidden(string $repoName): bool {
        // Check exact match
        if (in_array($repoName, $this->hiddenProjects, true)) {
            return true;
        }
        
        // Check if repo name contains hidden project name
        foreach ($this->hiddenProjects as $hidden) {
            if (empty($hidden)) continue;
            if (stripos($repoName, $hidden) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Fetch repositories owned by the user
     */
    private function fetchOwnedRepositories(): array {
        $repos = $this->makeRequest("{$this->apiUrl}/users/{$this->username}/repos?sort=stars&per_page=100&type=owner");
        
        if (!$repos) {
            return $this->getFallbackProjects();
        }
        
        return $this->processRepositories($repos, true);
    }
    
    /**
     * Fetch repositories where user contributed (but doesn't own)
     * Shows original repositories, not forks. If user forked it, shows fork link.
     */
    private function fetchContributedRepositories(): array {
        // Get user's events to find contributed repos
        $events = $this->makeRequest("{$this->apiUrl}/users/{$this->username}/events/public?per_page=100");
        
        if (!$events) {
            return [];
        }
        
        $contributedRepos = [];
        $seenIds = [];
        
        foreach ($events as $event) {
            // Look for PushEvents and PullRequestEvents
            if (!in_array($event['type'] ?? '', ['PushEvent', 'PullRequestEvent'])) {
                continue;
            }
            
            if (!isset($event['repo']['name'])) {
                continue;
            }
            
            $repoFullName = $event['repo']['name'];
            
            // Skip own repos (user is the owner)
            if (str_starts_with($repoFullName, $this->username . '/')) {
                continue;
            }
            
            // Fetch full repo details
            $repoDetails = $this->makeRequest("{$this->apiUrl}/repos/{$repoFullName}");
            
            if (!$repoDetails) {
                continue;
            }
            
            $targetRepo = null;
            $userForkUrl = null;
            $isForkContribution = false;
            
            // If this is a fork, get the original repository
            if ($repoDetails['fork'] ?? false) {
                $isForkContribution = true;
                
                // Fetch fork details to get parent info
                $forkDetails = $this->makeRequest("{$this->apiUrl}/repos/{$repoFullName}");
                
                if ($forkDetails && isset($forkDetails['parent'])) {
                    $parentRepo = $forkDetails['parent'];
                    $targetRepo = $parentRepo;
                    
                    // Check if user's fork is public
                    if (!($forkDetails['private'] ?? true)) {
                        $userForkUrl = $forkDetails['html_url'];
                    }
                } else {
                    // Can't get parent, skip this
                    continue;
                }
            } else {
                // Not a fork, use as-is
                $targetRepo = $repoDetails;
                
                // Check if user has a public fork of this repo
                $userForkUrl = $this->findUserFork($targetRepo['full_name']);
            }
            
            if (!$targetRepo) {
                continue;
            }
            
            // Skip if already seen (by original repo ID)
            $originalId = $targetRepo['id'];
            if (in_array($originalId, $seenIds)) {
                continue;
            }
            
            // Skip if this would be in owned repos
            if (str_starts_with($targetRepo['full_name'], $this->username . '/')) {
                continue;
            }
            
            $seenIds[] = $originalId;
            $processedRepo = $this->processContributedRepository($targetRepo, $userForkUrl, $isForkContribution);
            
            if ($processedRepo !== null) {
                $contributedRepos[] = $processedRepo;
            }
        }
        
        return $contributedRepos;
    }
    
    /**
     * Cached user forks to avoid multiple API calls
     */
    private ?array $userForksCache = null;
    
    /**
     * Find if user has a public fork of a repository (uses cache)
     */
    private function findUserFork(string $originalFullName): ?string {
        // Load forks once and cache
        if ($this->userForksCache === null) {
            $this->userForksCache = [];
            $userRepos = $this->makeRequest("{$this->apiUrl}/users/{$this->username}/repos?type=forks&per_page=30");
            
            if ($userRepos) {
                foreach ($userRepos as $repo) {
                    if (($repo['fork'] ?? false) && isset($repo['parent'])) {
                        $parentName = $repo['parent']['full_name'];
                        if (!($repo['private'] ?? true)) {
                            $this->userForksCache[$parentName] = $repo['html_url'];
                        }
                    }
                }
            }
        }
        
        return $this->userForksCache[$originalFullName] ?? null;
    }
    
    /**
     * Process contributed repository with fork information
     */
    private function processContributedRepository(array $repo, ?string $userForkUrl, bool $isForkContribution): ?array {
        $name = $repo['name'];
        $fullName = $repo['full_name'] ?? $repo['name'];
        
        // Skip hidden projects
        if ($this->isHidden($name)) {
            return null;
        }
        
        // Skip repos with numeric-only names (like "1")
        if (is_numeric($name)) {
            return null;
        }
        
        return [
            'id' => $repo['id'],
            'name' => $name,
            'full_name' => $fullName,
            'description' => $repo['description'] ?: 'No description available',
            'language' => $repo['language'] ?? 'Unknown',
            'stargazers_count' => $repo['stargazers_count'] ?? 0,
            'html_url' => $repo['html_url'],
            'homepage' => $repo['homepage'] ?? null,
            'created_at' => $repo['created_at'],
            'updated_at' => $repo['updated_at'],
            'is_owned' => false,
            'is_private' => $repo['private'] ?? false,
            'is_fork_contribution' => $isForkContribution,
            'user_fork_url' => $userForkUrl
        ];
    }
    
    /**
     * Make HTTP request to GitHub API
     */
    private function makeRequest(string $url): ?array {
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Portfolio-Site/1.0',
                    'Accept: application/vnd.github.v3+json'
                ],
                'timeout' => 10,
                'follow_location' => true
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ];
        
        if ($this->token) {
            $options['http']['header'][] = 'Authorization: Bearer ' . $this->token;
        }
        
        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return null;
        }
        
        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }
    
    /**
     * Process multiple repositories
     */
    private function processRepositories(array $repos, bool $isOwned): array {
        $processed = [];
        
        foreach ($repos as $repo) {
            // Skip forks in owned repos (we handle forks separately in contributed)
            if ($repo['fork'] ?? false) {
                continue;
            }
            
            $processedRepo = $this->processSingleRepository($repo, $isOwned);
            if ($processedRepo !== null) {
                $processed[] = $processedRepo;
            }
        }
        
        return $processed;
    }
    
    /**
     * Process single repository
     */
    private function processSingleRepository(array $repo, bool $isOwned): ?array {
        $name = $repo['name'];
        $fullName = $repo['full_name'] ?? $repo['name'];
        
        // Skip hidden projects
        if ($this->isHidden($name)) {
            return null;
        }
        
        // Skip repos with numeric-only names (like "1")
        if (is_numeric($name)) {
            return null;
        }
        
        return [
            'id' => $repo['id'],
            'name' => $name,
            'full_name' => $fullName,
            'description' => $repo['description'] ?: 'No description available',
            'language' => $repo['language'] ?? 'Unknown',
            'stargazers_count' => $repo['stargazers_count'] ?? 0,
            'html_url' => $repo['html_url'],
            'homepage' => $repo['homepage'] ?? null,
            'created_at' => $repo['created_at'],
            'updated_at' => $repo['updated_at'],
            'is_owned' => $isOwned,
            'is_private' => $repo['private'] ?? false,
            'is_fork_contribution' => false,
            'user_fork_url' => null
        ];
    }
    
    /**
     * Fallback projects in case API fails
     */
    private function getFallbackProjects(): array {
        return [
            [
                'id' => 1,
                'name' => 'portfolio',
                'full_name' => $this->username . '/portfolio',
                'description' => 'My personal portfolio website with 3D effects',
                'language' => 'PHP',
                'stargazers_count' => 0,
                'html_url' => 'https://github.com/' . $this->username . '/portfolio',
                'homepage' => null,
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
                'is_owned' => true,
                'is_private' => false,
                'is_fork_contribution' => false,
                'user_fork_url' => null
            ]
        ];
    }
}
