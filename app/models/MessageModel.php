<?php
/**
 * Message Model
 * 
 * Model untuk menangani operasi terkait pesan dalam diskusi
 */

class MessageModel {
    private $db;
    private $geminiService;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
        
        // Make sure GeminiService is loaded properly
        if (!class_exists('GeminiService')) {
            require_once BASE_PATH . '/app/core/GeminiService.php';
        }
        
        $this->geminiService = new GeminiService();
    }
    
    /**
     * Get messages by chat ID
     * 
     * @param int $chatId Chat ID
     * @return array
     */
    public function getMessagesByChatId($chatId) {
        $this->db->query("SELECT m.*, u.username FROM message_log m 
                          LEFT JOIN user u ON m.sender_id = u.user_id 
                          WHERE m.chat_id = :chat_id 
                          ORDER BY m.timestamp ASC");
        $this->db->bind(':chat_id', $chatId);
        return $this->db->resultSet();
    }
    
    /**
     * Get recent messages by chat ID
     * 
     * @param int $chatId Chat ID
     * @param int $limit Number of messages to retrieve
     * @return array
     */
    public function getRecentMessagesByChatId($chatId, $limit = 50) {
        // Langsung ambil dalam urutan kronologis (ASC) untuk menampilkan dari lama ke baru
        $this->db->query("SELECT m.*, u.username FROM message_log m 
                          LEFT JOIN user u ON m.sender_id = u.user_id 
                          WHERE m.chat_id = :chat_id 
                          ORDER BY m.timestamp ASC 
                          LIMIT :limit");
        $this->db->bind(':chat_id', $chatId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
    /**
     * Get messages after timestamp
     * 
     * @param int $chatId Chat ID
     * @param string $timestamp Timestamp
     * @return array
     */
    public function getMessagesAfterTimestamp($chatId, $timestamp) {
        $this->db->query("SELECT m.*, u.username FROM message_log m 
                          LEFT JOIN user u ON m.sender_id = u.user_id 
                          WHERE m.chat_id = :chat_id AND m.timestamp > :timestamp 
                          ORDER BY m.timestamp ASC");
        $this->db->bind(':chat_id', $chatId);
        $this->db->bind(':timestamp', $timestamp);
        return $this->db->resultSet();
    }
    
    /**
     * Get new messages by chat ID (untuk polling AJAX)
     * 
     * @param int $chatId Chat ID
     * @param string $lastTimestamp Last timestamp
     * @return array
     */
    public function getNewMessagesByChatId($chatId, $lastTimestamp = null) {
        if ($lastTimestamp) {
            // Jika ada lastTimestamp, ambil pesan setelah timestamp tersebut
            // Pastikan hanya mengambil pesan yang benar-benar baru (strictly after timestamp)
            $this->db->query("SELECT m.*, u.username, 
                          CASE WHEN m.sender_id = :current_user_id THEN 'self' 
                               WHEN m.sender_id = 0 THEN 'system' 
                               ELSE 'other' END AS sender_type 
                          FROM message_log m 
                          LEFT JOIN user u ON m.sender_id = u.user_id 
                          WHERE m.chat_id = :chat_id AND m.timestamp > :timestamp 
                          ORDER BY m.timestamp ASC");
            $this->db->bind(':chat_id', $chatId);
            $this->db->bind(':timestamp', $lastTimestamp);
            $this->db->bind(':current_user_id', $_SESSION['user_id']);
            return $this->db->resultSet();
        } else {
            // Jika tidak ada lastTimestamp, ambil pesan terbaru
            return $this->getRecentMessagesByChatId($chatId);
        }
    }
    
    /**
     * Create new message
     * 
     * @param array $data Message data
     * @return int|bool Last insert ID or false
     */
    public function create($data) {
        try {
            // Validate required data
            if (empty($data['chat_id']) || empty($data['sender_id']) || !isset($data['text'])) {
                error_log("Missing required data for message creation");
                return false;
            }
            
            // Verify chat_id exists in chat_log table
            $this->db->query("SELECT chat_id FROM chat_log WHERE chat_id = :chat_id");
            $this->db->bind(':chat_id', $data['chat_id']);
            $chat = $this->db->single();
            
            if (!$chat) {
                error_log("Invalid chat_id: {$data['chat_id']} - Chat does not exist");
                return false;
            }
            
            // Verify sender_id exists in user table
            $this->db->query("SELECT user_id FROM user WHERE user_id = :user_id");
            $this->db->bind(':user_id', $data['sender_id']);
            $user = $this->db->single();
            
            if (!$user) {
                error_log("Invalid sender_id: {$data['sender_id']} - User does not exist");
                return false;
            }
            
            // Detect emotion using hybrid approach
            $emotionLabel = $this->detectEmotion($data['text']);
            
            // Log untuk debugging
            error_log("Creating message with emotion: " . $emotionLabel['label'] . ", source: " . $emotionLabel['source']);
            
            // Query yang sudah disesuaikan dengan kolom source_label
            $this->db->query("INSERT INTO message_log (chat_id, sender_id, text, timestamp, emotion_label, source_label) 
                              VALUES (:chat_id, :sender_id, :text, :timestamp, :emotion_label, :source_label)");
            $this->db->bind(':chat_id', $data['chat_id']);
            $this->db->bind(':sender_id', $data['sender_id']);
            $this->db->bind(':text', $data['text']);
            $this->db->bind(':timestamp', date('Y-m-d H:i:s'));
            $this->db->bind(':emotion_label', $emotionLabel['label']);
            $this->db->bind(':source_label', $emotionLabel['source']);
            
            if ($this->db->execute()) {
                $messageId = $this->db->lastInsertId();
                
                // Check if feedback is needed
                $this->checkAndCreateFeedback($data['chat_id']);
                
                // Update team mood
                $this->updateTeamMood($data['chat_id']);
                
                return $messageId;
            } else {
                error_log("Database execution failed when inserting message");
                return false;
            }
        } catch (PDOException $e) {
            // Log detailed database error
            error_log('Database error creating message: ' . $e->getMessage());
            error_log('SQL State: ' . $e->getCode());
            return false;
        } catch (Exception $e) {
            // Log general error
            error_log('Error creating message: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Detect emotion from message text
     * 
     * @param string $text Message text
     * @return array Emotion data with label and source
     */
    private function detectEmotion($text) {
        try {
            $normalizedText = strtolower(trim($text));
            error_log("Analyzing text: {$normalizedText}");
            
            // Daftar kata-kata positif
            $positiveWords = [
                'bagus', 'baik', 'hebat', 'keren', 'mantap', 'wow', 'luar biasa', 
                'senang', 'gembira', 'suka', 'cinta', 'sayang', 'bahagia', 'sukses', 
                'berhasil', 'setuju', 'benar', 'betul', 'oke', 'ok', 'ya', 'yes', 
                'positif', 'menang', 'untung', 'semangat', 'termotivasi', 'terinspirasi',
                'makasih', 'terima kasih', 'thanks', 'good', 'nice', 'great', 'awesome'
            ];
            
            // Daftar kata-kata negatif
            $negativeWords = [
                'buruk', 'jelek', 'parah', 'payah', 'bodoh', 'tolol', 'goblok', 
                'sedih', 'kecewa', 'marah', 'kesal', 'benci', 'bosan', 'gagal', 'malas',
                'tidak setuju', 'salah', 'tidak', 'no', 'nope', 'bukan', 
                'negatif', 'kalah', 'rugi', 'salah', 'keliru', 'error', 'masalah', 'problem', 'susah',
                'bad', 'poor', 'terrible', 'awful', 'sorry'
            ];
            
            // Cek kata positif dalam teks
            foreach ($positiveWords as $word) {
                // Simpler approach - just check if the word is in the text
                // This works better for Indonesian words that may have affixes
                if (stripos($normalizedText, $word) !== false) {
                    // Verify it's a whole word by checking boundaries
                    $wordLen = strlen($word);
                    $pos = stripos($normalizedText, $word);
                    $leftBoundary = ($pos === 0 || $normalizedText[$pos-1] === ' ');
                    $rightBoundary = ($pos + $wordLen === strlen($normalizedText) || $normalizedText[$pos + $wordLen] === ' ');
                    
                    if ($leftBoundary && $rightBoundary) {
                        error_log("Positive word found: {$word}");
                        return [
                            'label' => 'positif',
                            'source' => 'heuristik'
                        ];
                    }
                }
            }
            
            // Cek kata negatif dalam teks
            foreach ($negativeWords as $word) {
                // Simpler approach - just check if the word is in the text
                // This works better for Indonesian words that may have affixes
                if (stripos($normalizedText, $word) !== false) {
                    // Verify it's a whole word by checking boundaries
                    $wordLen = strlen($word);
                    $pos = stripos($normalizedText, $word);
                    $leftBoundary = ($pos === 0 || $normalizedText[$pos-1] === ' ');
                    $rightBoundary = ($pos + $wordLen === strlen($normalizedText) || $normalizedText[$pos + $wordLen] === ' ');
                    
                    if ($leftBoundary && $rightBoundary) {
                        error_log("Negative word found: {$word}");
                        return [
                            'label' => 'negatif',
                            'source' => 'heuristik'
                        ];
                    }
                }
            }
            
            // Langkah 1: Cari exact match di dataset_emosi
            $this->db->query("SELECT label FROM dataset_emosi WHERE LOWER(text) = :text LIMIT 1");
            $this->db->bind(':text', $normalizedText);
            $result = $this->db->single();
            
            if ($result) {
                error_log("Found exact match in dataset_emosi: {$result->label}");
                return [
                    'label' => $result->label,
                    'source' => 'database'
                ];
            }
            
            // Langkah 2: Cari exact match di message_log
            $this->db->query("SELECT emotion_label FROM message_log WHERE LOWER(text) = :text AND emotion_label IS NOT NULL AND source_label = 'database' LIMIT 1");
            $this->db->bind(':text', $normalizedText);
            $result = $this->db->single();
            
            if ($result && !empty($result->emotion_label)) {
                error_log("Found exact match in message_log: {$result->emotion_label}");
                return [
                    'label' => $result->emotion_label,
                    'source' => 'database'
                ];
            }
            
            // Langkah 3: Cari kata yang mengandung teks di dataset_emosi
            $this->db->query("SELECT label FROM dataset_emosi WHERE LOWER(text) LIKE :text LIMIT 1");
            $this->db->bind(':text', '%' . $normalizedText . '%');
            $result = $this->db->single();
            
            if ($result) {
                error_log("Found partial match in dataset_emosi: {$result->label}");
                return [
                    'label' => $result->label,
                    'source' => 'database'
                ];
            }
            
            // Langkah 4: Cari kata yang mengandung teks di message_log
            $this->db->query("SELECT emotion_label FROM message_log WHERE LOWER(text) LIKE :text AND emotion_label IS NOT NULL AND source_label = 'database' LIMIT 1");
            $this->db->bind(':text', '%' . $normalizedText . '%');
            $result = $this->db->single();
            
            if ($result && !empty($result->emotion_label)) {
                error_log("Found partial match in message_log: {$result->emotion_label}");
                return [
                    'label' => $result->emotion_label,
                    'source' => 'database'
                ];
            }
            
            // Langkah 5: Cari teks yang mengandung kata di dataset_emosi
            $this->db->query("SELECT label FROM dataset_emosi WHERE :text LIKE CONCAT('%', LOWER(text), '%') LIMIT 1");
            $this->db->bind(':text', $normalizedText);
            $result = $this->db->single();
            
            if ($result) {
                error_log("Text contains a word from dataset_emosi: {$result->label}");
                return [
                    'label' => $result->label,
                    'source' => 'database'
                ];
            }
            
            // Langkah 6: Cari teks yang mengandung kata di message_log
            $this->db->query("SELECT emotion_label FROM message_log WHERE :text LIKE CONCAT('%', LOWER(text), '%') AND emotion_label IS NOT NULL AND source_label = 'database' LIMIT 1");
            $this->db->bind(':text', $normalizedText);
            $result = $this->db->single();
            
            if ($result && !empty($result->emotion_label)) {
                error_log("Text contains a word from message_log: {$result->emotion_label}");
                return [
                    'label' => $result->emotion_label,
                    'source' => 'database'
                ];
            }
            
            // Jika tidak ada di database, gunakan Gemini API
            try {
                error_log("Text not found in database, using Gemini API");
                $label = $this->geminiService->analyzeEmotion($normalizedText);
                
                if (!empty($label)) {
                    error_log("Gemini API result: {$label}");
                    
                    // Simpan hasil ke database message_log untuk penggunaan di masa depan
                    // Note: Ini tidak menyimpan ke message_log secara langsung karena itu akan dilakukan
                    // oleh method saveMessage. Kita hanya mengembalikan hasil untuk digunakan.
                    
                    return [
                        'label' => $label,
                        'source' => 'gemini'
                    ];
                } else {
                    error_log("Gemini API returned empty result");
                    return [
                        'label' => 'netral',
                        'source' => 'gemini'
                    ];
                }
            } catch (Exception $e) {
                error_log("Error calling Gemini API: " . $e->getMessage());
                // Fallback ke netral jika terjadi error
                return [
                    'label' => 'netral',
                    'source' => 'gemini'
                ];
            }
        } catch (Exception $e) {
            error_log('Error in emotion detection: ' . $e->getMessage());
            return [
                'label' => 'netral',
                'source' => 'gemini'
            ];
        }
    }
    
    /**
     * Check if feedback is needed and create feedback message
     * 
     * @param int $chatId Chat ID
     * @return void
     */
    private function checkAndCreateFeedback($chatId) {
        // Check if we've recently sent feedback (within the last 5 minutes)
        $this->db->query("SELECT COUNT(*) as count FROM message_log 
                          WHERE chat_id = :chat_id 
                          AND sender_id = '0' 
                          AND source_label = 'gemini-feedback' 
                          AND timestamp > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
        $this->db->bind(':chat_id', $chatId);
        $recentFeedback = $this->db->single();
        
        // If we've sent feedback recently, don't send more
        if ($recentFeedback && $recentFeedback['count'] > 0) {
            error_log("Skipping feedback - cooldown period active");
            return;
        }
        
        // Get last 3 messages from different senders (not system messages)
        $this->db->query("SELECT emotion_label FROM message_log 
                          WHERE chat_id = :chat_id 
                          AND sender_id != '0' 
                          ORDER BY timestamp DESC 
                          LIMIT 3");
        $this->db->bind(':chat_id', $chatId);
        $messages = $this->db->resultSet();
        
        // Check if all 3 messages are negative
        if (count($messages) == 3) {
            $allNegative = true;
            foreach ($messages as $message) {
                if ($message['emotion_label'] != 'negatif') {
                    $allNegative = false;
                    break;
                }
            }
            
            // If all negative, generate feedback
            if ($allNegative) {
                error_log("Generating feedback for 3 consecutive negative messages");
                $feedback = $this->geminiService->generateFeedback();
                
                // Create feedback message from system
                $this->db->query("INSERT INTO message_log (chat_id, sender_id, text, timestamp, emotion_label, source_label) 
                                  VALUES (:chat_id, '0', :text, :timestamp, 'positif', 'gemini-feedback')");
                $this->db->bind(':chat_id', $chatId);
                $this->db->bind(':text', $feedback);
                $this->db->bind(':timestamp', date('Y-m-d H:i:s'));
                $this->db->execute();
                
                error_log("Feedback message created successfully");
            }
        }
    }
    
    /**
     * Update team mood based on recent messages
     * 
     * @param int $chatId Chat ID
     * @return void
     */
    private function updateTeamMood($chatId) {
        // Get session ID for this chat
        $this->db->query("SELECT session_id FROM session WHERE chat_log_id = :chat_id");
        $this->db->bind(':chat_id', $chatId);
        $session = $this->db->single();
        
        if (!$session) {
            return;
        }
        
        $sessionId = $session['session_id'];
        
        // Get all messages for this chat
        $this->db->query("SELECT emotion_label FROM message_log WHERE chat_id = :chat_id");
        $this->db->bind(':chat_id', $chatId);
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
        
        // Determine team mood
        $teamMood = 'netral';
        if ($emotionCounts['positif'] > $emotionCounts['negatif'] && $emotionCounts['positif'] > $emotionCounts['netral']) {
            $teamMood = 'positif';
        } elseif ($emotionCounts['negatif'] > $emotionCounts['positif'] && $emotionCounts['negatif'] > $emotionCounts['netral']) {
            $teamMood = 'negatif';
        }
        
        // Check if team_mood entry exists
        $this->db->query("SELECT mood_id FROM team_mood WHERE session_id = :session_id");
        $this->db->bind(':session_id', $sessionId);
        $existingMood = $this->db->single();
        
        $emotionSummary = json_encode($emotionCounts);
        
        if ($existingMood) {
            // Update existing mood
            $this->db->query("UPDATE team_mood SET team_mood = :team_mood, emotion_summary = :emotion_summary WHERE session_id = :session_id");
        } else {
            // Create new mood entry
            $this->db->query("INSERT INTO team_mood (session_id, team_mood, emotion_summary) VALUES (:session_id, :team_mood, :emotion_summary)");
        }
        
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':team_mood', $teamMood);
        $this->db->bind(':emotion_summary', $emotionSummary);
        $this->db->execute();
    }
}
