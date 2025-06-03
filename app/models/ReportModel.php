<?php
/**
 * Report Model
 * 
 * Model untuk menangani operasi terkait laporan sesi diskusi
 */

class ReportModel {
    private $db;
    private $geminiService;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
        $this->geminiService = new GeminiService();
    }
    
    /**
     * Get all reports
     * 
     * @return array
     */
    public function getReports() {
        $this->db->query("SELECT r.*, s.start_time, s.end_time FROM session_report r 
                          LEFT JOIN session s ON r.session_id = s.session_id 
                          ORDER BY r.generated_at DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Get report by ID
     * 
     * @param int $id Report ID
     * @return array
     */
    public function getReportById($id) {
        $this->db->query("SELECT r.*, s.start_time, s.end_time FROM session_report r 
                          LEFT JOIN session s ON r.session_id = s.session_id 
                          WHERE r.report_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Get reports by session ID
     * 
     * @param int $sessionId Session ID
     * @return array
     */
    public function getReportsBySessionId($sessionId) {
        $this->db->query("SELECT r.*, s.start_time, s.end_time 
                          FROM session_report r 
                          LEFT JOIN session s ON r.session_id = s.session_id 
                          WHERE r.session_id = :session_id 
                          ORDER BY r.generated_at DESC");
        $this->db->bind(':session_id', $sessionId);
        return $this->db->resultSet();
    }
    
    /**
     * Generate report for session
     * 
     * @param int $sessionId Session ID
     * @return int|bool Last insert ID or false
     */
    public function generateReport($sessionId) {
        // Get session details
        $sessionModel = new SessionModel();
        $session = $sessionModel->getSessionById($sessionId);
        
        if (!$session) {
            return false;
        }
        
        // Get messages for this session
        $messageModel = new MessageModel();
        $messages = $messageModel->getMessagesByChatId($session['chat_log_id']);
        
        // Generate summary using Gemini
        $summary = $this->geminiService->generateSessionSummary($messages);
        
        // Create report
        $this->db->query("INSERT INTO session_report (session_id, summary, generated_at) 
                          VALUES (:session_id, :summary, :generated_at)");
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':summary', $summary);
        $this->db->bind(':generated_at', date('Y-m-d H:i:s'));
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Delete report
     * 
     * @param int $id Report ID
     * @return bool
     */
    public function deleteReport($id) {
        $this->db->query("DELETE FROM session_report WHERE report_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
