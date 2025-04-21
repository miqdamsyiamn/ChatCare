// ChatCare Main Script

// Emotion Detection Keywords
const emotionKeywords = {
    positive: [
        'senang', 'bahagia', 'gembira', 'suka', 'cinta', 'sayang', 'bagus', 'hebat', 
        'keren', 'mantap', 'asik', 'oke', 'yes', 'berhasil', 'sukses', 'menang',
        'wow', 'keren', 'awesome', 'nice', 'good', 'great', 'perfect', 'cool',
        '😊', '😄', '😃', '😀', '😁', '🥰', '😍', '🤗', '👍', '💪', '✨', '⭐'
    ],
    neutral: [
        'biasa', 'normal', 'standar', 'lumayan', 'cukup', 'mungkin', 'bisa', 'iya',
        'ok', 'baik', 'hmm', 'oh', 'begitu', 'gitu', 'ya', 'yah', 'yup', 'nah',
        '🤔', '😐', '😶', '🤨', '🙂', '👌', '🆗', '💭'
    ],
    negative: [
        'sedih', 'marah', 'kesal', 'kecewa', 'benci', 'sebal', 'bosan', 'lelah',
        'capek', 'males', 'bete', 'gabut', 'gagal', 'kalah', 'rugi', 'susah',
        'sulit', 'rumit', 'bingung', 'pusing', 'stress', 'takut', 'khawatir',
        '😢', '😭', '😤', '😠', '😡', '😞', '😔', '😣', '😖', '😫', '😩', '😰'
    ]
};

// Emoji picker configuration
const emojiList = {
    positive: ['😊', '😄', '😃', '😀', '😁', '🥰', '😍', '🤗', '👍', '💪', '✨', '⭐'],
    neutral: ['🤔', '😐', '😶', '🤨', '🙂', '👌', '🆗', '💭'],
    negative: ['😢', '😭', '😤', '😠', '😡', '😞', '😔', '😣', '😖', '😫', '😩', '😰']
};

// Local Storage Keys
const STORAGE_KEYS = {
    MESSAGES: 'chatcare_messages',
    SESSION: 'chatcare_session'
};

// Initialize storage if empty
if (!localStorage.getItem(STORAGE_KEYS.MESSAGES)) {
    localStorage.setItem(STORAGE_KEYS.MESSAGES, JSON.stringify([]));
}

// Enhanced Emotion Detection
function detectEmotion(text) {
    if (!text) return 'neutral';
    text = text.toLowerCase();
    
    let scores = {
        positive: 0,
        neutral: 0,
        negative: 0
    };

    // Check for swear words first (immediate negative)
    const swearWords = ['ajg', 'anj', 'anjg'];
    if (swearWords.some(word => text.includes(word))) {
        return 'negative';
    }

    // Count emotion occurrences
    for (const emotion in emotionKeywords) {
        emotionKeywords[emotion].forEach(keyword => {
            if (text.includes(keyword.toLowerCase())) {
                scores[emotion]++;
            }
        });
    }

    // Weight the scores
    scores.negative *= 1.5; // Give more weight to negative emotions
    
    // Get the dominant emotion
    const dominant = Object.entries(scores).sort((a, b) => b[1] - a[1])[0][0];
    
    // If no clear emotion is detected, return neutral
    return scores[dominant] > 0 ? dominant : 'neutral';
}

