<?php
// Definisikan variabel untuk layout
$css_file = 'chatroom';
$js_file = 'chatroom';

// Mulai output buffering
ob_start();
?>

<!-- Popup Feedback AI -->
<div id="ai-feedback-popup" class="fixed inset-0 flex items-center justify-center z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full relative z-10">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-bold" id="feedback-title">Feedback</h3>
            <button id="close-feedback" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="feedback-emotion" class="text-center text-3xl mb-3"></div>
        <div id="feedback-content" class="mb-4 text-gray-700 text-center font-medium"></div>
        <div class="flex justify-end">
            <button id="acknowledge-feedback" class="bg-[#ffa726] hover:bg-[#e65100] text-white px-4 py-2 rounded transition">Mengerti</button>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-4">
    <div class="flex items-center mb-4">
        <a href="/dashboard" class="text-gray-600 hover:text-gray-900 mr-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <span class="ml-1">Kembali</span>
        </a>
        <h1 class="text-2xl font-bold ml-4"><?= $title ?></h1>
    </div>
    
    <div class="grid-layout gap-4">
        <!-- Chat Area -->
        <div class="col-span-12 lg:col-span-9">
            <div class="bg-white rounded-lg shadow-md overflow-hidden chat-container flex flex-col">
                <!-- Chat Header -->
                <div class="bg-gray-100 px-4 py-3 border-b flex justify-between items-center">
                    <div>
                        <h2 class="font-semibold"><?= $session_title ?></h2>
                        <p class="text-xs text-gray-600"><?= $session_description ?></p>
                    </div>
                    
                    <?php if ($_SESSION['role'] == 'leader' || $_SESSION['role'] == 'admin'): ?>
                    <form action="/discussions/end" method="POST" class="inline-block">
                        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline transition" onclick="return confirm('Yakin ingin mengakhiri diskusi ini?')">
                            Akhiri Diskusi
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                
                <!-- Chat Messages -->
                <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4">
                    <?php if (empty($messages) && empty($self_messages)): ?>
                    <div class="text-center text-gray-500 py-4">
                        Belum ada pesan. Mulai diskusi sekarang!
                    </div>
                    <?php else: ?>
                    <?php 
                    // Gunakan array asosiatif untuk mencegah duplikasi berdasarkan message_id
                    $uniqueMessages = [];
                    
                    // Proses pesan dari pengguna lain (messages) terlebih dahulu
                    foreach ($messages as $msg) {
                        // Pastikan message_id ada dan valid
                        if (isset($msg['message_id']) && !empty($msg['message_id'])) {
                            $uniqueMessages[$msg['message_id']] = $msg;
                        }
                    }
                    
                    // Proses pesan dari pengguna sendiri (self_messages)
                    foreach ($self_messages as $msg) {
                        // Pastikan message_id ada dan valid
                        if (isset($msg['message_id']) && !empty($msg['message_id'])) {
                            $uniqueMessages[$msg['message_id']] = $msg;
                        }
                    }
                    
                    // Konversi kembali ke array numerik
                    $allMessages = array_values($uniqueMessages);
                    
                    // Urutkan berdasarkan timestamp
                    usort($allMessages, function($a, $b) {
                        return strtotime($a['timestamp']) - strtotime($b['timestamp']);
                    });
                    
                    // Log untuk debugging
                    error_log("Rendering " . count($allMessages) . " unique messages in view");
                    
                    // Simpan ID pesan yang akan dirender untuk JavaScript
                    $renderedMessageIds = array_map(function($msg) {
                        return $msg['message_id'];
                    }, $allMessages);
                    
                    // Tampilkan semua pesan yang sudah diurutkan
                    foreach ($allMessages as $msg): 
                        // Tentukan jenis pesan
                        $isSystem = $msg['sender_id'] == '0';
                        $isSelf = $msg['sender_id'] == $current_user_id;
                    ?>
                    
                    <?php if ($isSystem): ?>
                    <!-- Pesan Sistem - Tampilkan di tengah -->
                    <div class="message mb-4" data-message-id="<?= $msg['message_id'] ?>" data-type="system">
                        <div class="bg-blue-100 p-3 rounded-lg inline-block max-w-[80%] mx-auto text-center">
                            <div class="text-xs text-blue-600 font-bold mb-1">SISTEM</div>
                            <div class="text-sm"><?= $msg['text'] ?></div>
                            <div class="text-xs text-gray-500 mt-1"><?= date('H:i', strtotime($msg['timestamp'])) ?></div>
                        </div>
                    </div>
                    
                    <?php elseif ($isSelf): ?>
                    <!-- Pesan dari user yang sedang login - Tampilkan HANYA di kanan (warna oranye) -->
                    <div class="message mb-4" data-message-id="<?= $msg['message_id'] ?>" data-type="self">
                        <div class="flex justify-end mb-2">
                            <div class="bg-[#ffa726] text-white p-3 rounded-lg max-w-[80%]">
                                <div class="text-sm"><?= $msg['text'] ?></div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-white opacity-75"><?= date('H:i', strtotime($msg['timestamp'])) ?></span>
                                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'leader'): ?>
                                    <span class="text-xs px-2 py-0.5 rounded-full 
                                        <?= $msg['emotion_label'] == 'positif' ? 'bg-green-200 text-green-800' : 
                                            ($msg['emotion_label'] == 'negatif' ? 'bg-red-200 text-red-800' : 
                                            'bg-blue-200 text-blue-800') ?>">
                                        <?= ucfirst($msg['emotion_label']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php else: ?>
                    <!-- Pesan dari user lain - Tampilkan HANYA di kiri (warna abu-abu) -->
                    <div class="message mb-4" data-message-id="<?= $msg['message_id'] ?>" data-type="other">
                        <div class="flex mb-2">
                            <div class="bg-gray-200 p-3 rounded-lg max-w-[80%]">
                                <div class="text-xs text-gray-600 font-bold mb-1"><?= $msg['username'] ?></div>
                                <div class="text-sm"><?= $msg['text'] ?></div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-500"><?= date('H:i', strtotime($msg['timestamp'])) ?></span>
                                    <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'leader'): ?>
                                    <span class="text-xs px-2 py-0.5 rounded-full 
                                        <?= $msg['emotion_label'] == 'positif' ? 'bg-green-200 text-green-800' : 
                                            ($msg['emotion_label'] == 'negatif' ? 'bg-red-200 text-red-800' : 
                                            'bg-blue-200 text-blue-800') ?>">
                                        <?= ucfirst($msg['emotion_label']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Chat Input -->
                <div class="border-t p-4">
                    <form id="chat-form" action="/chatroom/send" method="POST" class="flex">
                        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                        <input type="text" name="message" id="message-input" class="flex-1 border rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#f57c00]" placeholder="Ketik pesan..." required>
                        <button type="submit" class="bg-[#ffa726] hover:bg-[#e65100] text-white px-4 py-2 rounded-r-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Mood Panel -->
        <div class="col-span-12 lg:col-span-3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden h-full">
                <div class="bg-gray-100 px-4 py-3 border-b">
                    <h2 class="font-semibold">Suasana Diskusi</h2>
                </div>
                
                <div class="p-4">
                        <!-- Current Mood -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 mb-2">Suasana Saat Ini</h3>
                        <?php 
                        $moodEmoji = '😐';
                        $moodColor = 'bg-blue-100 text-blue-800';
                        
                        if (isset($mood_data) && isset($mood_data['team_mood'])) {
                            if ($mood_data['team_mood'] == 'positif') {
                                $moodEmoji = '😊';
                                $moodColor = 'bg-green-100 text-green-800';
                            } elseif ($mood_data['team_mood'] == 'negatif') {
                                $moodEmoji = '😟';
                                $moodColor = 'bg-red-100 text-red-800';
                            }
                        }
                        ?>
                        
                        <div class="flex items-center justify-center p-4 <?= $moodColor ?> rounded-lg">
                            <span class="text-4xl mr-2"><?= $moodEmoji ?></span>
                            <span class="text-lg font-bold"><?= isset($mood_data) && isset($mood_data['team_mood']) ? ucfirst($mood_data['team_mood']) : 'Netral' ?></span>
                        </div>
                    </div>
                    
                    <!-- Mood Stats -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 mb-2">Statistik Emosi</h3>
                        
                        <?php
                        $positifCount = 0;
                        $netralCount = 0;
                        $negatifCount = 0;
                        
                        if (isset($mood_data) && isset($mood_data['emotion_summary'])) {
                            $emotionSummary = json_decode($mood_data['emotion_summary'], true);
                            $positifCount = $emotionSummary['positif'] ?? 0;
                            $netralCount = $emotionSummary['netral'] ?? 0;
                            $negatifCount = $emotionSummary['negatif'] ?? 0;
                        }
                        
                        $total = $positifCount + $netralCount + $negatifCount;
                        $positifPercent = $total > 0 ? round(($positifCount / $total) * 100) : 0;
                        $netralPercent = $total > 0 ? round(($netralCount / $total) * 100) : 0;
                        $negatifPercent = $total > 0 ? round(($negatifCount / $total) * 100) : 0;
                        ?>
                        
                        <!-- Positif -->
                        <div class="mb-2">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-medium text-green-800">Positif</span>
                                <span class="text-xs font-medium text-green-800"><?= $positifCount ?> (<?= $positifPercent ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: <?= $positifPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Netral -->
                        <div class="mb-2">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-medium text-blue-800">Netral</span>
                                <span class="text-xs font-medium text-blue-800"><?= $netralCount ?> (<?= $netralPercent ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: <?= $netralPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Negatif -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-medium text-red-800">Negatif</span>
                                <span class="text-xs font-medium text-red-800"><?= $negatifCount ?> (<?= $negatifPercent ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: <?= $negatifPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div>
                        <a href="/mood?session_id=<?= $session['session_id'] ?>" class="block w-full bg-[#ffa726] hover:bg-[#e65100] text-white text-center font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition mb-2">
                            Lihat Detail Emosi
                        </a>
                        
                        <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'leader'): ?>
                        
                        <a href="/discussions/members?id=<?= $session['session_id'] ?>" class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 text-center font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                            Kelola Peserta
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const sessionId = <?= $session['session_id'] ?>;
        
        // Inisialisasi daftar pesan yang sudah dirender
        window.initialRenderedMessages = <?= json_encode($renderedMessageIds ?? []) ?>;
        console.log('Initial rendered messages:', window.initialRenderedMessages);
        let lastTimestamp = <?= !empty($messages) ? "'" . end($messages)['timestamp'] . "'" : 'null' ?>;
        const currentUserId = '<?= $current_user_id ?>';
        let tempMessageId = 0;
        
        // AI Feedback Popup Elements
        const aiPopup = document.getElementById('ai-feedback-popup');
        const closePopup = document.getElementById('close-feedback');
        const acknowledgePopup = document.getElementById('acknowledge-feedback');
        const feedbackTitle = document.getElementById('feedback-title');
        const feedbackContent = document.getElementById('feedback-content');
        const feedbackEmotion = document.getElementById('feedback-emotion');
        
        // Scroll to bottom of chat
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Variabel untuk melacak tren emosi
        let recentMessages = [];
        const MESSAGES_TO_ANALYZE = 3; // Jumlah pesan untuk dianalisis
        let lastFeedbackTime = 0;
        const FEEDBACK_COOLDOWN = 60000; // Cooldown 1 menit (60000 ms) antara feedback
        
        // Fungsi untuk menganalisis tren emosi dari pesan-pesan terakhir
        function analyzeEmotionTrend() {
            // Jika belum cukup pesan untuk dianalisis, return
            if (recentMessages.length < MESSAGES_TO_ANALYZE) {
                return null;
            }
            
            // Hitung jumlah pesan positif dan negatif
            let positifCount = 0;
            let negatifCount = 0;
            
            recentMessages.forEach(msg => {
                if (msg.emotion_label === 'positif') {
                    positifCount++;
                } else if (msg.emotion_label === 'negatif') {
                    negatifCount++;
                }
            });
            
            console.log('Analisis tren emosi:', { positifCount, negatifCount, total: recentMessages.length });
            
            // Jika mayoritas pesan positif (minimal 2 dari 3)
            if (positifCount >= 2) {
                return 'positif';
            }
            // Jika mayoritas pesan negatif (minimal 2 dari 3)
            else if (negatifCount >= 2) {
                return 'negatif';
            }
            
            // Jika tidak ada tren yang jelas
            return null;
        }
        
        // Fungsi untuk menampilkan popup feedback AI berdasarkan tren emosi
        function showAIFeedback(message) {
            // Tambahkan pesan ke daftar pesan terbaru
            recentMessages.push(message);
            
            // Jika daftar melebihi jumlah yang akan dianalisis, hapus yang paling lama
            if (recentMessages.length > MESSAGES_TO_ANALYZE) {
                recentMessages.shift();
            }
            
            // AI Feedback ditampilkan untuk semua pengguna
            
            // Cek cooldown
            const now = Date.now();
            if (now - lastFeedbackTime < FEEDBACK_COOLDOWN) {
                return;
            }
            
            // Analisis tren emosi
            const trend = analyzeEmotionTrend();
            
            // Jika tidak ada tren yang jelas, tidak perlu menampilkan feedback
            if (!trend) {
                return;
            }
            
            // Update waktu feedback terakhir
            lastFeedbackTime = now;
            
            // Set judul dan emoji berdasarkan tren emosi
            if (trend === 'positif') {
                feedbackTitle.textContent = 'Semangat Diskusi!';
                feedbackEmotion.innerHTML = '😊';
                feedbackTitle.className = 'text-xl font-bold text-green-600';
                
                // Gunakan Gemini API untuk mendapatkan feedback positif
                generateFeedbackWithGemini('positif');
            } else {
                feedbackTitle.textContent = 'Tips Diskusi';
                feedbackEmotion.innerHTML = '💡';
                feedbackTitle.className = 'text-xl font-bold text-blue-600';
                
                // Gunakan Gemini API untuk mendapatkan feedback negatif
                generateFeedbackWithGemini('negatif');
            }
            
            // Tampilkan popup
            aiPopup.classList.remove('hidden');
        }
        
        // Event listener untuk tombol tutup popup
        closePopup.addEventListener('click', function() {
            aiPopup.classList.add('hidden');
        });
        
        // Event listener untuk tombol acknowledge
        acknowledgePopup.addEventListener('click', function() {
            aiPopup.classList.add('hidden');
        });
        
        // Tutup popup jika user klik di luar popup
        aiPopup.addEventListener('click', function(e) {
            if (e.target === aiPopup) {
                aiPopup.classList.add('hidden');
            }
        });
        
        // Fungsi untuk mendapatkan feedback dari Gemini API
        function generateFeedbackWithGemini(emotionType) {
            // Tampilkan loading indicator di feedback content
            feedbackContent.textContent = 'Menyiapkan feedback...';
            
            // Tampilkan popup sementara menunggu respons API
            aiPopup.classList.remove('hidden');
            
            // Prompt sudah dihandle di backend, kita hanya perlu mengirim jenis emosi
            
            // Panggil Gemini API
            fetch('/api/generate-feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    emotion_type: emotionType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.feedback) {
                    // Update konten feedback dengan respons dari API
                    feedbackContent.textContent = data.feedback;
                } else {
                    // Fallback jika API gagal
                    if (emotionType === 'positif') {
                        feedbackContent.textContent = 'Keren! Semangat positifmu membuat diskusi ini semakin hidup dan produktif! 🌟';
                    } else {
                        feedbackContent.textContent = 'Yuk, coba ekspresikan pendapatmu dengan cara yang lebih positif untuk membuat diskusi ini lebih menyenangkan! 💪';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching feedback:', error);
                // Fallback jika terjadi error
                if (emotionType === 'positif') {
                    feedbackContent.textContent = 'Keren! Semangat positifmu membuat diskusi ini semakin hidup dan produktif! 🌟';
                } else {
                    feedbackContent.textContent = 'Yuk, coba ekspresikan pendapatmu dengan cara yang lebih positif untuk membuat diskusi ini lebih menyenangkan! 💪';
                }
            });
        }
        
        // Initial scroll
        scrollToBottom();
        
        // Polling interval
        let pollingInterval = 1000; // 1 seconds
        let pollingTimer = null;
        
        // Fungsi untuk memulai polling
        function startPolling() {
            // Hentikan polling yang sedang berjalan (jika ada)
            if (pollingTimer) {
                clearInterval(pollingTimer);
            }
            
            // Mulai polling baru
            pollingTimer = setInterval(getNewMessages, pollingInterval);
            console.log('Polling started with interval:', pollingInterval, 'ms');
        }
        
        // Mulai polling saat halaman dimuat
        startPolling();
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            // Kosongkan input sebelum pengiriman untuk UX yang lebih baik
            const originalMessage = message;
            messageInput.value = '';
            
            // Tampilkan loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-center text-gray-500 py-2';
            loadingDiv.innerHTML = 'Mengirim pesan...';
            chatMessages.appendChild(loadingDiv);
            scrollToBottom();
            
            // Optimistic UI: Tambahkan pesan langsung ke UI
            const optimisticMessage = {
                message_id: 'temp_' + Date.now(), // ID sementara
                sender_id: currentUserId, // ID pengirim (user saat ini)
                text: message,
                timestamp: new Date().toISOString(),
                username: '<?= $_SESSION['username'] ?>', // Username pengirim
                emotion_label: 'netral', // Default emotion
                is_optimistic: true // Tandai sebagai pesan optimistic
            };
            
            console.log('Adding optimistic message:', optimisticMessage);
            
            // Tambahkan pesan optimistic ke chat
            const optimisticElement = addMessageToChat(optimisticMessage);
            
            // Hapus loading div setelah menampilkan pesan optimistic
            if (loadingDiv.parentNode) {
                loadingDiv.parentNode.removeChild(loadingDiv);
            }
            
            // Kirim pesan ke server
            
            fetch('/chatroom/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    message: message
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Hapus loading indicator
                    const loadingDiv = document.querySelector('.text-center.text-gray-500.py-2');
                    if (loadingDiv && loadingDiv.parentNode) {
                        loadingDiv.parentNode.removeChild(loadingDiv);
                    }
                    
                    // Hapus pesan optimistic yang memiliki ID temp_*
                    const optimisticMessages = document.querySelectorAll('[data-message-id^="temp_"]');
                    optimisticMessages.forEach(msg => {
                        if (msg && msg.parentNode) {
                            msg.parentNode.removeChild(msg);
                        }
                    });
                    
                    // Tambahkan pesan yang dikonfirmasi server jika belum ada
                    if (data.data && data.data.message_id) {
                        // Periksa apakah pesan sudah ada di chat
                        const existingMessage = document.querySelector(`[data-message-id="${data.data.message_id}"]`);
                        if (!existingMessage) {
                            addMessageToChat(data.data);
                        }
                    }
                    
                    // Update lastTimestamp untuk polling berikutnya
                    if (data.data && data.data.timestamp) {
                        lastTimestamp = data.data.timestamp;
                    }
                    
                    // Restart polling untuk mendapatkan pembaruan segera
                    startPolling();
                    
                    console.log('Pesan berhasil dikirim:', data);
                } else {
                    // Hapus pesan optimistic yang memiliki ID temp_*
                    const optimisticMessages = document.querySelectorAll('[data-message-id^="temp_"]');
                    optimisticMessages.forEach(msg => {
                        if (msg && msg.parentNode) {
                            msg.parentNode.removeChild(msg);
                        }
                    });
                    
                    // Kembalikan pesan ke input jika gagal
                    messageInput.value = originalMessage;
                    
                    // Tampilkan pesan error jika ada
                    console.error('Gagal mengirim pesan:', data.message);
                    alert(data.message || 'Gagal mengirim pesan. Silakan coba lagi.');
                }
            })
            .catch(error => {
                // Hapus pesan optimistic yang memiliki ID temp_*
                const optimisticMessages = document.querySelectorAll('[data-message-id^="temp_"]');
                optimisticMessages.forEach(msg => {
                    if (msg && msg.parentNode) {
                        msg.parentNode.removeChild(msg);
                    }
                });
                
                // Kembalikan pesan ke input jika gagal
                messageInput.value = originalMessage;
                
                console.error('Error sending message:', error);
                alert('Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
            });
        });
        
        // Function to add a single message to chat
        function addMessageToChat(message) {
            // Skip if message is already in the chat (check by message_id)
            if (message && message.message_id) {
                const existingMessage = document.querySelector(`[data-message-id="${message.message_id}"]`);
                if (existingMessage) {
                    console.log('Skipping duplicate message:', message.message_id);
                    return existingMessage; // Return existing message element and skip adding a new one
                }
                
                // También verificar mensajes temporales con el mismo contenido
                if (!message.message_id.toString().startsWith('temp_')) {
                    const tempMessages = document.querySelectorAll('[data-message-id^="temp_"]');
                    for (const tempMsg of tempMessages) {
                        const tempMsgText = tempMsg.querySelector('.text-sm')?.textContent;
                        if (tempMsgText === message.text && 
                            ((message.sender_id === currentUserId && tempMsg.getAttribute('data-type') === 'self') ||
                             (message.sender_id !== currentUserId && tempMsg.getAttribute('data-type') === 'other'))) {
                            console.log('Removing temporary message with same content:', tempMsgText);
                            tempMsg.parentNode.removeChild(tempMsg);
                            break;
                        }
                    }
                }
            }
            
            // Pastikan sender_id dikonversi ke string untuk perbandingan yang konsisten
            const messageSenderId = message ? String(message.sender_id) : '';
            
            // PENTING: Tentukan apakah pesan dari user yang sedang login atau user lain
            const isSelf = messageSenderId === currentUserId;
            const isSystem = messageSenderId === '0';
            
            console.log('Message sender ID:', messageSenderId);
            console.log('Current user ID:', currentUserId);
            console.log('Is self:', isSelf);
            console.log('Is system:', isSystem);
            console.log('Message:', message);
            
            // Jika ini adalah pesan dari user saat ini, periksa dan hapus pesan optimistic yang terkait
            if (isSelf && message.message_id && !message.message_id.toString().startsWith('temp_')) {
                const optimisticMessages = document.querySelectorAll('[data-message-id^="temp_"]');
                for (const optMsg of optimisticMessages) {
                    // Jika ada pesan optimistic dengan teks yang sama, hapus pesan optimistic tersebut
                    const optMsgText = optMsg.querySelector('.text-sm')?.textContent;
                    if (optMsgText === message.text) {
                        console.log('Removing optimistic message with same text:', optMsgText);
                        optMsg.parentNode.removeChild(optMsg);
                        break;
                    }
                }
            }
            
            // Tambahkan pesan ke analisis tren emosi jika memiliki label emosi
            if (message.emotion_label) {
                showAIFeedback(message);
            }
            
            // Buat elemen pesan baru
            const messageElement = document.createElement('div');
            messageElement.className = 'message mb-4';
            
            // Tambahkan atribut data
            if (message && message.message_id) {
                messageElement.setAttribute('data-message-id', message.message_id);
                
                if (isSystem) {
                    messageElement.setAttribute('data-type', 'system');
                } else if (isSelf) {
                    messageElement.setAttribute('data-type', 'self');
                } else {
                    messageElement.setAttribute('data-type', 'other');
                }
            }
            
            // Cek apakah pesan dari sistem, pengguna yang sedang login, atau pengguna lain
            if (isSystem) {
                // Pesan Sistem - Tampilkan di tengah
                messageElement.innerHTML = `
                    <div class="bg-blue-100 p-3 rounded-lg inline-block max-w-[80%] mx-auto text-center">
                        <div class="text-xs text-blue-600 font-bold mb-1">SISTEM</div>
                        <div class="text-sm">${message.text}</div>
                        <div class="text-xs text-gray-500 mt-1">${formatTime(message.timestamp)}</div>
                    </div>
                `;
            } else if (isSelf) {
                // Pesan dari user yang sedang login - Tampilkan HANYA di kanan (warna oranye)
                messageElement.innerHTML = `
                    <div class="flex justify-end mb-2">
                        <div class="bg-[#ffa726] text-white p-3 rounded-lg max-w-[80%]">
                            <div class="text-sm">${message.text}</div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-white opacity-75">${formatTime(message.timestamp)}</span>
                                ${('<?= $_SESSION['role'] ?>' === 'admin' || '<?= $_SESSION['role'] ?>' === 'leader') ? `
                                <span class="text-xs px-2 py-0.5 rounded-full 
                                    ${message.emotion_label === 'positif' ? 'bg-green-200 text-green-800' : 
                                    (message.emotion_label === 'negatif' ? 'bg-red-200 text-red-800' : 
                                    'bg-blue-200 text-blue-800')}">
                                    ${capitalizeFirstLetter(message.emotion_label)}
                                </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            } else if (!isSelf) {
                // Pesan dari user lain - Tampilkan HANYA di kiri (warna abu-abu)
                messageElement.innerHTML = `
                    <div class="flex mb-2">
                        <div class="bg-gray-200 p-3 rounded-lg max-w-[80%]">
                            <div class="text-xs text-gray-600 font-bold mb-1">${message.username}</div>
                            <div class="text-sm">${message.text}</div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-gray-500">${formatTime(message.timestamp)}</span>
                                ${('<?= $_SESSION['role'] ?>' === 'admin' || '<?= $_SESSION['role'] ?>' === 'leader') ? `
                                <span class="text-xs px-2 py-0.5 rounded-full 
                                    ${message.emotion_label === 'positif' ? 'bg-green-200 text-green-800' : 
                                    (message.emotion_label === 'negatif' ? 'bg-red-200 text-red-800' : 
                                    'bg-blue-200 text-blue-800')}">
                                    ${capitalizeFirstLetter(message.emotion_label)}
                                </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }
            
            chatMessages.appendChild(messageElement);
            scrollToBottom();
            return messageElement;
        }
        
        // Get new messages
        function getNewMessages() {
            const url = `/chatroom/messages?session_id=${sessionId}${lastTimestamp ? '&last_timestamp=' + encodeURIComponent(lastTimestamp) : ''}`;
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.messages && data.data.messages.length > 0) {
                    // Log untuk debugging
                    console.log('Current user ID:', currentUserId);
                    console.log('New messages:', data.data.messages.length);
                    console.log('Last timestamp before update:', lastTimestamp);
                    
                    // Hapus pesan duplikat yang ada di DOM
                    removeAllDuplicateMessages();
                    
                    // Tambahkan pesan baru ke chat
                    data.data.messages.forEach(message => {
                        // Pastikan sender_id selalu string untuk perbandingan yang konsisten
                        message.sender_id = String(message.sender_id);
                        
                        // Periksa apakah pesan ini sudah ada di chat
                        const existingMessage = document.querySelector(`[data-message-id="${message.message_id}"]`);
                        if (existingMessage) {
                            console.log('Skipping existing message:', message.message_id);
                            return;
                        }
                        
                        // Tambahkan pesan ke chat berdasarkan tipe
                        addMessageToChat(message);
                    });
                    
                    // Update last timestamp hanya jika ada pesan baru
                    if (data.data.messages.length > 0) {
                        lastTimestamp = data.data.messages[data.data.messages.length - 1].timestamp;
                        console.log('Last timestamp updated to:', lastTimestamp);
                    }
                    
                    // Update mood data if available
                    if (data.data.mood_data) {
                        updateMoodData(data.data.mood_data);
                    }
                    
                    // Scroll to bottom only if new messages were added
                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error('Error getting messages:', error);
            });
        }
        
        // Fungsi untuk menghapus semua pesan duplikat
        function removeAllDuplicateMessages() {
            // Dapatkan semua pesan
            const allMessages = document.querySelectorAll('.message');
            const processedIds = {};
            
            // Periksa setiap pesan
            allMessages.forEach(msg => {
                const messageId = msg.getAttribute('data-message-id');
                const messageType = msg.getAttribute('data-type');
                
                // Jika pesan ini tidak memiliki ID, lewati
                if (!messageId) return;
                
                // Jika pesan ini sudah diproses, hapus
                if (processedIds[messageId]) {
                    console.log('Removing duplicate message:', messageId);
                    msg.remove();
                    return;
                } 
                
                // Tandai pesan ini sudah diproses
                processedIds[messageId] = true;
                
                // Jika pesan dari pengguna yang sedang login (type=self)
                if (messageType === 'self') {
                    // Pastikan pesan self hanya muncul di kanan
                    const msgContainer = msg.querySelector('.flex');
                    if (msgContainer && !msgContainer.classList.contains('justify-end')) {
                        console.log('Removing self message with wrong position');
                        msg.remove();
                        return;
                    }
                }
                
                // Jika pesan dari pengguna lain (type=other)
                if (messageType === 'other') {
                    // Pastikan pesan other hanya muncul di kiri
                    const msgContainer = msg.querySelector('.flex');
                    if (msgContainer && msgContainer.classList.contains('justify-end')) {
                        console.log('Removing other message with wrong position');
                        msg.remove();
                        return;
                    }
                }
            });
        }
        
        // Fungsi untuk menambahkan pesan dari pengguna yang sedang login
        function addSelfMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'message mb-4';
            messageElement.setAttribute('data-message-id', message.message_id);
            messageElement.setAttribute('data-type', 'self');
            
            messageElement.innerHTML = `
                <div class="flex justify-end mb-2">
                    <div class="bg-[#ffa726] text-white p-3 rounded-lg max-w-[80%]">
                        <div class="text-sm">${message.text}</div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-white opacity-75">${formatTime(message.timestamp)}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                ${message.emotion_label === 'positif' ? 'bg-green-200 text-green-800' : 
                                (message.emotion_label === 'negatif' ? 'bg-red-200 text-red-800' : 
                                'bg-blue-200 text-blue-800')}">
                                ${capitalizeFirstLetter(message.emotion_label)}
                            </span>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('chat-messages').appendChild(messageElement);
            return messageElement;
        }
        
        // Fungsi untuk menambahkan pesan dari pengguna lain
        function addOtherMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'message mb-4';
            messageElement.setAttribute('data-message-id', message.message_id);
            messageElement.setAttribute('data-type', 'other');
            
            messageElement.innerHTML = `
                <div class="flex mb-2">
                    <div class="bg-gray-200 p-3 rounded-lg max-w-[80%]">
                        <div class="text-xs text-gray-600 font-bold mb-1">${message.username}</div>
                        <div class="text-sm">${message.text}</div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-500">${formatTime(message.timestamp)}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                ${message.emotion_label === 'positif' ? 'bg-green-200 text-green-800' : 
                                (message.emotion_label === 'negatif' ? 'bg-red-200 text-red-800' : 
                                'bg-blue-200 text-blue-800')}">
                                ${capitalizeFirstLetter(message.emotion_label)}
                            </span>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('chat-messages').appendChild(messageElement);
            return messageElement;
        }
        
        // Fungsi untuk menambahkan pesan sistem
        function addSystemMessage(message) {
            const messageElement = document.createElement('div');
            messageElement.className = 'message mb-4';
            messageElement.setAttribute('data-message-id', message.message_id);
            messageElement.setAttribute('data-type', 'system');
            
            messageElement.innerHTML = `
                <div class="bg-blue-100 p-3 rounded-lg inline-block max-w-[80%] mx-auto text-center">
                    <div class="text-xs text-blue-600 font-bold mb-1">SISTEM</div>
                    <div class="text-sm">${message.text}</div>
                    <div class="text-xs text-gray-500 mt-1">${formatTime(message.timestamp)}</div>
                </div>
            `;
            
            document.getElementById('chat-messages').appendChild(messageElement);
            return messageElement;
        }
        
        // Fungsi untuk menambahkan pesan jika belum ada
        function addMessageIfNotExists(message) {
            // Buat kunci unik untuk pesan ini
            const messageKey = `${message.sender_id}_${md5(message.text)}`;
            
            // Periksa apakah pesan sudah ada di chat
            const existingMessage = document.querySelector(`[data-message-id="${message.message_id}"]`);
            const existingMessageByKey = document.querySelector(`[data-message-key="${messageKey}"]`);
            
            // Jika pesan belum ada di chat, tambahkan
            if (!existingMessage && !existingMessageByKey) {
                console.log('Adding new message:', message);
                addMessageToChat(message);
            } else {
                console.log('Message already exists:', messageKey);
            }
        }
        
        // Fungsi untuk menghapus pesan duplikat
        function removeDuplicateMessages() {
            // Dapatkan semua pesan dari pengguna yang sedang login
            const selfMessages = document.querySelectorAll('[data-is-self="true"]');
            const processedKeys = {};
            
            // Periksa setiap pesan
            selfMessages.forEach(msg => {
                const messageKey = msg.getAttribute('data-message-key');
                
                // Jika pesan ini sudah diproses, hapus
                if (processedKeys[messageKey]) {
                    console.log('Removing duplicate self message:', messageKey);
                    msg.remove();
                } else {
                    // Tandai pesan ini sudah diproses
                    processedKeys[messageKey] = true;
                }
            });
            
            // Hapus pesan dari pengguna yang sedang login yang muncul di kiri
            const allMessages = document.querySelectorAll('.message');
            allMessages.forEach(msg => {
                const isSelf = msg.getAttribute('data-is-self') === 'true';
                if (isSelf) {
                    const msgContainer = msg.querySelector('.flex');
                    if (msgContainer && !msgContainer.classList.contains('justify-end')) {
                        console.log('Removing self message with wrong position');
                        msg.remove();
                    }
                }
            });
        }
        
        // Fungsi untuk membersihkan pesan duplikat
        function cleanupDuplicateMessages() {
            // Dapatkan semua pesan
            const allMessages = document.querySelectorAll('.message');
            const processedKeys = {};
            
            // Periksa setiap pesan
            allMessages.forEach(msg => {
                const messageId = msg.getAttribute('data-message-id');
                const messageKey = msg.getAttribute('data-message-key');
                const senderId = msg.getAttribute('data-sender-id');
                const isSelf = senderId === currentUserId;
                
                // Jika pesan dari pengguna yang sedang login
                if (isSelf) {
                    // Periksa apakah pesan ini sudah diproses
                    if (processedKeys[messageKey]) {
                        // Jika sudah diproses, hapus pesan ini (duplikat)
                        console.log('Removing duplicate self message:', messageKey);
                        msg.remove();
                    } else {
                        // Jika belum diproses, tandai sebagai diproses
                        processedKeys[messageKey] = true;
                        
                        // Pastikan pesan dari pengguna yang sedang login muncul di kanan
                        const msgContainer = msg.querySelector('.flex');
                        if (msgContainer && !msgContainer.classList.contains('justify-end')) {
                            console.log('Removing self message with wrong position:', messageKey);
                            msg.remove();
                        }
                    }
                }
            });
        }
        
        // Update mood data
        function updateMoodData(moodData) {
            // Implementation depends on the structure of your mood data
            // This is a simplified example
            if (moodData.team_mood) {
                const moodEmoji = moodData.team_mood === 'positif' ? '😊' : (moodData.team_mood === 'negatif' ? '😟' : '😐');
                const moodColor = moodData.team_mood === 'positif' ? 'bg-green-100 text-green-800' : 
                                 (moodData.team_mood === 'negatif' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800');
                
                // Update mood display
                // This would need to be adapted to your actual HTML structure
                const moodDisplay = document.querySelector('.mood-display');
                if (moodDisplay) {
                    moodDisplay.className = `flex items-center justify-center p-4 ${moodColor} rounded-lg mood-display`;
                    moodDisplay.innerHTML = `
                        <span class="text-4xl mr-2">${moodEmoji}</span>
                        <span class="text-lg font-bold">${moodData.team_mood.charAt(0).toUpperCase() + moodData.team_mood.slice(1)}</span>
                    `;
                }
                
                // Update statistics if available
                if (moodData.emotion_summary) {
                    // Implementation depends on your HTML structure
                }
            }
        }
        
        // Fungsi untuk menghasilkan MD5 hash
        function md5(string) {
            // Implementasi sederhana untuk menghasilkan hash unik dari string
            // Ini bukan MD5 yang sebenarnya, tapi cukup untuk kasus ini
            let hash = 0;
            if (string.length === 0) return hash;
            for (let i = 0; i < string.length; i++) {
                const char = string.charCodeAt(i);
                hash = ((hash << 5) - hash) + char;
                hash = hash & hash; // Convert to 32bit integer
            }
            return hash.toString();
        }
        
        // Fungsi untuk memformat waktu
        function formatTime(timestamp) {
            if (!timestamp) return '';
            return new Date(timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        
        // Fungsi untuk mengubah huruf pertama menjadi kapital
        function capitalizeFirstLetter(string) {
            if (!string) return '';
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        // Poll for new messages every 1 second untuk mengurangi delay
        setInterval(getNewMessages, 1000);
        
        // Panggil getNewMessages segera setelah halaman dimuat
        getNewMessages();
    });
</script>

<?php
// Simpan output buffering ke variabel $content
$content = ob_get_clean();

// Sertakan layout utama
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
