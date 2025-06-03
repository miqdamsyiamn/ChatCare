<?php
/**
 * Mood Model
 * 
 * Model untuk menangani operasi terkait mood tim
 */

class MoodModel {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Get mood by session ID
     * 
     * @param int $sessionId Session ID
     * @return array
     */
    public function getMoodBySessionId($sessionId) {
        $this->db->query("SELECT * FROM team_mood WHERE session_id = :session_id");
        $this->db->bind(':session_id', $sessionId);
        return $this->db->single();
    }
    
    /**
     * Get mood history for session
     * 
     * @param int $sessionId Session ID
     * @return array
     */
    public function getMoodHistory($sessionId) {
        $this->db->query("SELECT m.*, ml.timestamp FROM team_mood m 
                          JOIN session s ON m.session_id = s.session_id 
                          JOIN message_log ml ON s.chat_log_id = ml.chat_id 
                          WHERE m.session_id = :session_id 
                          ORDER BY ml.timestamp ASC");
        $this->db->bind(':session_id', $sessionId);
        return $this->db->resultSet();
    }
    
    /**
     * Get mood data for visualization
     * 
     * @param int $sessionId Session ID
     * @return array
     */
    public function getMoodData($sessionId) {
        // Get session details
        $sessionModel = new SessionModel();
        $session = $sessionModel->getSessionById($sessionId);
        
        if (!$session) {
            return [];
        }
        
        // Get all messages for this session
        $this->db->query("SELECT ml.*, u.username FROM message_log ml 
                          JOIN user u ON ml.sender_id = u.user_id 
                          WHERE ml.chat_id = :chat_id 
                          ORDER BY ml.timestamp ASC");
        $this->db->bind(':chat_id', $session['chat_log_id']);
        $messages = $this->db->resultSet();
        
        // Prepare data for visualization
        $data = [
            'labels' => [], // Timestamps
            'datasets' => [
                [
                    'label' => 'Positif',
                    'data' => [],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Netral',
                    'data' => [],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Negatif',
                    'data' => [],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ],
            'summary' => [
                'positif' => 0,
                'netral' => 0,
                'negatif' => 0
            ]
        ];
        
        // Count emotions over time
        $positifCount = 0;
        $netralCount = 0;
        $negatifCount = 0;
        
        foreach ($messages as $message) {
            // Skip system messages
            if ($message['sender_id'] === '0') {
                continue;
            }
            
            // Update counts based on emotion
            if ($message['emotion_label'] === 'positif') {
                $positifCount++;
            } elseif ($message['emotion_label'] === 'netral') {
                $netralCount++;
            } elseif ($message['emotion_label'] === 'negatif') {
                $negatifCount++;
            }
            
            // Add data point
            $timestamp = date('H:i', strtotime($message['timestamp']));
            $data['labels'][] = $timestamp;
            $data['datasets'][0]['data'][] = $positifCount;
            $data['datasets'][1]['data'][] = $netralCount;
            $data['datasets'][2]['data'][] = $negatifCount;
        }
        
        // Update summary
        $data['summary']['positif'] = $positifCount;
        $data['summary']['netral'] = $netralCount;
        $data['summary']['negatif'] = $negatifCount;
        
        return $data;
    }
    
    /**
     * Get user mood data
     * 
     * @param int $sessionId Session ID
     * @param string $userId User ID
     * @return array
     */
    public function getUserMoodData($sessionId, $userId) {
        // Get session details
        $sessionModel = new SessionModel();
        $session = $sessionModel->getSessionById($sessionId);
        
        if (!$session) {
            return [];
        }
        
        // Get user messages for this session
        $this->db->query("SELECT * FROM message_log 
                          WHERE chat_id = :chat_id AND sender_id = :sender_id 
                          ORDER BY timestamp ASC");
        $this->db->bind(':chat_id', $session['chat_log_id']);
        $this->db->bind(':sender_id', $userId);
        $messages = $this->db->resultSet();
        
        // Count emotions
        $emotionCounts = [
            'positif' => 0,
            'netral' => 0,
            'negatif' => 0
        ];
        
        foreach ($messages as $message) {
            if (isset($emotionCounts[$message['emotion_label']])) {
                $emotionCounts[$message['emotion_label']]++;
            }
        }
        
        return $emotionCounts;
    }
}
