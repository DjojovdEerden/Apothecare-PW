// Main JavaScript for Apothecare

document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity buttons in product detail page
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        const increaseBtn = document.getElementById('increase-quantity');
        const decreaseBtn = document.getElementById('decrease-quantity');
        
        if (increaseBtn) {
            increaseBtn.addEventListener('click', function() {
                const max = parseInt(quantityInput.getAttribute('max'));
                let value = parseInt(quantityInput.value);
                
                if (value < max) {
                    quantityInput.value = value + 1;
                }
            });
        }
        
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });
        }
    }
    
    // Add confirmation for remove/clear actions
    const confirmForms = document.querySelectorAll('form[data-confirm]');
    confirmForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Auto hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Login/Register modal functionality
    const loginButton = document.getElementById('loginButton');
    if (loginButton) {
        loginButton.addEventListener('click', function() {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    }

    // Form submission handling
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageDiv = document.getElementById('loginMessage');
            
            // Clear previous messages
            messageDiv.innerHTML = '';
            messageDiv.className = 'mt-3';
            
            // You can use fetch API for AJAX submission
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.className = 'mt-3 alert alert-success';
                    messageDiv.textContent = data.message;
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    messageDiv.className = 'mt-3 alert alert-danger';
                    messageDiv.textContent = data.message || 'Login failed. Please try again.';
                }
            })
            .catch(error => {
                messageDiv.className = 'mt-3 alert alert-danger';
                messageDiv.textContent = 'An error occurred. Please try again.';
                console.error('Error:', error);
            });
        });
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const messageDiv = document.getElementById('registerMessage');
            
            // Clear previous messages
            messageDiv.innerHTML = '';
            messageDiv.className = 'mt-3';
            
            // Check if passwords match
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                messageDiv.className = 'mt-3 alert alert-danger';
                messageDiv.textContent = 'Passwords do not match!';
                return;
            }
            
            // You can use fetch API for AJAX submission
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.className = 'mt-3 alert alert-success';
                    messageDiv.textContent = data.message;
                    
                    // Reset form
                    this.reset();
                    
                    // Switch to login tab after 2 seconds
                    setTimeout(function() {
                        const loginTab = document.getElementById('login-tab');
                        if (loginTab) {
                            loginTab.click();
                        }
                    }, 2000);
                } else {
                    messageDiv.className = 'mt-3 alert alert-danger';
                    messageDiv.textContent = data.message || 'Registration failed. Please try again.';
                }
            })
            .catch(error => {
                messageDiv.className = 'mt-3 alert alert-danger';
                messageDiv.textContent = 'An error occurred. Please try again.';
                console.error('Error:', error);
            });
        });
    }
});
