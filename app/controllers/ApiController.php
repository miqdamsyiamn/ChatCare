<?php
/**
 * API Controller
 * 
 * Controller untuk menangani API requests
 */

class ApiController extends Controller {
    private $messageModel;
    private $moodModel;
    private $geminiService;
    
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
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
            exit;
        }
        
        $this->messageModel = $this->model('Message');
        $this->moodModel = $this->model('Mood');
        
        // Initialize Gemini service
        require_once BASE_PATH . '/app/core/GeminiService.php';
        $this->geminiService = new GeminiService();
    }
    
    /**
     * Get messages for a chat session
     * 
     * @return void
     */
    public function getMessages() {
        // Check if request is AJAX
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        // Get session ID from query string
        $sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : null;
        $lastTimestamp = isset($_GET['last_timestamp']) ? $_GET['last_timestamp'] : null;
        
        if (!$sessionId) {
            $this->jsonResponse(['success' => false, 'message' => 'Session ID is required'], 400);
        }
        
        // Get session
        $sessionModel = $this->model('Session');
        $session = $sessionModel->getSessionById($sessionId);
        
        if (!$session) {
            $this->jsonResponse(['success' => false, 'message' => 'Session not found'], 404);
        }
        
        // Check if user is participant
        if (!$sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'You are not a participant of this discussion'], 403);
        }
        
        // Get messages
        $messages = $this->messageModel->getNewMessagesByChatId($session['chat_log_id'], $lastTimestamp);
        
        $this->jsonResponse(['success' => true, 'data' => $messages]);
    }
    
    /**
     * Send message
     * 
     * @return void
     */
    public function sendMessage() {
        // Check if request is AJAX
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        // Get POST data
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);
        
        $sessionId = $input['session_id'] ?? null;
        $message = $input['message'] ?? null;
        
        if (!$sessionId || !$message) {
            $this->jsonResponse(['success' => false, 'message' => 'Session ID and message are required'], 400);
        }
        
        // Get session
        $sessionModel = $this->model('Session');
        $session = $sessionModel->getSessionById($sessionId);
        
        if (!$session) {
            $this->jsonResponse(['success' => false, 'message' => 'Session not found'], 404);
        }
        
        // Check if user is participant
        if (!$sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'You are not a participant of this discussion'], 403);
        }
        
        // Create message
        $messageData = [
            'chat_id' => $session['chat_log_id'],
            'sender_id' => $_SESSION['user_id'],
            'text' => $message
        ];
        
        $messageId = $this->messageModel->create($messageData);
        
        if ($messageId) {
            // Get the created message with emotion
            $this->db = new Database();
            $this->db->query("SELECT m.*, u.username FROM message_log m 
                              LEFT JOIN user u ON m.sender_id = u.user_id 
                              WHERE m.message_id = :message_id");
            $this->db->bind(':message_id', $messageId);
            $newMessage = $this->db->single();
            
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Message sent successfully',
                'data' => $newMessage
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to send message'], 500);
        }
    }
    
    /**
     * Get mood data for a session
     * 
     * @return void
     */
    public function getMoodData() {
        // Check if request is AJAX
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        // Get session ID from query string
        $sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : null;
        
        if (!$sessionId) {
            $this->jsonResponse(['success' => false, 'message' => 'Session ID is required'], 400);
        }
        
        // Get mood data
        $moodData = $this->moodModel->getMoodBySessionId($sessionId);
        
        $this->jsonResponse(['success' => true, 'data' => $moodData]);
    }
    
    /**
     * Generate feedback using Gemini API
     * 
     * @return void
     */
    public function generateFeedback() {
        // Check if request is AJAX
        if (!$this->isAjaxRequest()) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }
        
        // Get POST data
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);
        
        $prompt = $input['prompt'] ?? '';
        $emotionType = $input['emotion_type'] ?? '';
        
        if (empty($prompt)) {
            $this->jsonResponse(['success' => false, 'message' => 'Prompt is required'], 400);
        }
        
        try {
            // Generate feedback using Gemini API based on emotion type
            $feedback = $this->geminiService->generateFeedback($emotionType);
            
            // Log the feedback for debugging
            error_log("Generated feedback for {$emotionType}: " . $feedback);
            
            // If feedback is empty, use fallback
            if (empty($feedback)) {
                if ($emotionType === 'positif') {
                    $feedback = 'Keren! Semangat positifmu membuat diskusi ini semakin hidup dan produktif! 🌟';
                } else {
                    $feedback = 'Yuk, coba ekspresikan pendapatmu dengan cara yang lebih positif untuk membuat diskusi ini lebih menyenangkan! 💪';
                }
            }
            
            $this->jsonResponse([
                'success' => true,
                'feedback' => $feedback
            ]);
        } catch (Exception $e) {
            error_log('Error generating feedback: ' . $e->getMessage());
            
            // Use fallback feedback
            if ($emotionType === 'positif') {
                $feedback = 'Keren! Semangat positifmu membuat diskusi ini semakin hidup dan produktif! 🌟';
            } else {
                $feedback = 'Yuk, coba ekspresikan pendapatmu dengan cara yang lebih positif untuk membuat diskusi ini lebih menyenangkan! 💪';
            }
            
            $this->jsonResponse([
                'success' => true,
                'feedback' => $feedback
            ]);
        }
    }
    
    /**
     * Check if request is AJAX
     * 
     * @return bool
     */
    private function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
