/**
 * ChatCare - Enhanced Notifications
 * Provides improved notification animations and interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize notification enhancements
    initNotifications();
});

/**
 * Initialize notification enhancements
 */
function initNotifications() {
    // Add animation to notifications
    animateNotifications();
    
    // Add close buttons to notifications
    addCloseButtons();
    
    // Initialize field validation animations
    initFieldValidation();
    
    // Create confetti for success notifications
    createConfetti();
}

/**
 * Add animations to notifications
 */
function animateNotifications() {
    const notifications = document.querySelectorAll('.notification');
    
    notifications.forEach((notification, index) => {
        // Add staggered animation delay
        notification.style.animationDelay = `${index * 0.1}s`;
        
        // Add subtle hover effect
        notification.addEventListener('mouseenter', () => {
            notification.style.transform = 'translateY(-2px)';
            
            // Different shadow effects based on notification type
            if (notification.classList.contains('notification-success')) {
                notification.style.boxShadow = '0 8px 20px rgba(16, 185, 129, 0.2)';
            } else if (notification.classList.contains('notification-error')) {
                notification.style.boxShadow = '0 8px 20px rgba(239, 68, 68, 0.2)';
            } else {
                notification.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
            }
        });
        
        notification.addEventListener('mouseleave', () => {
            notification.style.transform = 'translateY(0)';
            
            // Restore original shadow based on notification type
            if (notification.classList.contains('notification-success')) {
                notification.style.boxShadow = '0 4px 15px rgba(16, 185, 129, 0.15)';
            } else if (notification.classList.contains('notification-error')) {
                notification.style.boxShadow = '0 4px 15px rgba(239, 68, 68, 0.15)';
            } else {
                notification.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
            }
        });
        
        // Set different auto-hide times based on notification type
        const hideTime = notification.classList.contains('notification-success') ? 12000 : 8000;
        
        // Auto-hide notifications after specified time
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            
            // Remove from DOM after animation completes
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, hideTime);
    });
}

/**
 * Add close buttons to notifications
 */
function addCloseButtons() {
    const notifications = document.querySelectorAll('.notification');
    
    notifications.forEach(notification => {
        // Create close button
        const closeButton = document.createElement('button');
        closeButton.classList.add('notification-close');
        closeButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        `;
        
        // Add close button styles
        closeButton.style.background = 'transparent';
        closeButton.style.border = 'none';
        closeButton.style.cursor = 'pointer';
        closeButton.style.padding = '4px';
        closeButton.style.marginLeft = '8px';
        closeButton.style.opacity = '0.6';
        closeButton.style.transition = 'opacity 0.2s ease';
        
        // Add hover effect
        closeButton.addEventListener('mouseover', () => {
            closeButton.style.opacity = '1';
        });
        
        closeButton.addEventListener('mouseout', () => {
            closeButton.style.opacity = '0.6';
        });
        
        // Add click event to close notification
        closeButton.addEventListener('click', () => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            
            // Remove from DOM after animation completes
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
        
        // Add button to notification
        notification.appendChild(closeButton);
    });
}

/**
 * Initialize field validation animations
 */
function initFieldValidation() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            // Remove error state when input gets focus
            input.classList.remove('input-error');
            
            // Find the associated error message and hide it
            const parentDiv = input.closest('div');
            const errorDiv = parentDiv.querySelector('.field-error');
            if (errorDiv) {
                errorDiv.classList.remove('active');
            }
        });
    });
}

/**
 * Create confetti animation for success notifications
 */
function createConfetti() {
    const successNotifications = document.querySelectorAll('.notification-success');
    
    successNotifications.forEach(notification => {
        const confettiContainer = notification.querySelector('.confetti-container');
        if (!confettiContainer) return;
        
        // Create confetti pieces with different colors
        const colors = ['#10B981', '#34D399', '#6EE7B7', '#A7F3D0', '#D1FAE5'];
        const totalPieces = 50;
        
        for (let i = 0; i < totalPieces; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = Math.random() * 100 + '%';
            confetti.style.animationDelay = Math.random() * 2 + 's';
            confetti.style.animationDuration = Math.random() * 3 + 2 + 's';
            
            // Different shapes for confetti
            if (Math.random() > 0.6) {
                confetti.style.borderRadius = '50%';
            } else if (Math.random() > 0.5) {
                confetti.style.width = '6px';
                confetti.style.height = '12px';
                confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
            }
            
            confettiContainer.appendChild(confetti);
        }
    });
}

/**
 * Enhance field validation with animations
 */
function enhanceFieldValidation() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        // Add focus event
        input.addEventListener('focus', () => {
            // Clear error state when user focuses on input
            const errorContainer = input.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('field-error')) {
                // Don't remove the error immediately, wait for user to type
                input.addEventListener('input', function inputHandler() {
                    // Remove error classes
                    input.classList.remove('input-error');
                    errorContainer.classList.remove('active');
                    
                    // Remove this event listener after first input
                    input.removeEventListener('input', inputHandler);
                });
            }
        });
        
        // Add validation on blur
        input.addEventListener('blur', () => {
            validateInput(input);
        });
    });
    
    // Add pulse animation to error fields
    const errorFields = document.querySelectorAll('.field-error.active');
    errorFields.forEach(field => {
        field.classList.add('error-pulse');
        
        // Remove animation class after it completes
        setTimeout(() => {
            field.classList.remove('error-pulse');
        }, 300);
    });
}

/**
 * Validate input field
 * @param {HTMLElement} input - Input element to validate
 */
function validateInput(input) {
    if (input.value.trim() === '' && input.required) {
        // Show required error
        input.classList.add('input-error');
        const errorContainer = input.nextElementSibling;
        if (errorContainer && errorContainer.classList.contains('field-error')) {
            errorContainer.classList.add('active');
        }
    } else if (input.type === 'email' && input.value.trim() !== '') {
        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(input.value.trim())) {
            input.classList.add('input-error');
            const errorContainer = input.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('field-error')) {
                errorContainer.classList.add('active');
            }
        } else {
            input.classList.add('input-success');
        }
    } else if (input.id === 'password' && input.value.trim() !== '') {
        // Validate password strength
        if (input.value.length < 6) {
            input.classList.add('input-error');
            const errorContainer = input.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('field-error')) {
                errorContainer.classList.add('active');
            }
        } else {
            input.classList.add('input-success');
        }
    } else if (input.id === 'confirm_password' && input.value.trim() !== '') {
        // Validate password match
        const password = document.getElementById('password');
        if (password && password.value !== input.value) {
            input.classList.add('input-error');
            const errorContainer = input.nextElementSibling;
            if (errorContainer && errorContainer.classList.contains('field-error')) {
                errorContainer.classList.add('active');
            }
        } else {
            input.classList.add('input-success');
        }
    } else if (input.value.trim() !== '') {
        // Input has value and passed validation
        input.classList.add('input-success');
    }
}
