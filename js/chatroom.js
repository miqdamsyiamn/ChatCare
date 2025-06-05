/**
 * ChatCare - Chatroom JavaScript
 * 
 * Handles real-time chat functionality and emotion visualization
 */

document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    
    // Flag untuk menandai apakah sedang mengirim pesan
    let isSendingMessage = false;
    
    // Get session ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id');
    
    // Get user ID from hidden input
    let userId = '';
    const sessionIdInput = document.querySelector('input[name="session_id"]');
    if (sessionIdInput) {
        userId = sessionIdInput.getAttribute('data-user-id') || '';
    }
    
    // Track last message timestamp for polling
    let lastTimestamp = null;
    
    // Initialize - get last timestamp if messages exist
    const messages = document.querySelectorAll('.message');
    if (messages.length > 0) {
        // Get the timestamp from the last message
        const allTimestamps = document.querySelectorAll('.text-xs.text-gray-500, .text-xs.text-white.opacity-75');
        if (allTimestamps.length > 0) {
            const lastTimestampElement = allTimestamps[allTimestamps.length - 1];
            const timeText = lastTimestampElement.textContent.trim();
            // Convert time to full timestamp (this is an approximation)
            const today = new Date();
            const [hours, minutes] = timeText.split(':');
            today.setHours(parseInt(hours), parseInt(minutes), 0);
            lastTimestamp = today.toISOString().slice(0, 19).replace('T', ' ');
        }
    }
    
    // Scroll to bottom of chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Initial scroll
    scrollToBottom();
    
    // Send message via AJAX
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;
        
        // Set flag bahwa sedang mengirim pesan
        isSendingMessage = true;
        
        // Disable form during submission
        const submitButton = chatForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        
        // Membuat FormData untuk mengirim data
        const formData = new FormData();
        formData.append('session_id', sessionId);
        formData.append('message', message);
        
        // Menampilkan pesan sementara (optimistic UI)
        const tempMessageId = 'temp-' + Date.now();
        const tempMessageElement = document.createElement('div');
        tempMessageElement.id = tempMessageId;
        tempMessageElement.className = 'message mb-4';
        tempMessageElement.innerHTML = `
            <div class="flex justify-end mb-2">
                <div class="bg-[#f57c00] text-white p-3 rounded-lg max-w-[80%]">
                    <div class="text-sm">${message}</div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-white opacity-75">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-800">Mengirim...</span>
                    </div>
                </div>
            </div>
        `;
        chatMessages.appendChild(tempMessageElement);
        scrollToBottom();
        
        // Store the message text for later verification
        const messageText = message;
        
        // Add a delay to allow any existing messages to be rendered first
        setTimeout(() => {
            // Check if message already exists in DOM before sending
            if (NotificationManager.isMessageInChat(messageText)) {
                console.log('Message already exists in DOM before sending, updating temp message');
                const tempMessage = document.getElementById(tempMessageId);
                if (tempMessage) {
                    const statusElement = tempMessage.querySelector('.rounded-full');
                    if (statusElement) {
                        statusElement.textContent = 'Terkirim';
                        statusElement.className = 'text-xs px-2 py-0.5 rounded-full bg-green-200 text-green-800';
                    }
                }
                messageInput.value = '';
                submitButton.disabled = false;
                isSendingMessage = false;
                return;
            }
            
            // Simpan pesan yang sedang dikirim untuk pengecekan duplikat
            window.lastSentMessage = messageText;
            
            fetch('/chatroom/send', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Selalu periksa apakah ada data.data meskipun success=false
                // Ini menangani kasus di mana server mengembalikan success=false tapi pesan sebenarnya berhasil disimpan
                if (data.success || (data.data && data.data.message_id)) {
                    // Clear input
                    messageInput.value = '';
                    
                    // Remove temporary message
                    const tempMessage = document.getElementById(tempMessageId);
                    if (tempMessage) {
                        tempMessage.remove();
                    }
                    
                    // Add the message ID to our tracking Set to prevent duplicates
                    if (data.data && data.data.message_id) {
                        const newMessageId = data.data.message_id;
                        displayedMessageIds.add(newMessageId);
                        console.log('Added sent message to tracking:', newMessageId);
                        
                        // Jika pesan sudah ada di DOM, hapus untuk mencegah duplikasi
                        const existingMessage = document.getElementById(`msg-${newMessageId}`);
                        if (existingMessage) {
                            console.log('Removing existing message to prevent duplication:', newMessageId);
                            existingMessage.remove();
                        }
                    }
                    
                    // Set a cooldown to prevent polling from immediately fetching the same message
                    pollingCooldown = true;
                    setTimeout(() => {
                        pollingCooldown = false;
                        // Get new messages (including the one just sent)
                        getNewMessages();
                    }, 2000); // Tambah waktu cooldown menjadi 2 detik
                } else {
                    console.error('Error sending message:', data.message);
                    
                    // Periksa lagi setelah delay untuk memastikan pesan tidak muncul di DOM
                    setTimeout(() => {
                        // Double check if message is in chat
                        // Jika ada data.data, berarti pesan mungkin sudah disimpan meskipun ada error
                        if (!NotificationManager.isMessageInChat(messageText) && 
                            !document.getElementById(`msg-${data.data?.message_id}`) &&
                            !data.data) {
                            // Only show notification if message is not in the chat
                            NotificationManager.showNotification('Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
                            
                            // Remove temporary message
                            const tempMessage = document.getElementById(tempMessageId);
                            if (tempMessage) {
                                tempMessage.remove();
                            }
                        } else {
                            console.log('Message appears to be in the DOM despite server error');
                            // Update the temporary message to show it was sent
                            const tempMessage = document.getElementById(tempMessageId);
                            if (tempMessage) {
                                const statusElement = tempMessage.querySelector('.rounded-full');
                                if (statusElement) {
                                    statusElement.textContent = 'Terkirim';
                                    statusElement.className = 'text-xs px-2 py-0.5 rounded-full bg-green-200 text-green-800';
                                }
                            }
                        }
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                
                // Periksa lagi setelah delay untuk memastikan pesan tidak muncul di DOM
                setTimeout(() => {
                    // Double check if message is in chat
                    if (!NotificationManager.isMessageInChat(messageText)) {
                        // Only show notification if message is not in the chat
                        NotificationManager.showNotification('Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
                        
                        // Remove temporary message
                        const tempMessage = document.getElementById(tempMessageId);
                        if (tempMessage) {
                            tempMessage.remove();
                        }
                    } else {
                        console.log('Message appears to be in the DOM despite network error');
                        // Update the temporary message to show it was sent
                        const tempMessage = document.getElementById(tempMessageId);
                        if (tempMessage) {
                            const statusElement = tempMessage.querySelector('.rounded-full');
                            if (statusElement) {
                                statusElement.textContent = 'Terkirim';
                                statusElement.className = 'text-xs px-2 py-0.5 rounded-full bg-green-200 text-green-800';
                            }
                        }
                    }
                }, 500);
            })
            .finally(() => {
                // Re-enable form
                submitButton.disabled = false;
                messageInput.focus();
                
                // Setelah 2 detik, reset flag pengiriman pesan
                setTimeout(() => {
                    isSendingMessage = false;
                }, 2000);
            });
        }, 100); // Add a small delay to check for existing messages
    });
    
    // Track displayed message IDs to prevent duplicates
    const displayedMessageIds = new Set();
    
    // Track if we're in a polling cooldown period
    let pollingCooldown = false;
    
    // Inisialisasi displayedMessageIds dengan pesan yang sudah ada di DOM
    function initializeDisplayedMessages() {
        // Gunakan daftar pesan yang sudah dirender dari server jika tersedia
        if (window.initialRenderedMessages && Array.isArray(window.initialRenderedMessages)) {
            window.initialRenderedMessages.forEach(messageId => {
                if (messageId) {
                    displayedMessageIds.add(messageId);
                    console.log('Added server-rendered message to tracking:', messageId);
                }
            });
        }
        
        // Ambil juga semua pesan yang sudah ada di DOM sebagai backup
        const existingMessages = document.querySelectorAll('.message[data-message-id]');
        existingMessages.forEach(element => {
            const messageId = element.getAttribute('data-message-id');
            if (messageId) {
                displayedMessageIds.add(messageId);
                console.log('Added DOM message to tracking:', messageId);
            }
        });
        
        console.log('Total tracked messages after initialization:', displayedMessageIds.size);
    }
    
    // Panggil inisialisasi saat halaman dimuat
    initializeDisplayedMessages();
    
    // Get new messages
    function getNewMessages() {
        // Skip polling jika sedang mengirim pesan atau dalam cooldown
        if (pollingCooldown || isSendingMessage) {
            console.log('Skipping polling - cooldown or sending message');
            return;
        }
        
        const url = `/chatroom/messages?session_id=${sessionId}${lastTimestamp ? '&last_timestamp=' + encodeURIComponent(lastTimestamp) : ''}`;
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data.messages && data.data.messages.length > 0) {
                // Update last timestamp
                lastTimestamp = data.data.messages[data.data.messages.length - 1].timestamp;
                
                // Add new messages to chat
                data.data.messages.forEach(message => {
                    // Check if message is already displayed (to avoid duplicates)
                    const messageId = `msg-${message.message_id}`;
                    
                    // Only add the message if we haven't displayed it before
                    if (!document.getElementById(messageId) && !displayedMessageIds.has(message.message_id)) {
                        addMessageToChat(message);
                        // Add to our tracking Set
                        displayedMessageIds.add(message.message_id);
                    }
                });
                
                // Update mood data if available
                if (data.data.mood_data) {
                    updateMoodData(data.data.mood_data);
                }
                
                // Scroll to bottom
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error getting messages:', error);
        });
    }
    
    // Add message to chat
    function addMessageToChat(message) {
        // Skip jika pesan sudah ada di DOM atau di Set tracking
        const messageId = message.message_id;
        if (document.getElementById(`msg-${messageId}`) || displayedMessageIds.has(messageId)) {
            console.log('Skipping duplicate message:', messageId);
            return;
        }
        
        // Tambahkan ke Set tracking
        displayedMessageIds.add(messageId);
        console.log('Adding new message to chat:', messageId);
        
        const messageElement = document.createElement('div');
        messageElement.id = `msg-${messageId}`;
        messageElement.className = 'message mb-4';
        messageElement.setAttribute('data-message-id', messageId);
        
        if (message.sender_id === '0') {
            // System message
            messageElement.innerHTML = `
                <div class="bg-blue-100 p-3 rounded-lg inline-block max-w-[80%] mx-auto text-center">
                    <div class="text-xs text-blue-600 font-bold mb-1">SISTEM</div>
                    <div class="text-sm">${message.text}</div>
                    <div class="text-xs text-gray-500 mt-1">${new Date(message.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                </div>
            `;
        } else if (message.sender_id === userId) {
            // Sender message (current user)
            messageElement.innerHTML = `
                <div class="flex justify-end mb-2">
                    <div class="bg-[#f57c00] text-white p-3 rounded-lg max-w-[80%]">
                        <div class="text-sm">${message.text}</div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-white opacity-75">${new Date(message.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                ${message.emotion_label === 'positif' ? 'bg-green-200 text-green-800' : 
                                (message.emotion_label === 'negatif' ? 'bg-red-200 text-red-800' : 
                                'bg-blue-200 text-blue-800')}">
                                ${message.emotion_label ? message.emotion_label.charAt(0).toUpperCase() + message.emotion_label.slice(1) : 'Netral'}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Receiver message (other user)
            messageElement.innerHTML = `
                <div class="flex mb-2">
                    <div class="bg-gray-200 p-3 rounded-lg max-w-[80%]">
                        <div class="text-xs text-gray-600 font-bold mb-1">${message.username || 'User'}</div>
                        <div class="text-sm">${message.text}</div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-500">${new Date(message.timestamp).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                ${message.emotion_label === 'positif' ? 'bg-green-200 text-green-800' : 
                                (message.emotion_label === 'negatif' ? 'bg-red-200 text-red-800' : 
                                'bg-blue-200 text-blue-800')}">
                                ${message.emotion_label ? message.emotion_label.charAt(0).toUpperCase() + message.emotion_label.slice(1) : 'Netral'}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        chatMessages.appendChild(messageElement);
    }
    
    // Update mood data
    function updateMoodData(moodData) {
        // Update current mood
        if (moodData.team_mood) {
            const moodEmoji = moodData.team_mood === 'positif' ? '😊' : (moodData.team_mood === 'negatif' ? '😟' : '😐');
            const moodColor = moodData.team_mood === 'positif' ? 'bg-green-100 text-green-800' : 
                             (moodData.team_mood === 'negatif' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800');
            
            const currentMoodElement = document.querySelector('.text-4xl.mr-2');
            if (currentMoodElement) {
                currentMoodElement.textContent = moodEmoji;
            }
            
            const currentMoodTextElement = document.querySelector('.text-lg.font-bold');
            if (currentMoodTextElement) {
                currentMoodTextElement.textContent = moodData.team_mood.charAt(0).toUpperCase() + moodData.team_mood.slice(1);
            }
            
            const currentMoodContainer = document.querySelector('.flex.items-center.justify-center.p-4');
            if (currentMoodContainer) {
                // Remove old color classes
                currentMoodContainer.classList.remove('bg-green-100', 'bg-red-100', 'bg-blue-100', 'text-green-800', 'text-red-800', 'text-blue-800');
                // Add new color classes
                const newClasses = moodColor.split(' ');
                newClasses.forEach(cls => currentMoodContainer.classList.add(cls));
            }
        }
        
        // Update mood stats
        if (moodData.emotion_summary) {
            const emotionSummary = typeof moodData.emotion_summary === 'string' ? 
                JSON.parse(moodData.emotion_summary) : moodData.emotion_summary;
            
            const positifCount = emotionSummary.positif || 0;
            const netralCount = emotionSummary.netral || 0;
            const negatifCount = emotionSummary.negatif || 0;
            
            const total = positifCount + netralCount + negatifCount;
            const positifPercent = total > 0 ? Math.round((positifCount / total) * 100) : 0;
            const netralPercent = total > 0 ? Math.round((netralCount / total) * 100) : 0;
            const negatifPercent = total > 0 ? Math.round((negatifCount / total) * 100) : 0;
            
            // Update positif stats
            const positifCountElement = document.querySelector('.text-xs.font-medium.text-green-800 + .text-xs.font-medium.text-green-800');
            if (positifCountElement) {
                positifCountElement.textContent = `${positifCount} (${positifPercent}%)`;
            }
            
            const positifBarElement = document.querySelector('.bg-green-500.h-2.rounded-full');
            if (positifBarElement) {
                positifBarElement.style.width = `${positifPercent}%`;
            }
            
            // Update netral stats
            const netralCountElement = document.querySelector('.text-xs.font-medium.text-blue-800 + .text-xs.font-medium.text-blue-800');
            if (netralCountElement) {
                netralCountElement.textContent = `${netralCount} (${netralPercent}%)`;
            }
            
            const netralBarElement = document.querySelector('.bg-blue-500.h-2.rounded-full');
            if (netralBarElement) {
                netralBarElement.style.width = `${netralPercent}%`;
            }
            
            // Update negatif stats
            const negatifCountElement = document.querySelector('.text-xs.font-medium.text-red-800 + .text-xs.font-medium.text-red-800');
            if (negatifCountElement) {
                negatifCountElement.textContent = `${negatifCount} (${negatifPercent}%)`;
            }
            
            const negatifBarElement = document.querySelector('.bg-red-500.h-2.rounded-full');
            if (negatifBarElement) {
                negatifBarElement.style.width = `${negatifPercent}%`;
            }
        }
    }
    
    // Fungsi untuk menghapus pesan duplikat dari DOM
    function removeDuplicateMessagesFromDOM() {
        // Dapatkan semua pesan dengan atribut data-message-id
        const allMessages = document.querySelectorAll('.message[data-message-id]');
        const seenIds = new Set();
        
        allMessages.forEach(element => {
            const messageId = element.getAttribute('data-message-id');
            if (seenIds.has(messageId)) {
                // Ini adalah duplikat, hapus dari DOM
                console.log('Removing duplicate message from DOM:', messageId);
                element.remove();
            } else {
                // Tambahkan ke set untuk melacak
                seenIds.add(messageId);
            }
        });
    }
    
    // Panggil fungsi pembersihan duplikat setiap 10 detik
    setInterval(removeDuplicateMessagesFromDOM, 1000);
    
    // Set up polling for new messages
    const POLLING_INTERVAL = 100; // 5 seconds
    setInterval(getNewMessages, POLLING_INTERVAL);
});
