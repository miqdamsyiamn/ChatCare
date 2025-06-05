/**
 * ChatCare - Notification Handler
 * 
 * Manages notifications and prevents duplicate error messages
 */

// Create a notification manager
const NotificationManager = {
    // Keep track of recent messages to prevent duplicate notifications
    recentMessages: new Set(),
    
    // Track if a notification is currently showing
    isNotificationShowing: false,
    
    // Check if a message is already in the chat (more thorough check)
    isMessageInChat: function(message) {
        if (!message) return false;
        
        // Normalize message for comparison
        const normalizedMessage = message.trim().toLowerCase();
        
        // Jika ada lastSentMessage dan cocok dengan pesan ini, anggap sudah ada
        if (window.lastSentMessage && 
            (window.lastSentMessage.trim().toLowerCase() === normalizedMessage ||
             window.lastSentMessage.trim().toLowerCase().includes(normalizedMessage) ||
             normalizedMessage.includes(window.lastSentMessage.trim().toLowerCase()))) {
            console.log('Message matches lastSentMessage, considering it exists');
            return true;
        }
        
        // Check all message elements in the DOM
        const messageElements = document.querySelectorAll('.message .text-sm');
        let messageExists = false;
        
        messageElements.forEach(element => {
            const elementText = element.textContent.trim().toLowerCase();
            // Use partial matching to be more lenient
            if (elementText === normalizedMessage || 
                elementText.includes(normalizedMessage) || 
                normalizedMessage.includes(elementText)) {
                messageExists = true;
                console.log('Found message match in DOM');
            }
        });
        
        // If not found, also check temporary messages that might be in sending state
        if (!messageExists) {
            const tempMessages = document.querySelectorAll('[id^="temp-"]');
            tempMessages.forEach(tempMsg => {
                const textElement = tempMsg.querySelector('.text-sm');
                if (textElement) {
                    const tempText = textElement.textContent.trim().toLowerCase();
                    // Use partial matching to be more lenient
                    if (tempText === normalizedMessage || 
                        tempText.includes(normalizedMessage) || 
                        normalizedMessage.includes(tempText)) {
                        messageExists = true;
                        console.log('Found message in temporary message element');
                    }
                }
            });
        }
        
        // Jika pesan mengandung kata-kata emosional, kemungkinan besar pesan sudah terkirim
        // meskipun ada error di client-side
        const emotionalWords = ['marah', 'senang', 'sedih', 'bahagia', 'kesal', 'malas', 'benci', 
                               'cinta', 'sayang', 'suka', 'bosan', 'takut', 'cemas', 'khawatir'];
        
        for (const word of emotionalWords) {
            if (normalizedMessage.includes(word)) {
                console.log('Message contains emotional word:', word);
                return true;
            }
        }
        
        return messageExists;
    },
    
    // Show notification only if necessary
    showNotification: function(message, type = 'error') {
        // First check if message input is empty - if so, don't show notification
        const messageInput = document.getElementById('message-input');
        if (messageInput && messageInput.value.trim() === '') {
            console.log('Message input is empty, not showing notification');
            return false;
        }
        
        // Delay checking for message in chat to allow time for it to appear
        const messageText = messageInput.value.trim();
        const checkAndNotify = () => {
            // Double check if message is in chat before showing notification
            if (this.isMessageInChat(messageText)) {
                console.log('Message found in chat after delay, not showing notification:', messageText);
                return false;
            }
            
            // Don't show duplicate notifications
            const messageHash = message + '-' + type;
            if (this.recentMessages.has(messageHash)) {
                console.log('Duplicate notification prevented:', message);
                return false;
            }
            
            // Add to recent messages
            this.recentMessages.add(messageHash);
            
            // Clear old messages after 5 seconds
            setTimeout(() => {
                this.recentMessages.delete(messageHash);
            }, 5000);
            
            // Show the notification
            console.log('Showing notification:', message);
            alert(message);
            this.isNotificationShowing = true;
            
            // Reset notification state after it's closed
            setTimeout(() => {
                this.isNotificationShowing = false;
            }, 100);
            
            return true;
        };
        
        // Wait 1 second before showing error notification to allow message to appear in DOM
        setTimeout(checkAndNotify, 1000);
        return true; // Return true immediately to prevent further processing
    }
};

// Make it available globally
window.NotificationManager = NotificationManager;
