<?php
/**
 * Home Controller
 * 
 * Controller default untuk aplikasi
 */

class HomeController extends Controller {
    /**
     * Constructor
     */
    public function __construct() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Home index page
     * 
     * @return void
     */
    public function index() {
        // Tampilkan halaman landing page dengan pengenalan aplikasi
        $this->view('home/index');
    }
}
