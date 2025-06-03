<?php
/**
 * Entry point aplikasi ChatCare
 * 
 * File ini berfungsi sebagai entry point untuk semua request ke aplikasi
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

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
