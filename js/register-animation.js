/**
 * Registration page animations
 * Handles animations and interactions for the registration page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to buttons
    addRippleEffect();
    
    // Animate form elements
    animateFormElements();
    
    // Add floating effect to illustration
    addFloatingEffect();
    
    // Add form validation visual feedback
    setupFormValidation();
});

// Function to add floating effect to illustration
function addFloatingEffect() {
    const illustration = document.querySelector('.illustration-container img');
    if (illustration) {
        illustration.classList.add('animate-float');
    }
}

// Function to add ripple effect to buttons
function addRippleEffect() {
    const buttons = document.querySelectorAll('button');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const x = e.clientX - e.target.getBoundingClientRect().left;
            const y = e.clientY - e.target.getBoundingClientRect().top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
}

// Function to animate form elements with a staggered effect
function animateFormElements() {
    const registerForm = document.querySelector('form');
    if (registerForm) {
        const formElements = registerForm.querySelectorAll('.form-group, .form-footer, .form-header');
        
        formElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(20px)';
            element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            element.style.transitionDelay = `${0.2 + (index * 0.1)}s`;
            
            setTimeout(() => {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, 100);
        });
    }
}

// Function to setup form validation visual feedback
function setupFormValidation() {
    const form = document.querySelector('form');
    const inputs = document.querySelectorAll('input');
    
    if (!form) return;
    
    // Add input focus effects
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('input-focused');
        });
        
        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('input-focused');
            
            // Add validation class if input has value
            if (input.value.trim() !== '') {
                validateInput(input);
            }
        });
        
        // Add keyup validation for password fields
        if (input.type === 'password') {
            input.addEventListener('keyup', () => {
                if (input.id === 'password') {
                    validatePasswordStrength(input);
                } else if (input.id === 'confirm_password') {
                    validatePasswordMatch(input);
                }
            });
        }
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (input.value.trim() === '') {
                isValid = false;
                showError(input, 'Field tidak boleh kosong');
            } else {
                validateInput(input);
            }
        });
        
        if (!isValid) {
            e.preventDefault();
        }
    });
}

// Validate individual input
function validateInput(input) {
    switch(input.type) {
        case 'email':
            validateEmail(input);
            break;
        case 'password':
            if (input.id === 'password') {
                validatePasswordStrength(input);
            } else if (input.id === 'confirm_password') {
                validatePasswordMatch(input);
            }
            break;
        default:
            if (input.value.trim() !== '') {
                showSuccess(input);
            } else {
                showError(input, 'Field tidak boleh kosong');
            }
    }
}

// Validate email format
function validateEmail(input) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    
    if (re.test(input.value.trim())) {
        showSuccess(input);
    } else {
        showError(input, 'Format email tidak valid');
    }
}

// Validate password strength
function validatePasswordStrength(input) {
    const password = input.value.trim();
    
    if (password.length < 6) {
        showError(input, 'Password minimal 6 karakter');
    } else {
        showSuccess(input);
    }
}

// Validate password match
function validatePasswordMatch(input) {
    const password = document.querySelector('#password');
    const confirmPassword = input;
    
    if (password && password.value !== confirmPassword.value) {
        showError(confirmPassword, 'Password tidak sama');
    } else {
        showSuccess(confirmPassword);
    }
}

// Show error message
function showError(input, message) {
    const formGroup = input.parentElement;
    formGroup.classList.add('error');
    formGroup.classList.remove('success');
    
    const errorElement = formGroup.querySelector('.error-message');
    if (errorElement) {
        errorElement.textContent = message;
    }
}

// Show success
function showSuccess(input) {
    const formGroup = input.parentElement;
    formGroup.classList.add('success');
    formGroup.classList.remove('error');
}
