// Login page animations
document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to buttons
    addRippleEffect();
    
    // Animate form elements
    animateFormElements();
    
    // Add floating effect to illustration
    addFloatingEffect();
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

// Add subtle hover effects to form elements
const inputs = document.querySelectorAll('input');
inputs.forEach(input => {
    input.addEventListener('focus', () => {
        input.parentElement.classList.add('input-focused');
    });
    
    input.addEventListener('blur', () => {
        input.parentElement.classList.remove('input-focused');
    });
});

// Function to animate form elements with a staggered effect
function animateFormElements() {
    const loginForm = document.querySelector('form');
    if (loginForm) {
        const formElements = loginForm.querySelectorAll('.form-group, .form-footer, .form-header');
        
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
