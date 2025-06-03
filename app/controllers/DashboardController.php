<?php
/**
 * Dashboard Controller
 * 
 * Controller untuk menangani halaman dashboard
 */

class DashboardController extends Controller {
    private $sessionModel;
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        $this->sessionModel = $this->model('Session');
        $this->userModel = $this->model('User');
    }
    
    /**
     * Dashboard index page
     * 
     * @return void
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        
        // Get data based on user role
        if ($role == 'admin') {
            // Admin sees all sessions
            $sessions = $this->sessionModel->getSessions();
            $users = $this->userModel->getUsers();
            
            $data = [
                'title' => 'Dashboard Admin',
                'sessions' => $sessions,
                'users' => $users,
                'active_sessions' => count(array_filter($sessions, function($session) {
                    return $session['end_time'] === null;
                })),
                'total_users' => count($users)
            ];
        } elseif ($role == 'leader') {
            // Leader sees sessions they created
            $sessions = $this->sessionModel->getSessionsByUserId($userId);
            
            $data = [
                'title' => 'Dashboard Leader',
                'sessions' => $sessions,
                'active_sessions' => count(array_filter($sessions, function($session) {
                    return $session['end_time'] === null;
                }))
            ];
        } else {
            // Member sees sessions they're part of
            $sessions = $this->sessionModel->getSessionsByUserId($userId);
            
            $data = [
                'title' => 'Dashboard Member',
                'sessions' => $sessions,
                'active_sessions' => count(array_filter($sessions, function($session) {
                    return $session['end_time'] === null;
                }))
            ];
        }
        
        $this->view('dashboard/index', $data);
    }
}
