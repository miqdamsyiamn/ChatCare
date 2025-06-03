/**
 * Fox Animation Interactions
 * This script adds interactive behaviors to the fox animation on the login page
 */
document.addEventListener('DOMContentLoaded', function() {
    // Get the fox container element
    const foxContainer = document.querySelector('.fox-container');
    if (!foxContainer) return;
    
    // Array of messages for the fox to display
    const messages = [
        "Selamat datang di ChatCare!",
        "Silahkan login untuk melanjutkan",
        "Diskusi digital dengan deteksi emosi",
        "Halo! Saya rubah ChatCare",
        "Butuh bantuan?",
        "Login untuk mulai diskusi"
    ];
    
    // Animation classes
    const animations = ['fox-jump', 'fox-spin'];
    
    // Function to set a random message
    function setRandomMessage() {
        const speechBubble = document.querySelector('.fox-speech');
        const randomIndex = Math.floor(Math.random() * messages.length);
        speechBubble.textContent = messages[randomIndex];
    }
    
    // Set initial random message
    setRandomMessage();
    
    // Change message when hovering over the fox
    foxContainer.addEventListener('mouseenter', setRandomMessage);
    
    // Add click interaction
    foxContainer.addEventListener('click', function() {
        // Get a random animation
        const randomAnimation = animations[Math.floor(Math.random() * animations.length)];
        
        // Remove any existing animation classes
        foxContainer.classList.remove(...animations);
        
        // Force a reflow to restart the animation
        void foxContainer.offsetWidth;
        
        // Add the new animation class
        foxContainer.classList.add(randomAnimation);
        
        // Change the message
        setRandomMessage();
        
        // Add a small wiggle to the tail
        const tail = document.querySelector('.fox-tail');
        tail.style.animationDuration = '0.5s';
        
        // Reset tail animation after a short delay
        setTimeout(() => {
            tail.style.animationDuration = '3s';
        }, 500);
        
        // Remove the animation class after it completes
        setTimeout(() => {
            foxContainer.classList.remove(randomAnimation);
        }, 800);
    });
    
    // Add some random movements occasionally
    setInterval(() => {
        if (Math.random() > 0.7) { // 30% chance of random movement
            const randomAnimation = animations[Math.floor(Math.random() * animations.length)];
            
            foxContainer.classList.remove(...animations);
            void foxContainer.offsetWidth;
            foxContainer.classList.add(randomAnimation);
            
            setTimeout(() => {
                foxContainer.classList.remove(randomAnimation);
            }, 800);
        }
    }, 5000); // Check every 5 seconds
    
    // Make eyes follow cursor
    const leftEye = document.querySelector('.fox-eye-left');
    const rightEye = document.querySelector('.fox-eye-right');
    
    document.addEventListener('mousemove', (e) => {
        const foxRect = foxContainer.getBoundingClientRect();
        const foxCenterX = foxRect.left + foxRect.width / 2;
        const foxCenterY = foxRect.top + foxRect.height / 2;
        
        // Calculate angle between mouse and fox
        const deltaX = e.clientX - foxCenterX;
        const deltaY = e.clientY - foxCenterY;
        
        // Limit eye movement
        const maxMove = 2;
        const moveX = Math.max(-maxMove, Math.min(maxMove, deltaX / 100));
        const moveY = Math.max(-maxMove, Math.min(maxMove, deltaY / 100));
        
        // Apply movement to eyes
        if (leftEye && rightEye) {
            leftEye.style.transform = `translate(${moveX}px, ${moveY}px)`;
            rightEye.style.transform = `translate(${moveX}px, ${moveY}px)`;
        }
    });
});
