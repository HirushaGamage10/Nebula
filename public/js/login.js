document.addEventListener('DOMContentLoaded', function () {

    document.body.classList.add('loaded');

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    emailInput.addEventListener('input', () => {
        emailInput.classList.remove('is-invalid');
    });

    passwordInput.addEventListener('input', () => {
        passwordInput.classList.remove('is-invalid');
    });

    document.getElementById('loginForm').addEventListener('submit', function (e) {
        let valid = true;

        if (!emailInput.value.trim()) {
            emailInput.classList.add('is-invalid');
            valid = false;
        }

        if (!passwordInput.value.trim()) {
            passwordInput.classList.add('is-invalid');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });

    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    togglePassword.addEventListener('click', () => {
        const hidden = passwordInput.type === 'password';
        passwordInput.type = hidden ? 'text' : 'password';
        togglePasswordIcon.classList.toggle('bi-eye');
        togglePasswordIcon.classList.toggle('bi-eye-slash');
    });

});
