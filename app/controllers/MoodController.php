<?php
/**
 * Mood Controller
 * 
 * Controller untuk menangani visualisasi mood
 */

class MoodController extends Controller {
    private $sessionModel;
    private $moodModel;
    
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
        $this->moodModel = $this->model('Mood');
    }
    
    /**
     * Mood index page
     * 
     * @return void
     */
    public function index() {
        if (!isset($_GET['session_id'])) {
            $this->redirect('/dashboard');
        }
        
        $sessionId = $_GET['session_id'];
        $session = $this->sessionModel->getSessionById($sessionId);
        
        if (!$session) {
            $this->redirect('/dashboard');
        }
        
        // Check if user is participant in this session
        if (!$this->sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        
        // Get session details
        $participants = json_decode($session['participants'], true);
        $sessionTitle = $participants['title'];
        
        // Get mood data
        $moodData = $this->moodModel->getMoodData($sessionId);
        
        // Get user mood data if member
        $userMoodData = null;
        if ($_SESSION['role'] == 'member') {
            $userMoodData = $this->moodModel->getUserMoodData($sessionId, $_SESSION['user_id']);
        }
        
        $data = [
            'title' => 'Visualisasi Emosi: ' . $sessionTitle,
            'session' => $session,
            'session_title' => $sessionTitle,
            'mood_data' => $moodData,
            'user_mood_data' => $userMoodData,
            'role' => $_SESSION['role']
        ];
        
        $this->view('mood/index', $data);
    }
    
    /**
     * Get mood data
     * 
     * @return void
     */
    public function getData() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $sessionId = $_GET['session_id'];
                
                // Get session
                $session = $this->sessionModel->getSessionById($sessionId);
                if (!$session) {
                    $this->jsonResponse(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);
                }
                
                // Check if user is participant
                if (!$this->sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
                    $this->jsonResponse(['success' => false, 'message' => 'Anda bukan peserta diskusi ini'], 403);
                }
                
                // Get mood data
                $moodData = $this->moodModel->getMoodData($sessionId);
                
                // Get user mood data if member
                $userMoodData = null;
                if ($_SESSION['role'] == 'member') {
                    $userMoodData = $this->moodModel->getUserMoodData($sessionId, $_SESSION['user_id']);
                }
                
                // Pastikan data yang dikembalikan sesuai dengan format yang diharapkan oleh frontend
                $this->jsonResponse([
                    'success' => true,
                    'data' => [
                        'labels' => $moodData['labels'],
                        'datasets' => $moodData['datasets'],
                        'summary' => $moodData['summary'],
                        'user_mood_data' => $userMoodData
                    ]
                ]);
            } else {
                $this->redirect('/dashboard');
            }
        } else {
            $this->redirect('/dashboard');
        }
    }
}
