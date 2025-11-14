document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#booking-form');
  if (!form) return;

  const fields = {
    name:     form.elements.namedItem('name'),
    email:    form.elements.namedItem('email'),
    phone:    form.elements.namedItem('phone'),
    date:     form.elements.namedItem('date'),
    time:     form.elements.namedItem('time'),
    people:   form.elements.namedItem('people'),
    message:  form.elements.namedItem('message'),
  };

  const getErrorEl = (name) =>
    form.querySelector(`.invalid-feedback[data-error-for="${name}"]`);

  function setError(name, message) {
    const input   = fields[name];
    const errorEl = getErrorEl(name);
    if (!input || !errorEl) return;
    input.classList.add('is-invalid');
    errorEl.textContent = message || '';
  }

  function clearError(name) {
    const input   = fields[name];
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
      case 'name':
        if (value.length < 3) {
          error = 'Nom trop court (min. 3 caractères).';
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

      case 'phone':
        if (value.length < 6) {
          error = 'Téléphone trop court ou invalide.';
        }
        break;

      case 'date':
        if (value === '') {
          error = 'Date obligatoire.';
        }
        break;

      case 'time':
        if (value === '') {
          error = 'Heure obligatoire.';
        }
        break;

      case 'people':
        if (value === '') {
          error = 'Merci d\'indiquer le nombre de convives.';
        }
        break;

      case 'message':
        if (value !== '' && value.length < 4) {
          error = 'Message trop court (min. 4 caractères) ou laissez vide.';
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