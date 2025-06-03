<?php
/**
 * App Class
 * 
 * Kelas utama yang menangani routing dan menjalankan controller yang sesuai
 */

class App {
    protected $routes = [];
    
    public function __construct() {
        // Load routes from routes file
        $this->routes = require_once BASE_PATH . '/routes/web.php';
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function run() {
        // Get current URL path
        $path = $this->getPath();
        
        // Find matching route
        if (isset($this->routes[$path])) {
            $route = $this->routes[$path];
            $controllerName = $route['controller'];
            $method = $route['method'];
            
            // Load controller
            require_once BASE_PATH . '/app/controllers/' . $controllerName . '.php';
            $controller = new $controllerName();
            
            // Call method
            call_user_func([$controller, $method]);
        } else {
            // Route not found, show 404 page
            header('HTTP/1.0 404 Not Found');
            echo '<h1>404 - Page Not Found</h1>';
            echo '<p>Halaman yang Anda cari tidak ditemukan.</p>';
            echo '<p><a href="/">Kembali ke halaman utama</a></p>';
        }
    }
    
    protected function getPath() {
        if (isset($_GET['url'])) {
            return rtrim($_GET['url'], '/');
        }
        return '';
    }
}
