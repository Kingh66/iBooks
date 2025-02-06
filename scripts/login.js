document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('togglePassword');
    const strengthMeter = document.getElementById('strength-meter');

    // Toggle password visibility
    toggleButton.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        toggleButton.textContent = type === 'password' ? 'Show' : 'Hide';
    });

    // Password strength indicator
    passwordInput.addEventListener('input', () => {
        const password = passwordInput.value;
        let strength = 0;

        if (password.length >= 8) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;

        if (strength <= 2) {
            strengthMeter.textContent = 'Weak';
            strengthMeter.className = 'weak';
        } else if (strength <= 4) {
            strengthMeter.textContent = 'Medium';
            strengthMeter.className = 'medium';
        } else {
            strengthMeter.textContent = 'Strong';
            strengthMeter.className = 'strong';
        }
    });
});