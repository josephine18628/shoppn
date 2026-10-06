// js/validate.js
// Client-side form validation with regex — runs BEFORE the browser submits
// the form, giving instant feedback. The PHP side (register.php/login.php)
// still re-validates everything server-side — this is for UX only and can
// be bypassed, so it is never the only line of defense.

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Matches the "code" + "number" split used on the register form: digits,
// spaces, and dashes only, 6 to 15 characters (the country code itself
// is picked from a dropdown, so it doesn't need to be validated here).
const phoneRegex = /^[0-9\s\-]{6,15}$/;

// Mirrors the PHP checkPassword() rule exactly: 8+ characters, at least
// one letter, one digit, and one symbol.
const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

document.addEventListener('DOMContentLoaded', function () {
    wireRegisterForm();
    wireLoginForm();
});

function wireRegisterForm() {
    const form = document.getElementById('register-form');
    if (!form) return; // not on this page — nothing to do

    form.addEventListener('submit', function (e) {
        clearFieldErrors(form);
        let isValid = true;

        const firstname = document.getElementById('firstname-input').value.trim();
        const lastname = document.getElementById('lastname-input').value.trim();
        const email = document.getElementById('email-input').value.trim();
        const password = document.getElementById('password-input').value;
        const confirmPassword = document.getElementById('confirm-password-input').value;
        const country = document.getElementById('country-input').value;
        const city = document.getElementById('city-input').value.trim();
        const phoneCode = document.getElementById('phone-code-input').value;
        const contact = document.getElementById('contact-input').value.trim();

        if (firstname.length < 2) {
            showFieldError('firstname-error', 'First name must be at least 2 characters.');
            isValid = false;
        }

        if (lastname.length < 2) {
            showFieldError('lastname-error', 'Last name must be at least 2 characters.');
            isValid = false;
        }

        if (!emailRegex.test(email)) {
            showFieldError('email-error', 'Enter a valid email address.');
            isValid = false;
        }

        if (!passwordRegex.test(password)) {
            showFieldError('password-error', 'Password needs 8+ characters, a letter, a number, and a symbol.');
            isValid = false;
        }

        if (password !== confirmPassword) {
            showFieldError('confirm-password-error', 'Passwords do not match.');
            isValid = false;
        }

        if (!country) {
            showFieldError('country-error', 'Please select your country.');
            isValid = false;
        }

        if (city.length < 1) {
            showFieldError('city-error', 'City is required.');
            isValid = false;
        }

        if (!phoneCode) {
            showFieldError('phone-code-error', 'Please select a country code.');
            isValid = false;
        }

        if (!phoneRegex.test(contact)) {
            showFieldError('contact-error', 'Enter a valid phone number (digits only, 6–15 characters).');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            return;
        }

        // Valid — let it submit normally, just show a loading state.
        const btn = document.getElementById('signup-button');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Creating account...';
        }
    });

    // Bonus: live feedback the instant confirm-password stops matching.
    const password = document.getElementById('password-input');
    const confirmPassword = document.getElementById('confirm-password-input');
    if (password && confirmPassword) {
        confirmPassword.addEventListener('input', function () {
            if (confirmPassword.value && password.value !== confirmPassword.value) {
                showFieldError('confirm-password-error', 'Passwords do not match.');
            } else {
                showFieldError('confirm-password-error', '');
            }
        });
    }
}

function wireLoginForm() {
    const form = document.getElementById('login-form');
    if (!form) return; // not on this page — nothing to do

    form.addEventListener('submit', function (e) {
        clearFieldErrors(form);
        let isValid = true;

        const email = document.getElementById('email-input').value.trim();
        const password = document.getElementById('password-input').value;

        if (!emailRegex.test(email)) {
            showFieldError('email-error', 'Enter a valid email address.');
            isValid = false;
        }

        // Login only checks that something was typed — password STRENGTH
        // is only enforced at registration, not at login (an old, already
        // weaker password still needs to be allowed to sign in).
        if (password.length < 1) {
            showFieldError('password-error', 'Password is required.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            return;
        }

        const btn = document.getElementById('login-button');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Logging in...';
        }
    });
}

function showFieldError(id, message) {
    const el = document.getElementById(id);
    if (el) el.textContent = message;
}

function clearFieldErrors(form) {
    form.querySelectorAll('.field-error').forEach(function (el) {
        el.textContent = '';
    });
}