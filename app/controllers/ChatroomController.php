<?php
/**
 * Chatroom Controller
 * 
 * Controller untuk menangani chatroom dan pesan
 */

class ChatroomController extends Controller {
    private $sessionModel;
    private $messageModel;
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
        $this->messageModel = $this->model('Message');
        $this->moodModel = $this->model('Mood');
    }
    
    /**
     * Chatroom index page
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
        
        // Simpan ID pengguna saat ini untuk digunakan dalam view
        $currentUserId = $_SESSION['user_id'];
        
        // Log untuk debugging
        error_log("Current user ID: " . $currentUserId);
        
        // Check if session is active (not ended)
        if ($session['end_time'] !== null) {
            $_SESSION['error_message'] = 'Diskusi ini telah berakhir';
            $this->redirect('/dashboard');
        }
        
        // Get session details
        $participants = json_decode($session['participants'], true);
        $sessionTitle = $participants['title'];
        $sessionDescription = $participants['description'];
        
        // Get recent messages
        $allMessages = $this->messageModel->getRecentMessagesByChatId($session['chat_log_id']);
        
        // Pisahkan pesan dari pengguna yang sedang login dan pesan dari pengguna lain
        $selfMessages = [];
        $otherMessages = [];
        $processedIds = [];
        
        foreach ($allMessages as $message) {
            // Pastikan pesan dengan ID yang sama tidak diproses dua kali
            if (in_array($message['message_id'], $processedIds)) {
                continue;
            }
            
            // Tandai pesan ini sudah diproses
            $processedIds[] = $message['message_id'];
            
            if ($message['sender_id'] == $_SESSION['user_id']) {
                // Pesan dari pengguna yang sedang login - HANYA masukkan ke selfMessages
                $selfMessages[] = $message;
            } else {
                // Pesan dari pengguna lain atau sistem - HANYA masukkan ke otherMessages
                $otherMessages[] = $message;
            }
        }
        
        // Pastikan pesan dari pengguna yang sedang login TIDAK termasuk dalam messages
        $messages = $otherMessages;
        
        // Get mood data
        $moodData = $this->moodModel->getMoodBySessionId($sessionId);
        
        $data = [
            'title' => 'Chatroom: ' . $sessionTitle,
            'session' => $session,
            'session_title' => $sessionTitle,
            'session_description' => $sessionDescription,
            'messages' => $messages,
            'self_messages' => $selfMessages,  // Pesan dari pengguna yang sedang login
            'other_messages' => $otherMessages,  // Pesan dari pengguna lain
            'mood_data' => $moodData,
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'current_user_id' => $_SESSION['user_id']  // ID pengguna saat ini
        ];
        
        $this->view('chatroom/index', $data);
    }
    
    /**
     * Send message
     * 
     * @return void
     */
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Log semua data POST untuk debugging
            error_log("POST Data: " . print_r($_POST, true));
            error_log("Content Type: " . $_SERVER['CONTENT_TYPE']);
            
            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // Get POST data
                $sessionId = isset($_POST['session_id']) ? $_POST['session_id'] : null;
                $message = isset($_POST['message']) ? $_POST['message'] : null;
                
                // Log untuk debugging
                error_log("AJAX Request - Session ID: {$sessionId}, Message: {$message}");
                
                // Jika data tidak ada di $_POST, coba ambil dari input stream (untuk JSON requests)
                if (empty($sessionId) || empty($message)) {
                    $inputJSON = file_get_contents('php://input');
                    $input = json_decode($inputJSON, true);
                    
                    if ($input && is_array($input)) {
                        $sessionId = $input['session_id'] ?? null;
                        $message = $input['message'] ?? null;
                        error_log("JSON Input - Session ID: {$sessionId}, Message: {$message}");
                    }
                }
                
                // Validate data
                if (empty($sessionId) || empty($message)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Data tidak lengkap. Session ID: ' . ($sessionId ? 'Ada' : 'Kosong') . ', Message: ' . ($message ? 'Ada' : 'Kosong')], 400);
                }
                
                // Get session
                $session = $this->sessionModel->getSessionById($sessionId);
                if (!$session) {
                    $this->jsonResponse(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);
                }
                
                // Check if user is participant
                if (!$this->sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
                    $this->jsonResponse(['success' => false, 'message' => 'Anda bukan peserta diskusi ini'], 403);
                }
                
                // Check if session is active
                if ($session['end_time'] !== null) {
                    $this->jsonResponse(['success' => false, 'message' => 'Diskusi telah berakhir'], 403);
                }
                
                // Create message - handle both array and object access
                $chatId = is_array($session) ? $session['chat_log_id'] : $session->chat_log_id;
                
                $messageData = [
                    'chat_id' => $chatId,
                    'sender_id' => $_SESSION['user_id'],
                    'text' => $message
                ];
                
                // Log message data before creating
                error_log("Attempting to create message with data: " . json_encode($messageData));
                error_log("Current user ID: " . $_SESSION['user_id']);
                error_log("Using chat_id: " . $chatId);
                
                // Verify user exists before creating message
                $this->db = new Database();
                $this->db->query("SELECT user_id FROM user WHERE user_id = :user_id");
                $this->db->bind(':user_id', $_SESSION['user_id']);
                $user = $this->db->single();
                
                if (!$user) {
                    error_log("ERROR: User ID {$_SESSION['user_id']} does not exist in database");
                    $this->jsonResponse(['success' => false, 'message' => 'User tidak valid'], 400);
                    return;
                }
                
                try {
                    // First check if this exact message already exists (to prevent duplicates)
                    $this->db = new Database();
                    $this->db->query("SELECT message_id FROM message_log 
                                      WHERE chat_id = :chat_id AND sender_id = :sender_id AND text = :text
                                      ORDER BY timestamp DESC LIMIT 1");
                    $this->db->bind(':chat_id', $messageData['chat_id']);
                    $this->db->bind(':sender_id', $messageData['sender_id']);
                    $this->db->bind(':text', $messageData['text']);
                    $existingMessage = $this->db->single();
                    
                    if ($existingMessage) {
                        // Message already exists, return it as success
                        error_log("Message already exists with ID: {$existingMessage->message_id}, returning as success");
                        
                        // Get the full message details
                        $this->db = new Database();
                        $this->db->query("SELECT m.*, u.username FROM message_log m 
                                          LEFT JOIN user u ON m.sender_id = u.user_id 
                                          WHERE m.message_id = :message_id");
                        $this->db->bind(':message_id', $existingMessage->message_id);
                        $newMessage = $this->db->single();
                        
                        // Convert to array if it's an object for consistent response
                        if (is_object($newMessage)) {
                            $newMessage = (array)$newMessage;
                        }
                        
                        $this->jsonResponse([
                            'success' => true, 
                            'message' => 'Pesan berhasil dikirim',
                            'data' => $newMessage
                        ]);
                        return;
                    }
                    
                    // Create the message if it doesn't exist
                    $messageId = $this->messageModel->create($messageData);
                    
                    if ($messageId) {
                        // Log success
                        error_log("Message created successfully with ID: {$messageId}");
                        
                        // Get the created message with emotion
                        $this->db = new Database();
                        $this->db->query("SELECT m.*, u.username FROM message_log m 
                                          LEFT JOIN user u ON m.sender_id = u.user_id 
                                          WHERE m.message_id = :message_id");
                        $this->db->bind(':message_id', $messageId);
                        $newMessage = $this->db->single();
                        
                        // Convert to array if it's an object for consistent response
                        if (is_object($newMessage)) {
                            $newMessage = (array)$newMessage;
                        }
                        
                        $this->jsonResponse([
                            'success' => true, 
                            'message' => 'Pesan berhasil dikirim',
                            'data' => $newMessage
                        ]);
                    } else {
                        // Coba cari pesan yang mungkin sudah dibuat meskipun ada error
                    try {
                        $this->db = new Database();
                        $this->db->query("SELECT m.*, u.username FROM message_log m 
                                          LEFT JOIN user u ON m.sender_id = u.user_id 
                                          WHERE m.chat_id = :chat_id AND m.sender_id = :sender_id AND m.text = :text
                                          ORDER BY m.timestamp DESC LIMIT 1");
                        $this->db->bind(':chat_id', $messageData['chat_id']);
                        $this->db->bind(':sender_id', $messageData['sender_id']);
                        $this->db->bind(':text', $messageData['text']);
                        $existingMessage = $this->db->single();
                        
                        if ($existingMessage) {
                            // Pesan sebenarnya sudah dibuat
                            error_log("Message found despite reported failure: " . json_encode($existingMessage));
                            
                            // Convert to array if it's an object for consistent response
                            if (is_object($existingMessage)) {
                                $existingMessage = (array)$existingMessage;
                            }
                            
                            $this->jsonResponse([
                                'success' => true, 
                                'message' => 'Pesan berhasil dikirim',
                                'data' => $existingMessage
                            ]);
                            return;
                        }
                    } catch (Exception $innerEx) {
                        error_log("Failed to check for existing message: " . $innerEx->getMessage());
                    }
                    
                    $this->jsonResponse(['success' => false, 'message' => 'Gagal mengirim pesan'], 500);
                    }
                } catch (Exception $e) {
                    // Log exception details
                    error_log("Exception in message creation: " . $e->getMessage());
                    
                    // Try to get the message that might have been created despite the error
                    try {
                        $this->db = new Database();
                        $this->db->query("SELECT m.*, u.username FROM message_log m 
                                          LEFT JOIN user u ON m.sender_id = u.user_id 
                                          WHERE m.chat_id = :chat_id AND m.sender_id = :sender_id AND m.text = :text
                                          ORDER BY m.timestamp DESC LIMIT 1");
                        $this->db->bind(':chat_id', $messageData['chat_id']);
                        $this->db->bind(':sender_id', $messageData['sender_id']);
                        $this->db->bind(':text', $messageData['text']);
                        $newMessage = $this->db->single();
                        
                        if ($newMessage) {
                            // Message was actually created despite the exception
                            error_log("Message found despite exception: " . json_encode($newMessage));
                            
                            // Convert to array if it's an object for consistent response
                            if (is_object($newMessage)) {
                                $newMessage = (array)$newMessage;
                            }
                            
                            $this->jsonResponse([
                                'success' => true, 
                                'message' => 'Pesan berhasil dikirim',
                                'data' => $newMessage
                            ]);
                            return;
                        }
                    } catch (Exception $innerEx) {
                        error_log("Failed to check for existing message: " . $innerEx->getMessage());
                    }
                    
                    // Jika masih gagal, kembalikan error dengan data kosong tapi valid
                    $this->jsonResponse([
                        'success' => false, 
                        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                        'data' => [
                            'message_id' => 0,
                            'chat_id' => $messageData['chat_id'],
                            'sender_id' => $messageData['sender_id'],
                            'text' => $messageData['text'],
                            'timestamp' => date('Y-m-d H:i:s'),
                            'emotion_label' => 'netral',
                            'source_label' => 'error'
                        ]
                    ], 500);
                }
            } else {
                // Regular form submission
                $sessionId = $_POST['session_id'];
                $message = $_POST['message'];
                
                // Validate data
                if (empty($sessionId) || empty($message)) {
                    $_SESSION['error_message'] = 'Data tidak lengkap';
                    $this->redirect('/chatroom?session_id=' . $sessionId);
                }
                
                // Get session
                $session = $this->sessionModel->getSessionById($sessionId);
                if (!$session) {
                    $this->redirect('/dashboard');
                }
                
                // Check if user is participant
                if (!$this->sessionModel->isParticipant($sessionId, $_SESSION['user_id'])) {
                    $this->redirect('/dashboard');
                }
                
                // Check if session is active
                if ($session['end_time'] !== null) {
                    $_SESSION['error_message'] = 'Diskusi telah berakhir';
                    $this->redirect('/dashboard');
                }
                
                // Create message - handle both array and object access
                $chatId = is_array($session) ? $session['chat_log_id'] : $session->chat_log_id;
                
                $messageData = [
                    'chat_id' => $chatId,
                    'sender_id' => $_SESSION['user_id'],
                    'text' => $message
                ];
                
                // Log message data before creating
                error_log("Regular form: Attempting to create message with data: " . json_encode($messageData));
                
                try {
                    $messageId = $this->messageModel->create($messageData);
                    
                    if ($messageId) {
                        // Redirect back to chatroom
                        $this->redirect('/chatroom?session_id=' . $sessionId);
                    } else {
                        $_SESSION['error_message'] = 'Gagal mengirim pesan';
                        $this->redirect('/chatroom?session_id=' . $sessionId);
                    }
                } catch (Exception $e) {
                    // Log exception details
                    error_log("Regular form: Exception in message creation: " . $e->getMessage());
                    
                    // Check if message was created despite the exception
                    $this->db = new Database();
                    $this->db->query("SELECT message_id FROM message_log 
                                      WHERE chat_id = :chat_id AND sender_id = :sender_id AND text = :text
                                      ORDER BY timestamp DESC LIMIT 1");
                    $this->db->bind(':chat_id', $messageData['chat_id']);
                    $this->db->bind(':sender_id', $messageData['sender_id']);
                    $this->db->bind(':text', $messageData['text']);
                    $existingMessage = $this->db->single();
                    
                    if ($existingMessage) {
                        // Message was created despite the exception
                        $this->redirect('/chatroom?session_id=' . $sessionId);
                    } else {
                        $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
                        $this->redirect('/chatroom?session_id=' . $sessionId);
                    }
                }
            }
        } else {
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Get messages
     * 
     * @return void
     */
    public function messages() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // Check if AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $sessionId = isset($_GET['session_id']) ? $_GET['session_id'] : null;
                $lastTimestamp = isset($_GET['last_timestamp']) ? $_GET['last_timestamp'] : null;
                
                if (empty($sessionId)) {
                    $this->jsonResponse(['success' => false, 'message' => 'Session ID tidak ditemukan'], 400);
                }
                
                // Get session
                $session = $this->sessionModel->getSessionById($sessionId);
                if (!$session) {
                    $this->jsonResponse(['success' => false, 'message' => 'Sesi tidak ditemukan'], 404);
                }
                
                // Log untuk debugging
                error_log("Fetching messages for chat_id: {$session['chat_log_id']}, after timestamp: {$lastTimestamp}");
                
                // Get messages
                $allMessages = $this->messageModel->getNewMessagesByChatId($session['chat_log_id'], $lastTimestamp);
                
                // Log jumlah pesan yang ditemukan
                error_log("Found " . count($allMessages) . " messages after timestamp");
                
                // Gunakan array asosiatif untuk mencegah duplikasi berdasarkan message_id
                $uniqueMessages = [];
                
                foreach ($allMessages as $message) {
                    $messageId = $message['message_id'];
                    
                    // Hanya tambahkan pesan jika belum ada di array uniqueMessages
                    if (!isset($uniqueMessages[$messageId])) {
                        // Tambahkan informasi tambahan untuk setiap pesan
                        $message['is_self'] = ($message['sender_id'] == $_SESSION['user_id']);
                        $message['is_system'] = ($message['sender_id'] == '0');
                        $message['position'] = $message['is_self'] ? 'right' : ($message['is_system'] ? 'center' : 'left');
                        
                        // Tambahkan ke array uniqueMessages dengan message_id sebagai key
                        $uniqueMessages[$messageId] = $message;
                    }
                }
                
                // Konversi kembali ke array numerik untuk JSON response
                $messages = array_values($uniqueMessages);
                
                // Urutkan pesan berdasarkan timestamp
                usort($messages, function($a, $b) {
                    return strtotime($a['timestamp']) - strtotime($b['timestamp']);
                });
                
                // Get mood data
                $moodData = $this->moodModel->getMoodBySessionId($sessionId);
                
                $this->jsonResponse([
                    'success' => true,
                    'data' => [
                        'messages' => $messages,
                        'mood_data' => $moodData,
                        'current_user_id' => $_SESSION['user_id'], // Tambahkan user_id saat ini
                        'timestamp' => date('Y-m-d H:i:s') // Tambahkan timestamp saat ini untuk debugging
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
