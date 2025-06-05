<?php
/**
 * Entry point aplikasi ChatCare
 * 
 * File ini berfungsi sebagai entry point untuk semua request ke aplikasi
 */

// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log errors
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php-errors.log');

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Define base path
define('BASE_PATH', __DIR__);

// Set include path to include the application directory
set_include_path(get_include_path() . PATH_SEPARATOR . BASE_PATH);

// Load autoloader
require_once BASE_PATH . '/app/core/Autoloader.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle URL for PHP built-in server
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Set the URL in $_GET['url'] for the App class to use
if ($uri !== '/' && $uri !== '') {
    $_GET['url'] = trim($uri, '/');
} else {
    $_GET['url'] = '';
}

// Initialize application
require_once BASE_PATH . '/app/core/App.php';

// Run the application
$app = new App();
$app->run();
