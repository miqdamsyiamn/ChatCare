/**
 * ChatCare - Authentication JavaScript
 * 
 * Handles login and authentication functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle login form submission
    const loginForm = document.getElementById('login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            // Basic validation
            if (!username) {
                showError('Username tidak boleh kosong');
                return;
            }
            
            if (!password) {
                showError('Password tidak boleh kosong');
                return;
            }
            
            // Disable form during submission
            const submitButton = loginForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Masuk...';
            
            // Clear previous errors
            hideError();
            
            // Send login request
            fetch('/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to dashboard
                    window.location.href = data.redirect || '/dashboard';
                } else {
                    // Show error message
                    showError(data.message || 'Login gagal. Silakan periksa username dan password Anda.');
                    
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Masuk';
                }
            })
            .catch(error => {
                console.error('Error during login:', error);
                showError('Terjadi kesalahan saat login. Silakan coba lagi.');
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Masuk';
            });
        });
    }
    
    // Function to show error message
    function showError(message) {
        const errorContainer = document.getElementById('error-container');
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.classList.remove('hidden');
            
            // Shake animation for error
            errorContainer.classList.add('shake-animation');
            setTimeout(() => {
                errorContainer.classList.remove('shake-animation');
            }, 500);
        }
    }
    
    // Function to hide error message
    function hideError() {
        const errorContainer = document.getElementById('error-container');
        if (errorContainer) {
            errorContainer.textContent = '';
            errorContainer.classList.add('hidden');
        }
    }
    
    // Add shake animation style
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .shake-animation {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
    `;
    document.head.appendChild(style);
    
    // Handle password visibility toggle
    const passwordToggle = document.getElementById('password-toggle');
    const passwordField = document.getElementById('password');
    
    if (passwordToggle && passwordField) {
        passwordToggle.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordField.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    }
    
    // Handle "Remember Me" checkbox
    const rememberMeCheckbox = document.getElementById('remember-me');
    
    if (rememberMeCheckbox) {
        // Check if username is stored in localStorage
        const storedUsername = localStorage.getItem('chatcare_username');
        const usernameField = document.getElementById('username');
        
        if (storedUsername && usernameField) {
            usernameField.value = storedUsername;
            rememberMeCheckbox.checked = true;
        }
        
        // Save username to localStorage when checkbox is checked
        loginForm.addEventListener('submit', function() {
            const username = document.getElementById('username').value.trim();
            
            if (rememberMeCheckbox.checked) {
                localStorage.setItem('chatcare_username', username);
            } else {
                localStorage.removeItem('chatcare_username');
            }
        });
    }
    
    // Auto-focus on username field
    const usernameField = document.getElementById('username');
    if (usernameField && !usernameField.value) {
        usernameField.focus();
    }
    
    // Handle logout button
    const logoutButton = document.getElementById('logout-button');
    
    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Send logout request
            fetch('/auth/logout', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to login page
                    window.location.href = '/auth/login';
                } else {
                    console.error('Error during logout:', data.message);
                }
            })
            .catch(error => {
                console.error('Error during logout:', error);
            });
        });
    }
    
    // Handle session timeout
    let sessionTimeoutTimer;
    const sessionTimeout = 30 * 60 * 1000; // 30 minutes
    
    function resetSessionTimeout() {
        clearTimeout(sessionTimeoutTimer);
        sessionTimeoutTimer = setTimeout(function() {
            // Show session timeout warning
            showSessionTimeoutWarning();
        }, sessionTimeout);
    }
    
    // Reset timer on user activity
    ['click', 'keypress', 'scroll', 'mousemove'].forEach(event => {
        document.addEventListener(event, resetSessionTimeout);
    });
    
    // Initial timer start
    resetSessionTimeout();
    
    // Function to show session timeout warning
    function showSessionTimeoutWarning() {
        // Create warning modal if it doesn't exist
        let warningModal = document.getElementById('session-timeout-modal');
        
        if (!warningModal) {
            warningModal = document.createElement('div');
            warningModal.id = 'session-timeout-modal';
            warningModal.className = 'fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50';
            warningModal.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                    <h3 class="text-lg font-bold mb-4">Sesi Akan Berakhir</h3>
                    <p class="mb-4">Sesi Anda akan berakhir dalam <span id="timeout-countdown">60</span> detik karena tidak ada aktivitas. Apakah Anda ingin tetap login?</p>
                    <div class="flex justify-end space-x-2">
                        <button id="logout-now-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Logout</button>
                        <button id="stay-logged-in-btn" class="px-4 py-2 bg-[#f57c00] text-white rounded hover:bg-[#e65100]">Tetap Login</button>
                    </div>
                </div>
            `;
            document.body.appendChild(warningModal);
            
            // Add event listeners to buttons
            document.getElementById('logout-now-btn').addEventListener('click', function() {
                // Redirect to logout
                window.location.href = '/auth/logout';
            });
            
            document.getElementById('stay-logged-in-btn').addEventListener('click', function() {
                // Hide modal and reset timer
                warningModal.style.display = 'none';
                clearInterval(countdownInterval);
                resetSessionTimeout();
                
                // Send keep-alive request
                fetch('/auth/keep-alive', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            });
        } else {
            warningModal.style.display = 'flex';
        }
        
        // Start countdown
        let countdown = 60;
        const countdownElement = document.getElementById('timeout-countdown');
        
        const countdownInterval = setInterval(function() {
            countdown--;
            if (countdownElement) {
                countdownElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                // Redirect to logout
                window.location.href = '/auth/logout';
            }
        }, 1000);
    }
});
