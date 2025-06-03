<?php
/**
 * Controller Class
 * 
 * Kelas dasar untuk semua controller dalam aplikasi
 */

class Controller {
    /**
     * Load model
     * 
     * @param string $model Nama model yang akan diload
     * @return object Instance dari model
     */
    public function model($model) {
        $modelName = ucfirst($model) . 'Model';
        require_once BASE_PATH . '/app/models/' . $modelName . '.php';
        return new $modelName();
    }
    
    /**
     * Load view
     * 
     * @param string $view Nama view yang akan diload
     * @param array $data Data yang akan dikirim ke view
     * @return void
     */
    public function view($view, $data = []) {
        // Extract data untuk digunakan dalam view
        if (!empty($data)) {
            extract($data);
        }
        
        // Cek apakah file view ada
        $viewFile = BASE_PATH . '/app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die('View tidak ditemukan: ' . $view);
        }
    }
    
    /**
     * Redirect ke URL tertentu
     * 
     * @param string $url URL tujuan
     * @return void
     */
    public function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True jika user sudah login, false jika belum
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check user role
     * 
     * @param string|array $roles Role yang diizinkan
     * @return bool True jika user memiliki role yang diizinkan
     */
    public function hasRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array($_SESSION['role'], $roles);
    }
    
    /**
     * Return JSON response
     * 
     * @param array $data Data yang akan dikirim sebagai JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function jsonResponse($data, $statusCode = 200) {
        // Pastikan tidak ada output sebelumnya yang mengganggu respons JSON
        if (ob_get_length()) ob_clean();
        
        // Set header dengan benar
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        
        // Log untuk debugging
        error_log('JSON Response: ' . json_encode($data));
        
        // Encode dengan opsi untuk menangani karakter Unicode dan error
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        exit;
    }
}
