<?php
/**
 * Database Configuration
 * Uses environment variables from .env file
 */

// Load environment variables
require_once __DIR__ . '/env_loader.php';

// Database configuration from environment variables
$config = [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'port'     => getenv('DB_PORT') ?: '3306',
    'dbname'   => getenv('DB_DATABASE') ?: 'portfolio',
    'user'     => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
];

// Alternative: Load from config.json if .env values are not set
if (empty($config['password']) && file_exists(__DIR__ . '/config.json')) {
    $jsonConfig = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    if ($jsonConfig) {
        $config = array_merge($config, $jsonConfig);
    }
}
