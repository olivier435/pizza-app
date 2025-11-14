document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#contact-form');
    if (!form) return;

    const fields = {
        firstname: form.elements.namedItem('firstname'),
        lastname:  form.elements.namedItem('lastname'),
        email:     form.elements.namedItem('email'),
        subject:   form.elements.namedItem('subject'),
        message:   form.elements.namedItem('message'),
    };

    const getErrorEl = (name) =>
        form.querySelector(`.invalid-feedback[data-error-for="${name}"]`);

    function setError(name, message) {
        const input = fields[name];
        const errorEl = getErrorEl(name);
        if (!input || !errorEl) return;
        input.classList.add('is-invalid');
        errorEl.textContent = message || '';
    }

    function clearError(name) {
        const input = fields[name];
        const errorEl = getErrorEl(name);
        if (!input || !errorEl) return;
        input.classList.remove('is-invalid');
        errorEl.textContent = '';
    }

    function validateField(name) {
        const input = fields[name];
        if (!input) return true;

        const value = input.value.trim();
        let error = '';

        switch (name) {
            case 'firstname':
                if (value.length < 2) {
                    error = 'Prénom trop court (min. 2 caractères).';
                }
                break;
            case 'lastname':
                if (value.length < 2) {
                    error = 'Nom trop court (min. 2 caractères).';
                }
                break;
            case 'email':
                if (value === '') {
                    error = 'Email obligatoire.';
                } else {
                    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!re.test(value)) {
                        error = 'Email invalide.';
                    }
                }
                break;
            case 'subject':
                if (value.length < 3) {
                    error = 'Sujet trop court (min. 3 caractères).';
                }
                break;
            case 'message':
                if (value.length < 4) {
                    error = 'Message trop court (min. 4 caractères).';
                }
                break;
        }

        if (error) {
            setError(name, error);
            return false;
        }
        clearError(name);
        return true;
    }

    Object.keys(fields).forEach((name) => {
        const input = fields[name];
        if (!input) return;
        input.addEventListener('input', () => validateField(name), { passive: true });
        input.addEventListener('blur', () => validateField(name));
    });

    form.addEventListener('submit', (e) => {
        let ok = true;
        Object.keys(fields).forEach((name) => {
            if (!validateField(name)) ok = false;
        });

        if (!ok) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
    });
});