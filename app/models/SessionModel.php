<?php
/**
 * Session Model
 * 
 * Model untuk menangani operasi terkait sesi diskusi
 */

class SessionModel {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get all sessions
     * 
     * @return array
     */
    public function getSessions() {
        $this->db->query("SELECT s.*, c.chat_id FROM session s 
                          LEFT JOIN chat_log c ON s.chat_log_id = c.chat_id 
                          ORDER BY s.start_time DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Get session by ID
     * 
     * @param int $id Session ID
     * @return array
     */
    public function getSessionById($id) {
        $this->db->query("SELECT s.*, c.chat_id FROM session s 
                          LEFT JOIN chat_log c ON s.chat_log_id = c.chat_id 
                          WHERE s.session_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Get sessions by user ID
     * 
     * @param string $userId User ID
     * @return array
     */
    public function getSessionsByUserId($userId) {
        $this->db->query("SELECT s.*, c.chat_id FROM session s 
                          LEFT JOIN chat_log c ON s.chat_log_id = c.chat_id 
                          WHERE JSON_CONTAINS(s.participants, :userId, '$.user_ids')
                          ORDER BY s.start_time DESC");
        $this->db->bind(':userId', json_encode($userId));
        return $this->db->resultSet();
    }
    
    /**
     * Get active sessions
     * 
     * @return array
     */
    public function getActiveSessions() {
        $this->db->query("SELECT s.*, c.chat_id FROM session s 
                          LEFT JOIN chat_log c ON s.chat_log_id = c.chat_id 
                          WHERE s.end_time IS NULL
                          ORDER BY s.start_time DESC");
        return $this->db->resultSet();
    }
    
    /**
     * Create new session
     * 
     * @param array $data Session data
     * @return int|bool Last insert ID or false
     */
    public function create($data) {
        // First create a chat log
        $this->db->query("INSERT INTO chat_log VALUES ()");
        if (!$this->db->execute()) {
            return false;
        }
        
        $chatId = $this->db->lastInsertId();
        
        // Then create the session
        $this->db->query("INSERT INTO session (chat_log_id, participants, start_time, creator_id) 
                          VALUES (:chat_log_id, :participants, :start_time, :creator_id)");
        $this->db->bind(':chat_log_id', $chatId);
        $this->db->bind(':participants', json_encode($data['participants']));
        $this->db->bind(':start_time', date('Y-m-d H:i:s'));
        $this->db->bind(':creator_id', $data['creator_id']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * End session
     * 
     * @param int $id Session ID
     * @return bool
     */
    public function endSession($id) {
        $this->db->query("UPDATE session SET end_time = :end_time WHERE session_id = :id");
        $this->db->bind(':end_time', date('Y-m-d H:i:s'));
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Add participant to session
     * 
     * @param int $sessionId Session ID
     * @param string $userId User ID
     * @return bool
     */
    public function addParticipant($sessionId, $userId) {
        // Get current participants
        $session = $this->getSessionById($sessionId);
        $participants = json_decode($session['participants'], true);
        
        // Add new participant if not already in the list
        if (!in_array($userId, $participants['user_ids'])) {
            $participants['user_ids'][] = $userId;
            
            // Update session
            $this->db->query("UPDATE session SET participants = :participants WHERE session_id = :id");
            $this->db->bind(':participants', json_encode($participants));
            $this->db->bind(':id', $sessionId);
            return $this->db->execute();
        }
        
        return true;
    }
    
    /**
     * Remove participant from session
     * 
     * @param int $sessionId Session ID
     * @param string $userId User ID
     * @return bool
     */
    public function removeParticipant($sessionId, $userId) {
        // Get current participants
        $session = $this->getSessionById($sessionId);
        $participants = json_decode($session['participants'], true);
        
        // Remove participant if in the list
        $key = array_search($userId, $participants['user_ids']);
        if ($key !== false) {
            unset($participants['user_ids'][$key]);
            $participants['user_ids'] = array_values($participants['user_ids']); // Reindex array
            
            // Update session
            $this->db->query("UPDATE session SET participants = :participants WHERE session_id = :id");
            $this->db->bind(':participants', json_encode($participants));
            $this->db->bind(':id', $sessionId);
            return $this->db->execute();
        }
        
        return true;
    }
    
    /**
     * Check if user is participant in session
     * 
     * @param int $sessionId Session ID
     * @param string $userId User ID
     * @return bool
     */
    public function isParticipant($sessionId, $userId) {
        $session = $this->getSessionById($sessionId);
        if (!$session) {
            return false;
        }
        
        $participants = json_decode($session['participants'], true);
        return in_array($userId, $participants['user_ids']);
    }
}
