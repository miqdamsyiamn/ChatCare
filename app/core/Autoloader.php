<?php
/**
 * Autoloader Class
 * 
 * Kelas ini menangani autoloading class-class dalam aplikasi
 */

class Autoloader {
    public static function register() {
        spl_autoload_register(function ($class) {
            // Convert namespace to file path
            $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            
            // Define base directories to look for classes
            $directories = [
                BASE_PATH . '/app/controllers/',
                BASE_PATH . '/app/models/',
                BASE_PATH . '/app/core/'
            ];
            
            // Try to find and include the class file
            foreach ($directories as $directory) {
                $file = $directory . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
            }
            
            return false;
        });
    }
}

// Register the autoloader
Autoloader::register();
