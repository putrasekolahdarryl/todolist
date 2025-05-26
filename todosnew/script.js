document.addEventListener('DOMContentLoaded', function () {
    // 1. Toggle password visibility
    const toggle = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const confirmField = document.getElementById('confirm_password');

    if (toggle && passwordField) {
        toggle.addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            passwordField.type = type;
            if (confirmField) confirmField.type = type;
        });
    }

    // 2. Real-time email format validation
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('input', function () {
            const pattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
            this.style.borderColor = pattern.test(this.value) ? 'green' : 'red';
        });
    }

    // 3. Real-time password confirmation check
    if (passwordField && confirmField) {
        confirmField.addEventListener('input', function () {
            if (this.value !== passwordField.value) {
                this.setCustomValidity("Password tidak cocok");
            } else {
                this.setCustomValidity("");
            }
        });
    }
});
