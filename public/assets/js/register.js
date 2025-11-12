import {
    evaluatePasswordStrength,
    updateEntropy,
    bindPasswordGenerator
} from '/assets/js/modules/passwordUtils.js';

function $(sel) {
    return document.querySelector(sel);
}

function pick(...selectors) {
    for (const s of selectors) {
        const el = $(s);
        if (el) return el;
    }
    return null;
}

document.addEventListener('DOMContentLoaded', () => {
    // Champs (double mapping pour s'adapter)
    const firstname = pick('#firstname', '#registration_form_firstname');
    const lastname = pick('#lastname', '#registration_form_lastname');
    const email = pick('#email', '#registration_form_email');
    const password = pick('#password', '#registration_form_plainPassword');
    const password2 = pick('#password2', '#registration_form_password2',
        '#registration_form_plainPassword_confirm');
    const terms = pick('#login-register-terms', '#registration_form_agreeTerms');
    const submitBtn = pick('#submit-button', 'button[type="submit"]');
    const entropyEl = pick('#entropy', '#password-entropy');

    // Générateur (facultatif)
    const generateBtn = $('#generate-password');
    bindPasswordGenerator(generateBtn, password || undefined);

    // Toggle password (si tu as mis le bouton #togglePassword)
    const toggleBtn = $('#togglePassword');
    if (toggleBtn && password && password2) {
        toggleBtn.addEventListener('click', () => {
            const show = (password.type === 'password');
            password.type = show ? 'text' : 'password';
            password2.type = show ? 'text' : 'password';
            const icon = toggleBtn.querySelector('i');
            if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    const state = {
        firstname: false,
        lastname: false,
        email: false,
        passPair: false,
        passStrong: false,
        terms: !!(terms && terms.checked),
    };

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function setSubmit() {
        if (!submitBtn) return;
        const ok = state.firstname && state.lastname && state.email && state.passPair && state.passStrong &&
            state.terms;
        submitBtn.toggleAttribute('disabled', !ok);
    }

    function onFirstname() {
        state.firstname = !!(firstname && firstname.value.trim().length >= 2);
        setSubmit();
    }

    function onLastname() {
        state.lastname = !!(lastname && lastname.value.trim().length >= 2);
        setSubmit();
    }

    function onEmail() {
        state.email = !!(email && emailRegex.test(email.value.trim()));
        setSubmit();
    }

    function onPassword() {
        if (!password) return;
        const strengthLabel = evaluatePasswordStrength(password.value);
        state.passStrong = updateEntropy(entropyEl, strengthLabel); // true si Fort / Très fort
        onPasswordPair(); // recalcule cohérence
    }

    function onPasswordPair() {
        state.passPair = !!(password && password2 && password.value !== '' && password.value === password2
            .value);
        setSubmit();
    }

    function onTerms() {
        state.terms = !!(terms && terms.checked);
        setSubmit();
    }

    // Bind
    firstname && firstname.addEventListener('input', onFirstname);
    lastname && lastname.addEventListener('input', onLastname);
    email && email.addEventListener('input', onEmail);
    password && password.addEventListener('input', onPassword);
    password2 && password2.addEventListener('input', onPasswordPair);
    terms && terms.addEventListener('change', onTerms);

    // Init
    onFirstname();
    onLastname();
    onEmail();
    onPassword();
    onPasswordPair();
    onTerms();
});