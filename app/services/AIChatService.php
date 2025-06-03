<?php
/**
 * AIChatService Class
 * 
 * Kelas untuk menangani komunikasi dengan Google Gemini API untuk fitur chat AI pribadi
 */

class AIChatService {
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
     * Mengirim prompt ke API Gemini dan mendapatkan respons
     * 
     * @param string $prompt Prompt untuk dikirim ke AI
     * @return string Respons dari AI
     */
    public function askAI($prompt) {
        // Buat data untuk API request
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 800,
                'topP' => 0.95,
                'topK' => 40
            ]
        ];
        
        // Kirim request ke API
        $url = $this->endpoint . '?key=' . $this->apiKey;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Untuk pengembangan
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Timeout 15 detik
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Log untuk debugging
        error_log("AI Chat - HTTP Code: " . $httpCode);
        if ($error) {
            error_log("AI Chat - cURL Error: " . $error);
            return "[AI tidak merespon]";
        }
        
        $result = json_decode($response, true);
        
        // Periksa apakah respons valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("AI Chat - JSON Error: " . json_last_error_msg());
            return "[AI tidak merespon]";
        }
        
        // Ekstrak teks dari respons
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }
        
        // Fallback jika format respons tidak sesuai ekspektasi
        error_log("AI Chat - Format respons tidak sesuai: " . json_encode($result));
        return "[AI tidak merespon]";
    }
}