window.ChatCare = {
    sessions: JSON.parse(localStorage.getItem('chatcare_sessions') || '[]'),
    messages: JSON.parse(localStorage.getItem('chatcare_messages') || '[]'),

    emotionKeywords: emotionKeywords,

    startSession(session) {
        this.sessions.push(session);
        localStorage.setItem('chatcare_sessions', JSON.stringify(this.sessions));
        this.enableChatInput();
        return true;
    },

    endSession(session) {
        const index = this.sessions.findIndex(s => s.id === session.id);
        if (index !== -1) {
            this.sessions[index] = session;
            localStorage.setItem('chatcare_sessions', JSON.stringify(this.sessions));
            return true;
        }
        return false;
    },

    deleteSession(sessionId) {
        // Remove session
        this.sessions = this.sessions.filter(s => s.id !== sessionId);
        localStorage.setItem('chatcare_sessions', JSON.stringify(this.sessions));
        
        // Remove associated messages
        this.messages = this.messages.filter(m => m.sessionId !== sessionId);
        localStorage.setItem('chatcare_messages', JSON.stringify(this.messages));
        
        return true;
    },

    getSessions(startDate = null, endDate = null) {
        let filteredSessions = [...this.sessions];
        
        if (startDate && endDate) {
            filteredSessions = filteredSessions.filter(session => {
                const sessionStart = new Date(session.startTime);
                return sessionStart >= startDate && sessionStart <= endDate;
            });
        }
        
        return filteredSessions.map(session => {
            const sessionMessages = this.messages.filter(msg => msg.sessionId === session.id);
            const mood = this.calculateTeamMood(sessionMessages);
            return {
                ...session,
                messages: sessionMessages,
                mood
            };
        }).sort((a, b) => new Date(b.startTime) - new Date(a.startTime)); // Sort by newest first
    },

    saveMessage(message) {
        this.messages.push(message);
        localStorage.setItem('chatcare_messages', JSON.stringify(this.messages));
        
        // If message belongs to a session, add it to that session
        if (message.sessionId) {
            const session = this.sessions.find(s => s.id === message.sessionId);
            if (session) {
                if (!session.messages) session.messages = [];
                session.messages.push(message);
                localStorage.setItem('chatcare_sessions', JSON.stringify(this.sessions));
            }
        }
        
        return true;
    },

    getMessages(startDate = null, endDate = null) {
        if (!startDate || !endDate) {
            return this.messages;
        }
        
        return this.messages.filter(message => {
            const messageDate = new Date(message.timestamp);
            return messageDate >= startDate && messageDate <= endDate;
        });
    },

    detectEmotion(text) {
        const words = text.toLowerCase().split(/\s+/);
        const scores = {
            positive: 0,
            neutral: 0,
            negative: 0
        };

        words.forEach(word => {
            for (const [emotion, keywords] of Object.entries(this.emotionKeywords)) {
                if (keywords.some(keyword => word.includes(keyword.toLowerCase()))) {
                    scores[emotion]++;
                }
            }
        });

        // Give more weight to negative emotions
        scores.negative *= 1.2;

        const dominant = Object.entries(scores)
            .sort((a, b) => b[1] - a[1])[0][0];

        return scores[dominant] > 0 ? dominant : 'neutral';
    },

    calculateTeamMood(messages) {
        if (!messages || !messages.length) {
            return {
                dominant: 'neutral',
                emoji: '😐',
                percentages: {
                    positive: 0,
                    negative: 0,
                    neutral: 100
                },
                recentNegativeCount: 0
            };
        }

        const emotions = messages.map(msg => msg.emotion);
        
        const moodCount = {
            positive: emotions.filter(e => e === 'positive').length,
            negative: emotions.filter(e => e === 'negative').length,
            neutral: emotions.filter(e => e === 'neutral').length
        };
        
        const total = Object.values(moodCount).reduce((a, b) => a + b, 0);
        
        // Calculate recent negative messages (last 5)
        const recentMessages = messages.slice(-5);
        const recentNegativeCount = recentMessages.filter(msg => msg.emotion === 'negative').length;

        const mood = {
            dominant: Object.entries(moodCount).sort((a, b) => b[1] - a[1])[0][0],
            percentages: {
                positive: (moodCount.positive / total) * 100 || 0,
                negative: (moodCount.negative / total) * 100 || 0,
                neutral: (moodCount.neutral / total) * 100 || 0
            },
            recentNegativeCount
        };

        mood.emoji = this.getMoodEmoji(mood.dominant);
        return mood;
    },

    getMoodEmoji(mood) {
        const emojis = {
            positive: ['😊', '😄', '🌟', '✨', '💪'],
            neutral: ['😐', '🤔', '📝', '🎯'],
            negative: ['😔', '😓', '😢', '😮‍💨', '🫂']
        };

        const moodEmojis = emojis[mood] || emojis.neutral;
        return moodEmojis[Math.floor(Math.random() * moodEmojis.length)];
    },

    getMoodText(mood) {
        const texts = {
            positive: [
                'Tim sedang bersemangat! 🌟',
                'Diskusi berjalan sangat baik! ✨',
                'Suasana tim sangat positif! 💪'
            ],
            neutral: [
                'Suasana tim stabil 🤝',
                'Diskusi berjalan normal 📝',
                'Tim dalam mood netral 🎯'
            ],
            negative: [
                'Tim butuh semangat! 💭',
                'Suasana mulai tegang... 😮‍💨',
                'Mood tim sedang menurun 🫂'
            ]
        };

        const options = texts[mood.dominant] || texts.neutral;
        return options[Math.floor(Math.random() * options.length)];
    },

    generateRecommendation(mood) {
        const recommendations = {
            positive: [
                "Wah, semangat timnya lagi bagus banget nih! Teruskan ya 💪",
                "Diskusinya asik banget 😊 Pertahankan yaa!",
                "Tim ini vibes-nya mantul 🔥 Good job!"
            ],
            neutral: [
                "Tim sedang netral. Ayo, jangan ragu untuk saling support 😊",
                "Diskusi masih stabil, jangan lupa beri semangat satu sama lain ya!",
                "Kalau mulai bingung, mungkin bisa dijeda dulu 1–2 menit biar mikir jernih 🤔"
            ],
            negative: [
                "Kelihatannya suasana mulai panas 😓. Yuk tarik napas dulu bareng-bareng.",
                "Diskusi terlihat agak tegang. Gimana kalau istirahat sebentar dulu?",
                "Emosi negatif meningkat. Jangan lupa, kita semua satu tim 🧠❤️",
                "Coba ubah topik sebentar ya, biar mood balik lagi 😊",
                "Terlalu banyak tekanan? Jangan dipendam sendiri yaa, kita bantu bareng-bareng 💬"
            ]
        };

        // Special case for high negative emotions
        if (mood.recentNegativeCount >= 3 || mood.percentages.negative > 50) {
            return recommendations.negative[Math.floor(Math.random() * recommendations.negative.length)];
        }

        // For normal cases, use the dominant mood
        const moodType = mood.dominant;
        const moodRecommendations = recommendations[moodType];
        return moodRecommendations[Math.floor(Math.random() * moodRecommendations.length)];
    },

    getEmojisByCategory(category) {
        if (!category || !this.emotionKeywords) {
            // Default emojis jika tidak ada kategori atau emotionKeywords
            const defaultEmojis = {
                positive: ['😊', '😄', '😃', '😀', '😁', '🥰', '😍', '🤗', '👍', '💪', '✨', '⭐'],
                neutral: ['🤔', '😐', '😶', '🤨', '🙂', '👌', '🆗', '💭'],
                negative: ['😢', '😭', '😤', '😠', '😡', '😞', '😔', '😣', '😖', '😫', '😩', '😰']
            };
            return defaultEmojis[category] || defaultEmojis.positive;
        }
        
        // Filter emoji dari emotionKeywords
        const emojis = this.emotionKeywords[category] ? 
            this.emotionKeywords[category].filter(item => /[\u{1F300}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F900}-\u{1F9FF}\u{1F1E0}-\u{1F1FF}]/u.test(item)) : [];
        
        // Jika tidak ada emoji yang ditemukan, gunakan default
        if (emojis.length === 0) {
            const defaultEmojis = {
                positive: ['😊', '😄', '😃', '😀', '😁', '🥰', '😍', '🤗', '👍', '💪', '✨', '⭐'],
                neutral: ['🤔', '😐', '😶', '🤨', '🙂', '👌', '🆗', '💭'],
                negative: ['😢', '😭', '😤', '😠', '😡', '😞', '😔', '😣', '😖', '😫', '😩', '😰']
            };
            return defaultEmojis[category] || defaultEmojis.positive;
        }
        
        return emojis;
    },

    enableChatInput() {
        document.getElementById('messageInput').disabled = false;
        document.getElementById('sendButton').disabled = false;
        document.querySelectorAll('.emoji-btn').forEach(btn => btn.disabled = false);
    }
};

// Initialize session if needed
if (!localStorage.getItem(STORAGE_KEYS.SESSION)) {
    localStorage.setItem(STORAGE_KEYS.SESSION, JSON.stringify({
        startTime: new Date().toISOString(),
        active: true
    }));
}
