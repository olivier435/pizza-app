export const PasswordStrength = {
    STRENGTH_VERY_WEAK: 'Très faible',
    STRENGTH_WEAK: 'Faible',
    STRENGTH_MEDIUM: 'Moyen',
    STRENGTH_STRONG: 'Fort',
    STRENGTH_VERY_STRONG: 'Très fort',
};

export function evaluatePasswordStrength(password) {
    let length = password.length;
    if (!length) return PasswordStrength.STRENGTH_VERY_WEAK;

    const passwordChars = {};
    for (let i = 0; i < password.length; i++) {
        const charCode = password.charCodeAt(i);
        passwordChars[charCode] = (passwordChars[charCode] || 0) + 1;
    }

    const chars = Object.keys(passwordChars).length;

    let control = 0,
        digit = 0,
        upper = 0,
        lower = 0,
        symbol = 0,
        other = 0;
    for (let [chr] of Object.entries(passwordChars)) {
        chr = Number(chr);
        if (chr < 32 || chr === 127) {
            control = 33;
        } else if (chr >= 48 && chr <= 57) {
            digit = 10;
        } else if (chr >= 65 && chr <= 90) {
            upper = 26;
        } else if (chr >= 97 && chr <= 122) {
            lower = 26;
        } else if (chr >= 128) {
            other = 128;
        } else {
            symbol = 33;
        }
    }

    const pool = control + digit + upper + lower + other + symbol;
    const entropy = chars * Math.log2(pool) + (length - chars) * Math.log2(chars);

    if (entropy >= 120) return PasswordStrength.STRENGTH_VERY_STRONG;
    if (entropy >= 100) return PasswordStrength.STRENGTH_STRONG;
    if (entropy >= 80) return PasswordStrength.STRENGTH_MEDIUM;
    if (entropy >= 60) return PasswordStrength.STRENGTH_WEAK;
    return PasswordStrength.STRENGTH_VERY_WEAK;
}

// Génération d'un mot de passe fort via crypto
export function generateStrongPassword(length = 24) {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+{}[]<>?=-";
    const array = new Uint32Array(length);
    window.crypto.getRandomValues(array);
    return Array.from(array, x => charset[x % charset.length]).join('');
}

// Affiche « Très faible / Faible / … » et renvoie true si Fort / Très fort
export function updateEntropy(entropyElement, label) {
    if (!entropyElement) return false;

    // Trouve la progress bar
    const progress = document.getElementById('password-progress');

    // Nettoyage des classes du badge
    entropyElement.className = 'badge'; // reset
    let isStrongEnough = false;
    let color = 'secondary';
    let width = 10;

    switch (label) {
        case 'Très faible':
            color = 'danger';
            width = 15;
            break;
        case 'Faible':
            color = 'warning';
            width = 35;
            break;
        case 'Moyen':
            color = 'info';
            width = 60;
            break;
        case 'Fort':
            color = 'primary';
            width = 85;
            isStrongEnough = true;
            break;
        case 'Très fort':
            color = 'success';
            width = 100;
            isStrongEnough = true;
            break;
        default:
            color = 'secondary';
            width = 10;
    }

    entropyElement.classList.add('text-bg-' + color);
    entropyElement.textContent = label;

    // Mise à jour de la barre de progression
    if (progress) {
        progress.style.width = width + '%';
        progress.className = 'progress-bar bg-' + color;
    }

    return isStrongEnough;
}


export function bindPasswordGenerator(generateBtn, passwordInput) {
    if (!generateBtn || !passwordInput) return;

    generateBtn.addEventListener("click", () => {
        const icon = generateBtn.querySelector("i");
        const label = generateBtn.querySelector("span");

        if (icon) {
            icon.classList.remove("bi-shuffle");
            icon.classList.add("bi-arrow-repeat");
        }
        const newPassword = generateStrongPassword(24);
        passwordInput.value = newPassword;
        passwordInput.dispatchEvent(new Event('input'));

        navigator.clipboard.writeText(newPassword).then(() => {
            if (icon) icon.className = "bi bi-clipboard-check";
            if (label) label.textContent = "Copié !";
            setTimeout(() => {
                if (label) label.textContent = "Générer";
                if (icon) icon.className = "bi bi-shuffle";
            }, 2000);
        }).catch(() => {
            if (icon) icon.className = "bi bi-exclamation-triangle";
            if (label) label.textContent = "Erreur";
            setTimeout(() => {
                if (label) label.textContent = "Générer";
                if (icon) icon.className = "bi bi-shuffle";
            }, 2000);
        });
    });
}