<?php
/**
 * GeminiService Class
 * 
 * Kelas untuk menangani interaksi dengan Google Gemini API
 */

class GeminiService {
    private $apiKey;
    private $endpoint;
    private $model;
    
    /**
     * Constructor
     */
    public function __construct() {
        // Load Gemini configuration
        $config = require BASE_PATH . '/config/gemini.php';
        
        $this->apiKey = $config['api_key'];
        $this->endpoint = $config['endpoint'];
        $this->model = $config['model'];
    }
    
    /**
     * Analyze emotion from text
     * 
     * @param string $text Text to analyze
     * @return string Emotion label (positif, negatif, netral)
     */
    public function analyzeEmotion($text) {
        // Log penggunaan API untuk debugging
        error_log("Analyzing emotion for text: " . substr($text, 0, 50) . (strlen($text) > 50 ? '...' : ''));
        
        // Simplified prompt that works with the API
        $prompt = "Klasifikasikan emosi dari teks berikut sebagai positif, negatif, atau netral (jawab hanya dengan satu kata): '{$text}'";
        
        $response = $this->generateContent($prompt, 200); // Short response needed
        
        // Log respons API untuk debugging
        error_log("Gemini API response: " . $response);
        
        if (empty($response)) {
            error_log("Empty response from Gemini API");
            return '';
        }
        
        // Normalize response
        $response = strtolower($response);
        
        if (strpos($response, 'positif') !== false || strpos($response, 'positive') !== false) {
            return 'positif';
        } else if (strpos($response, 'negatif') !== false || strpos($response, 'negative') !== false) {
            return 'negatif';
        } else {
            return 'netral';
        }
    }
    
    /**
     * Generate feedback for negative emotions
     * 
     * @return string Feedback message
     */
    public function generateFeedback() {
        $prompt = "Berikan saran atau kalimat penyemangat pendek untuk menjaga suasana diskusi tetap positif.";
        return $this->generateContent($prompt);
    }
    
    /**
     * Generate session summary
     * 
     * @param array $messages Array of messages from the session
     * @return string Session summary
     */
    public function generateSessionSummary($messages) {
        $messagesText = '';
        foreach ($messages as $message) {
            $messagesText .= "{$message['username']}: {$message['text']}\n";
        }
        
        $prompt = "Buatkan ringkasan dari percakapan berikut, fokus pada poin-poin penting dan suasana diskusi:\n\n{$messagesText}";
        return $this->generateContent($prompt);
    }
    
    /**
     * Call Gemini API to generate content
     * 
     * @param string $prompt Prompt for the API
     * @param int $maxTokens Maximum output tokens
     * @return string Generated content
     */
    private function generateContent($prompt, $maxTokens = 1000) {
        // Direct API call using the correct format
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey;
        
        // Force a unique request by adding a timestamp to ensure it's not cached
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt . ' [' . microtime(true) . ']'
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => $maxTokens
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_NONE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_NONE'
                ]
            ]
        ];
        
        // Log request untuk debugging
        error_log('Gemini API request URL: ' . $apiUrl);
        error_log('Gemini API request data: ' . json_encode($data));
        
        // Gunakan cURL untuk request yang lebih baik dan error handling
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for testing
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Increased timeout to 30 seconds
        
        // Execute the request and capture detailed information
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        // Log detailed information about the request
        error_log('Gemini API curl info: ' . json_encode($info));
        
        if ($response === false) {
            error_log('Gemini API request failed with curl error: ' . $error);
            curl_close($ch);
            return '';
        }
        
        if ($httpCode !== 200) {
            error_log('Gemini API request failed. HTTP Code: ' . $httpCode);
            error_log('Response body: ' . $response);
            curl_close($ch);
            return '';
        }
        
        curl_close($ch);
        
        // Log response untuk debugging
        error_log('Gemini API raw response: ' . substr($response, 0, 500));
        
        $responseData = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON decode error: ' . json_last_error_msg());
            error_log('Response that failed to decode: ' . substr($response, 0, 500));
            return '';
        }
        
        if (isset($responseData['error'])) {
            error_log('Gemini API error: ' . json_encode($responseData['error']));
            return '';
        }
        
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return $responseData['candidates'][0]['content']['parts'][0]['text'];
        } else {
            error_log('Unexpected response structure: ' . json_encode($responseData));
            return '';
        }
    }
}
