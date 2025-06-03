<?php
/**
 * Discussion Controller
 * 
 * Controller untuk menangani manajemen diskusi (khusus leader)
 */

class DiscussionController extends Controller {
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
        
        // Check if user is leader
        if (!$this->hasRole(['leader', 'admin'])) {
            $this->redirect('/dashboard');
        }
        
        $this->sessionModel = $this->model('Session');
        $this->userModel = $this->model('User');
    }
    
    /**
     * Discussion index page
     * 
     * @return void
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        
        // Get sessions based on role
        if ($role == 'admin') {
            $sessions = $this->sessionModel->getSessions();
        } else {
            $sessions = $this->sessionModel->getSessionsByUserId($userId);
        }
        
        $data = [
            'title' => 'Kelola Diskusi',
            'sessions' => $sessions
        ];
        
        $this->view('discussions/index', $data);
    }
    
    /**
     * Create discussion page
     * 
     * @return void
     */
    public function create() {
        $users = $this->userModel->getUsers();
        
        $data = [
            'title' => 'Buat Diskusi Baru',
            'users' => $users,
            'title_err' => '',
            'description_err' => '',
            'participants_err' => ''
        ];
        
        $this->view('discussions/create', $data);
    }
    
    /**
     * Store new discussion
     * 
     * @return void
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Get form data
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $participants = isset($_POST['participants']) ? $_POST['participants'] : [];
            
            $data = [
                'title' => 'Buat Diskusi Baru',
                'users' => $this->userModel->getUsers(),
                'discussion_title' => $title,
                'description' => $description,
                'participants' => $participants,
                'title_err' => '',
                'description_err' => '',
                'participants_err' => ''
            ];
            
            // Validate title
            if (empty($title)) {
                $data['title_err'] = 'Silakan masukkan judul diskusi';
            }
            
            // Validate description
            if (empty($description)) {
                $data['description_err'] = 'Silakan masukkan deskripsi diskusi';
            }
            
            // Validate participants
            if (empty($participants)) {
                $data['participants_err'] = 'Silakan pilih minimal satu peserta';
            }
            
            // Check for errors
            if (empty($data['title_err']) && empty($data['description_err']) && empty($data['participants_err'])) {
                // Add current user (leader) to participants
                if (!in_array($_SESSION['user_id'], $participants)) {
                    $participants[] = $_SESSION['user_id'];
                }
                
                // Create session
                $sessionData = [
                    'participants' => [
                        'title' => $title,
                        'description' => $description,
                        'user_ids' => $participants
                    ],
                    'creator_id' => $_SESSION['user_id']
                ];
                
                $sessionId = $this->sessionModel->create($sessionData);
                
                if ($sessionId) {
                    // Set flash message
                    $_SESSION['success_message'] = 'Diskusi berhasil dibuat';
                    $this->redirect('/discussions');
                } else {
                    die('Terjadi kesalahan');
                }
            } else {
                // Load view with errors
                $this->view('discussions/create', $data);
            }
        } else {
            $this->redirect('/discussions/create');
        }
    }
    
    /**
     * View discussion members
     * 
     * @return void
     */
    public function members() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
            $sessionId = $_GET['id'];
            $session = $this->sessionModel->getSessionById($sessionId);
            
            if (!$session) {
                $this->redirect('/discussions');
            }
            
            // Check if user is allowed to view this session
            if ($_SESSION['role'] != 'admin' && !$this->sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
                $this->redirect('/discussions');
            }
            
            $participants = json_decode($session['participants'], true);
            $participantIds = $participants['user_ids'];
            
            $participantUsers = [];
            foreach ($participantIds as $participantId) {
                $user = $this->userModel->getUserById($participantId);
                if ($user) {
                    $participantUsers[] = $user;
                }
            }
            
            $allUsers = $this->userModel->getUsers();
            
            $data = [
                'title' => 'Peserta Diskusi',
                'session' => $session,
                'participants' => $participantUsers,
                'all_users' => $allUsers,
                'session_title' => $participants['title'],
                'session_description' => $participants['description']
            ];
            
            $this->view('discussions/members', $data);
        } else {
            $this->redirect('/discussions');
        }
    }
    
    /**
     * Add member to discussion
     * 
     * @return void
     */
    public function addMember() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionId = $_POST['session_id'];
            $userId = $_POST['user_id'];
            
            // Check if session exists
            $session = $this->sessionModel->getSessionById($sessionId);
            if (!$session) {
                $this->redirect('/discussions');
            }
            
            // Check if user is allowed to modify this session
            // Jika user adalah admin, diizinkan untuk mengelola semua diskusi
            if ($_SESSION['role'] == 'admin') {
                // Admin diizinkan mengelola semua diskusi
                $isAllowed = true;
            } 
            // Jika user adalah pembuat diskusi, dia selalu diizinkan
            else if ($_SESSION['user_id'] == $session['creator_id']) {
                $isAllowed = true;
            }
            // Jika tidak memenuhi kondisi di atas, tidak diizinkan
            else {
                $isAllowed = false;
            }
            
            if (!$isAllowed) {
                $_SESSION['error_message'] = 'Anda tidak memiliki izin untuk mengelola diskusi ini';
                $this->redirect('/discussions');
            }
            
            // Add member
            if ($this->sessionModel->addParticipant($sessionId, $userId)) {
                // Set flash message
                $_SESSION['success_message'] = 'Peserta berhasil ditambahkan';
            } else {
                // Set flash message
                $_SESSION['error_message'] = 'Gagal menambahkan peserta';
            }
            
            $this->redirect('/discussions/members?id=' . $sessionId);
        } else {
            $this->redirect('/discussions');
        }
    }
    
    /**
     * Remove member from discussion
     * 
     * @return void
     */
    public function removeMember() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionId = $_POST['session_id'];
            $userId = $_POST['user_id'];
            
            // Check if session exists
            $session = $this->sessionModel->getSessionById($sessionId);
            if (!$session) {
                $this->redirect('/discussions');
            }
            
            // Check if user is allowed to modify this session
            // Jika user adalah admin, diizinkan untuk mengelola semua diskusi
            if ($_SESSION['role'] == 'admin') {
                // Admin diizinkan mengelola semua diskusi
                $isAllowed = true;
            } 
            // Jika user adalah pembuat diskusi, dia selalu diizinkan
            else if ($_SESSION['user_id'] == $session['creator_id']) {
                $isAllowed = true;
            }
            // Jika tidak memenuhi kondisi di atas, tidak diizinkan
            else {
                $isAllowed = false;
            }
            
            if (!$isAllowed) {
                $_SESSION['error_message'] = 'Anda tidak memiliki izin untuk mengelola diskusi ini';
                $this->redirect('/discussions');
            }
            
            // Remove member
            if ($this->sessionModel->removeParticipant($sessionId, $userId)) {
                // Set flash message
                $_SESSION['success_message'] = 'Peserta berhasil dihapus';
            } else {
                // Set flash message
                $_SESSION['error_message'] = 'Gagal menghapus peserta';
            }
            
            $this->redirect('/discussions/members?id=' . $sessionId);
        } else {
            $this->redirect('/discussions');
        }
    }
    
    /**
     * Start discussion
     * 
     * @return void
     */
    public function start() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionId = $_POST['session_id'];
            
            // Check if session exists
            $session = $this->sessionModel->getSessionById($sessionId);
            if (!$session) {
                $this->redirect('/discussions');
            }
            
            // Check if user is allowed to start this session
            // Jika user adalah admin, diizinkan untuk mengelola semua diskusi
            if ($_SESSION['role'] == 'admin') {
                // Admin diizinkan mengelola semua diskusi
                $isAllowed = true;
            } 
            // Jika user adalah pembuat diskusi, dia selalu diizinkan
            else if ($_SESSION['user_id'] == $session['creator_id']) {
                $isAllowed = true;
            }
            // Jika tidak memenuhi kondisi di atas, tidak diizinkan
            else {
                $isAllowed = false;
            }
            
            if (!$isAllowed) {
                $_SESSION['error_message'] = 'Anda tidak memiliki izin untuk memulai diskusi ini';
                $this->redirect('/discussions');
            }
            
            // Check if session is already started
            if ($session['start_time'] !== null && $session['end_time'] === null) {
                // Session already started
                $this->redirect('/chatroom?session_id=' . $sessionId);
            }
            
            // Start session (already started when created, so no need to update)
            $_SESSION['success_message'] = 'Diskusi berhasil dimulai';
            $this->redirect('/chatroom?session_id=' . $sessionId);
        } else {
            $this->redirect('/discussions');
        }
    }
    
    /**
     * End discussion
     * 
     * @return void
     */
    public function end() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionId = $_POST['session_id'];
            
            // Check if session exists
            $session = $this->sessionModel->getSessionById($sessionId);
            if (!$session) {
                $this->redirect('/discussions');
            }
            
            // Check if user is allowed to end this session
            // Jika user adalah admin, diizinkan untuk mengelola semua diskusi
            if ($_SESSION['role'] == 'admin') {
                // Admin diizinkan mengelola semua diskusi
                $isAllowed = true;
            } 
            // Jika user adalah pembuat diskusi, dia selalu diizinkan
            else if ($_SESSION['user_id'] == $session['creator_id']) {
                $isAllowed = true;
            }
            // Jika tidak memenuhi kondisi di atas, tidak diizinkan
            else {
                $isAllowed = false;
            }
            
            if (!$isAllowed) {
                $_SESSION['error_message'] = 'Anda tidak memiliki izin untuk mengakhiri diskusi ini';
                $this->redirect('/discussions');
            }
            
            // Check if session is already ended
            if ($session['end_time'] !== null) {
                // Session already ended
                $this->redirect('/discussions');
            }
            
            // End session
            if ($this->sessionModel->endSession($sessionId)) {
                // Generate report
                $reportModel = $this->model('Report');
                $reportModel->generateReport($sessionId);
                
                // Set flash message
                $_SESSION['success_message'] = 'Diskusi berhasil diakhiri';
            } else {
                // Set flash message
                $_SESSION['error_message'] = 'Gagal mengakhiri diskusi';
            }
            
            $this->redirect('/discussions');
        } else {
            $this->redirect('/discussions');
        }
    }
}
