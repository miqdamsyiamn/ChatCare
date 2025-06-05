/**
 * ChatCare - Utility JavaScript
 * 
 * Utility functions for the ChatCare application
 */

// Namespace for ChatCare utilities
const ChatCareUtils = {
    /**
     * Format a date string
     * @param {string|Date} dateString - Date string or Date object
     * @param {boolean} includeTime - Whether to include time
     * @returns {string} Formatted date string
     */
    formatDate: function(dateString, includeTime = false) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        
        // Check if date is valid
        if (isNaN(date.getTime())) return '';
        
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        
        if (includeTime) {
            options.hour = '2-digit';
            options.minute = '2-digit';
        }
        
        return date.toLocaleDateString('id-ID', options);
    },
    
    /**
     * Format a time string
     * @param {string|Date} dateString - Date string or Date object
     * @returns {string} Formatted time string
     */
    formatTime: function(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        
        // Check if date is valid
        if (isNaN(date.getTime())) return '';
        
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    },
    
    /**
     * Calculate time elapsed since a date
     * @param {string|Date} dateString - Date string or Date object
     * @returns {string} Time elapsed string
     */
    timeElapsed: function(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        
        // Check if date is valid
        if (isNaN(date.getTime())) return '';
        
        const now = new Date();
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHour = Math.floor(diffMin / 60);
        const diffDay = Math.floor(diffHour / 24);
        
        if (diffSec < 60) {
            return 'Baru saja';
        } else if (diffMin < 60) {
            return `${diffMin} menit yang lalu`;
        } else if (diffHour < 24) {
            return `${diffHour} jam yang lalu`;
        } else if (diffDay < 30) {
            return `${diffDay} hari yang lalu`;
        } else {
            return this.formatDate(date);
        }
    },
    
    /**
     * Truncate text to a specified length
     * @param {string} text - Text to truncate
     * @param {number} length - Maximum length
     * @returns {string} Truncated text
     */
    truncateText: function(text, length = 100) {
        if (!text) return '';
        
        if (text.length <= length) return text;
        
        return text.substring(0, length) + '...';
    },
    
    /**
     * Format a number with commas as thousands separator
     * @param {number} number - Number to format
     * @returns {string} Formatted number
     */
    formatNumber: function(number) {
        if (number === null || number === undefined) return '0';
        
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    },
    
    /**
     * Get emotion emoji based on emotion label
     * @param {string} emotion - Emotion label (positif, netral, negatif)
     * @returns {string} Emoji
     */
    getEmotionEmoji: function(emotion) {
        if (!emotion) return '😐';
        
        switch (emotion.toLowerCase()) {
            case 'positif':
                return '😊';
            case 'negatif':
                return '😟';
            case 'netral':
            default:
                return '😐';
        }
    },
    
    /**
     * Get emotion color based on emotion label
     * @param {string} emotion - Emotion label (positif, netral, negatif)
     * @returns {Object} Color object with background and text colors
     */
    getEmotionColor: function(emotion) {
        if (!emotion) {
            return {
                bg: 'bg-blue-100',
                text: 'text-blue-800'
            };
        }
        
        switch (emotion.toLowerCase()) {
            case 'positif':
                return {
                    bg: 'bg-green-100',
                    text: 'text-green-800'
                };
            case 'negatif':
                return {
                    bg: 'bg-red-100',
                    text: 'text-red-800'
                };
            case 'netral':
            default:
                return {
                    bg: 'bg-blue-100',
                    text: 'text-blue-800'
                };
        }
    },
    
    /**
     * Show a notification
     * @param {string} message - Notification message
     * @param {string} type - Notification type (success, error, info, warning)
     * @param {number} duration - Duration in milliseconds
     */
    showNotification: function(message, type = 'info', duration = 3000) {
        // Create notification container if it doesn't exist
        let container = document.getElementById('notification-container');
        
        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 flex flex-col items-end space-y-2';
            document.body.appendChild(container);
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'transform transition-all duration-300 ease-in-out translate-x-full';
        
        // Set notification style based on type
        let bgColor, textColor, icon;
        
        switch (type) {
            case 'success':
                bgColor = 'bg-green-100';
                textColor = 'text-green-800';
                icon = '<i class="fas fa-check-circle"></i>';
                break;
            case 'error':
                bgColor = 'bg-red-100';
                textColor = 'text-red-800';
                icon = '<i class="fas fa-exclamation-circle"></i>';
                break;
            case 'warning':
                bgColor = 'bg-yellow-100';
                textColor = 'text-yellow-800';
                icon = '<i class="fas fa-exclamation-triangle"></i>';
                break;
            case 'info':
            default:
                bgColor = 'bg-blue-100';
                textColor = 'text-blue-800';
                icon = '<i class="fas fa-info-circle"></i>';
                break;
        }
        
        notification.className += ` ${bgColor} ${textColor} px-4 py-3 rounded-lg shadow-md flex items-center max-w-md`;
        
        // Set notification content
        notification.innerHTML = `
            <div class="mr-3 text-xl">${icon}</div>
            <div class="flex-1">${message}</div>
            <button class="ml-4 text-gray-500 hover:text-gray-700 focus:outline-none" onclick="this.parentNode.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add notification to container
        container.appendChild(notification);
        
        // Animate notification
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 10);
        
        // Remove notification after duration
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            
            // Remove element after animation
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, duration);
    },
    
    /**
     * Show a confirmation dialog
     * @param {string} message - Confirmation message
     * @param {Function} onConfirm - Callback function when confirmed
     * @param {Function} onCancel - Callback function when canceled
     */
    showConfirmation: function(message, onConfirm, onCancel) {
        // Create modal container if it doesn't exist
        let modalContainer = document.getElementById('modal-container');
        
        if (!modalContainer) {
            modalContainer = document.createElement('div');
            modalContainer.id = 'modal-container';
            document.body.appendChild(modalContainer);
        }
        
        // Create modal element
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 flex items-center justify-center z-50';
        
        // Set modal content
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            <div class="bg-white rounded-lg max-w-md w-full mx-4 overflow-hidden shadow-xl transform transition-all">
                <div class="p-6">
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi</h3>
                        <p class="text-gray-700">${message}</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button id="confirm-button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#f57c00] text-base font-medium text-white hover:bg-[#e65100] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#f57c00] sm:ml-3 sm:w-auto sm:text-sm">
                        Ya
                    </button>
                    <button id="cancel-button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        `;
        
        // Add modal to container
        modalContainer.appendChild(modal);
        
        // Handle confirm button click
        const confirmButton = modal.querySelector('#confirm-button');
        confirmButton.addEventListener('click', function() {
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
            modal.remove();
        });
        
        // Handle cancel button click
        const cancelButton = modal.querySelector('#cancel-button');
        cancelButton.addEventListener('click', function() {
            if (typeof onCancel === 'function') {
                onCancel();
            }
            modal.remove();
        });
        
        // Handle click outside modal
        modal.querySelector('.fixed.inset-0').addEventListener('click', function() {
            if (typeof onCancel === 'function') {
                onCancel();
            }
            modal.remove();
        });
    },
    
    /**
     * Copy text to clipboard
     * @param {string} text - Text to copy
     * @returns {boolean} Success
     */
    copyToClipboard: function(text) {
        // Create temporary textarea
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        
        // Select and copy text
        textarea.select();
        let success = false;
        
        try {
            success = document.execCommand('copy');
            if (success) {
                this.showNotification('Teks berhasil disalin ke clipboard!', 'success');
            } else {
                this.showNotification('Gagal menyalin teks ke clipboard.', 'error');
            }
        } catch (error) {
            console.error('Error copying to clipboard:', error);
            this.showNotification('Gagal menyalin teks ke clipboard.', 'error');
        }
        
        // Remove temporary textarea
        document.body.removeChild(textarea);
        
        return success;
    },
    
    /**
     * Validate form inputs
     * @param {HTMLFormElement} form - Form element
     * @returns {boolean} Is valid
     */
    validateForm: function(form) {
        if (!form) return false;
        
        let isValid = true;
        
        // Get all required inputs
        const requiredInputs = form.querySelectorAll('[required]');
        
        // Check each required input
        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                
                // Add error class
                input.classList.add('border-red-500');
                
                // Show error message
                let errorMessage = input.getAttribute('data-error-message') || 'Bidang ini wajib diisi';
                
                // Check if error message element already exists
                let errorElement = input.nextElementSibling;
                
                if (!errorElement || !errorElement.classList.contains('error-message')) {
                    errorElement = document.createElement('p');
                    errorElement.className = 'text-red-500 text-xs mt-1 error-message';
                    input.parentNode.insertBefore(errorElement, input.nextSibling);
                }
                
                errorElement.textContent = errorMessage;
            } else {
                // Remove error class
                input.classList.remove('border-red-500');
                
                // Remove error message
                const errorElement = input.nextElementSibling;
                
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.remove();
                }
            }
        });
        
        return isValid;
    },
    
    /**
     * Initialize tooltips
     * @param {string} selector - Selector for tooltip elements
     */
    initTooltips: function(selector = '[data-tooltip]') {
        const tooltipElements = document.querySelectorAll(selector);
        
        tooltipElements.forEach(element => {
            const tooltipText = element.getAttribute('data-tooltip');
            
            if (!tooltipText) return;
            
            // Create tooltip element
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-10 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 pointer-events-none transition-opacity duration-200';
            tooltip.textContent = tooltipText;
            
            // Add tooltip to element
            element.style.position = 'relative';
            element.appendChild(tooltip);
            
            // Position tooltip
            const position = element.getAttribute('data-tooltip-position') || 'top';
            
            switch (position) {
                case 'top':
                    tooltip.style.bottom = '100%';
                    tooltip.style.left = '50%';
                    tooltip.style.transform = 'translateX(-50%) translateY(-5px)';
                    break;
                case 'bottom':
                    tooltip.style.top = '100%';
                    tooltip.style.left = '50%';
                    tooltip.style.transform = 'translateX(-50%) translateY(5px)';
                    break;
                case 'left':
                    tooltip.style.right = '100%';
                    tooltip.style.top = '50%';
                    tooltip.style.transform = 'translateY(-50%) translateX(-5px)';
                    break;
                case 'right':
                    tooltip.style.left = '100%';
                    tooltip.style.top = '50%';
                    tooltip.style.transform = 'translateY(-50%) translateX(5px)';
                    break;
            }
            
            // Show tooltip on hover
            element.addEventListener('mouseenter', () => {
                tooltip.style.opacity = '1';
            });
            
            // Hide tooltip on mouse leave
            element.addEventListener('mouseleave', () => {
                tooltip.style.opacity = '0';
            });
        });
    },
    
    /**
     * Initialize dropdown menus
     * @param {string} selector - Selector for dropdown elements
     */
    initDropdowns: function(selector = '[data-dropdown]') {
        const dropdownElements = document.querySelectorAll(selector);
        
        dropdownElements.forEach(element => {
            const dropdownId = element.getAttribute('data-dropdown');
            const dropdown = document.getElementById(dropdownId);
            
            if (!dropdown) return;
            
            // Toggle dropdown on click
            element.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                // Close all other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== dropdownId) {
                        menu.classList.add('hidden');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('hidden');
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        });
    }
};

// Make utilities available globally
window.ChatCareUtils = ChatCareUtils;
