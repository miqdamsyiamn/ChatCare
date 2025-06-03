<?php
/**
 * Report Controller
 * 
 * Controller untuk menangani laporan diskusi
 */

class ReportController extends Controller {
    private $sessionModel;
    private $reportModel;
    
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
        $this->reportModel = $this->model('Report');
    }
    
    /**
     * Report index page
     * 
     * @return void
     */
    public function index() {
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        
        // Get sessions based on role
        if ($role == 'admin') {
            $reports = $this->reportModel->getReports();
        } elseif ($role == 'leader') {
            // Get sessions for leader
            $sessions = $this->sessionModel->getSessionsByUserId($userId);
            $sessionIds = array_column($sessions, 'session_id');
            
            // Get reports for these sessions
            $reports = [];
            foreach ($sessionIds as $sessionId) {
                $sessionReports = $this->reportModel->getReportsBySessionId($sessionId);
                $reports = array_merge($reports, $sessionReports);
            }
        } else {
            // Get sessions for member
            $sessions = $this->sessionModel->getSessionsByUserId($userId);
            $sessionIds = array_column($sessions, 'session_id');
            
            // Get reports for these sessions
            $reports = [];
            foreach ($sessionIds as $sessionId) {
                $sessionReports = $this->reportModel->getReportsBySessionId($sessionId);
                $reports = array_merge($reports, $sessionReports);
            }
        }
        
        $data = [
            'title' => 'Laporan Diskusi',
            'reports' => $reports,
            'role' => $role
        ];
        
        $this->view('reports/index', $data);
    }
    
    /**
     * View report detail
     * 
     * @return void
     */
    public function viewReport() {
        if (!isset($_GET['id'])) {
            $this->redirect('/reports');
        }
        
        $reportId = $_GET['id'];
        $report = $this->reportModel->getReportById($reportId);
        
        if (!$report) {
            $_SESSION['error_message'] = 'Laporan tidak ditemukan';
            $this->redirect('/reports');
        }
        
        // Check if user is allowed to view this report
        $session = $this->sessionModel->getSessionById($report['session_id']);
        if (!$session) {
            $_SESSION['error_message'] = 'Sesi diskusi tidak ditemukan';
            $this->redirect('/reports');
        }
        
        // Admin can view all reports, others only if they're participants
        if ($_SESSION['role'] != 'admin' && !$this->sessionModel->isParticipant($report['session_id'], $_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Anda tidak memiliki akses untuk melihat laporan ini';
            $this->redirect('/reports');
        }
        
        // Get session details
        $participants = json_decode($session['participants'], true);
        $sessionTitle = $participants['title'];
        
        $data = [
            'title' => 'Laporan Diskusi: ' . $sessionTitle,
            'report' => $report,
            'session' => $session,
            'session_title' => $sessionTitle
        ];
        
        $this->view('reports/view', $data);
    }
    
    /**
     * Generate report
     * 
     * @return void
     */
    public function generate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $sessionId = $_POST['session_id'];
            
            // Check if session exists
            $session = $this->sessionModel->getSessionById($sessionId);
            if (!$session) {
                $this->redirect('/reports');
            }
            
            // Check if user is allowed to generate report for this session
            if ($_SESSION['role'] != 'admin' && !$this->sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
                $this->redirect('/reports');
            }
            
            // Check if session is ended
            if ($session['end_time'] === null) {
                $_SESSION['error_message'] = 'Diskusi belum berakhir';
                $this->redirect('/discussions');
            }
            
            // Generate report
            $reportId = $this->reportModel->generateReport($sessionId);
            
            if ($reportId) {
                $_SESSION['success_message'] = 'Laporan berhasil dibuat';
                $this->redirect('/reports/view?id=' . $reportId);
            } else {
                $_SESSION['error_message'] = 'Gagal membuat laporan';
                $this->redirect('/reports');
            }
        } else {
            $this->redirect('/reports');
        }
    }
}
