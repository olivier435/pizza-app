import {
  evaluatePasswordStrength,
  updateEntropy,
  bindPasswordGenerator
} from './modules/passwordUtils.js'; // <-- IMPORTANT: chemin relatif

const $ = (sel) => document.querySelector(sel);
const pick = (...selectors) => selectors.map((s) => $(s)).find(Boolean) || null;

document.addEventListener('DOMContentLoaded', () => {
  const isResetPage = !!document.getElementById('reset-password');

  // Champs selon la page
  const firstname = pick('#firstname', '#registration_form_firstname');
  const lastname  = pick('#lastname',  '#registration_form_lastname');
  const email     = pick('#email',     '#registration_form_email');
  const password  = pick('#password',  '#registration_form_plainPassword');
  const password2 = pick('#password2', '#registration_form_password2', '#registration_form_plainPassword_confirm');
  const terms     = pick('#login-register-terms', '#registration_form_agreeTerms');

  const submitBtn   = pick('#submit-button', 'button[type="submit"]');
  const entropyEl   = pick('#entropy', '#password-entropy');
  const progressBar = document.getElementById('password-progress');

  // Générateur
  const generateBtn = $('#generate-password');
  bindPasswordGenerator(generateBtn, password || undefined);

  // Toggle
  const toggleBtn = $('#togglePassword');
  if (toggleBtn && password && password2 && toggleBtn.dataset.bound !== '1') {
    toggleBtn.dataset.bound = '1';
    toggleBtn.addEventListener('click', () => {
      const show = (password.type === 'password');
      password.type  = show ? 'text' : 'password';
      password2.type = show ? 'text' : 'password';
      const icon = toggleBtn.querySelector('i');
      if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  }

  // Mise à jour de la progress bar (affichage uniquement)
  function updateProgressBar(strengthLabel) {
    if (!progressBar) return;
    // reset classes
    progressBar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
    let width = '5%';
    let cls = 'bg-danger';
    switch (strengthLabel) {
      case 'Très faible':
        width = '5%';   cls = 'bg-danger';  break;
      case 'Faible':
        width = '25%';  cls = 'bg-danger';  break;
      case 'Moyen':
        width = '50%';  cls = 'bg-warning'; break;
      case 'Fort':
        width = '75%';  cls = 'bg-success'; break;
      case 'Très fort':
        width = '100%'; cls = 'bg-success'; break;
    }
    progressBar.style.width = width;
    progressBar.classList.add(cls);
  }

  // État pour la page register uniquement (sur reset on ne bloque pas sur la force)
  const state = {
    firstname: !firstname,
    lastname:  !lastname,
    email:     !email,
    passPair:  !(password && password2),
    passStrong: !password, // register: sera mis à jour via updateEntropy
    terms:     terms ? !!terms.checked : true,
  };

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function setSubmit() {
    if (!submitBtn) return;

    if (isResetPage) {
      // Sur reset: bouton activé seulement si les 2 mots de passe sont non vides et identiques
      const ok = !!(password && password2 && password.value.trim() !== '' && password.value === password2.value);
      submitBtn.toggleAttribute('disabled', !ok);
      return;
    }

    // Sur register: UX stricte
    const ok = state.firstname && state.lastname && state.email && state.passPair && state.passStrong && state.terms;
    submitBtn.toggleAttribute('disabled', !ok);
  }

  function onFirstname() { state.firstname = !!(firstname && firstname.value.trim().length >= 2); setSubmit(); }
  function onLastname()  { state.lastname  = !!(lastname  && lastname.value.trim().length >= 2); setSubmit(); }
  function onEmail()     { state.email     = !!(email && emailRegex.test(email.value.trim()));   setSubmit(); }

  function onPasswordPair() {
    // paire identique ?
    state.passPair = !!(password && password2 && password.value !== '' && password.value === password2.value);
    setSubmit();
  }

  function onPassword() {
    if (!password) return;

    const strengthLabel = evaluatePasswordStrength(password.value);

    // Toujours mettre à jour le badge + barre (affichage)
    updateEntropy(entropyEl, strengthLabel);     // couleurs/texte badge
    updateProgressBar(strengthLabel);            // barre

    // Sur register on impose "Fort/Très fort" pour activer le bouton
    if (!isResetPage) {
      state.passStrong = (strengthLabel === 'Fort' || strengthLabel === 'Très fort');
    }
    onPasswordPair();
  }

  function onTerms() { state.terms = !!(terms && terms.checked); setSubmit(); }

  // Bind si présents
  firstname && firstname.addEventListener('input', onFirstname);
  lastname  && lastname.addEventListener('input',  onLastname);
  email     && email.addEventListener('input',     onEmail);
  password  && password.addEventListener('input',  onPassword);
  password2 && password2.addEventListener('input', onPasswordPair);
  terms     && terms.addEventListener('change',    onTerms);

  // Init (bouton désactivé au chargement sur reset tant que pair non OK)
  onFirstname();
  onLastname();
  onEmail();
  onPassword();
  onPasswordPair();
  onTerms();

  if (isResetPage && submitBtn) {
    // force disabled au chargement tant que l'utilisateur n'a pas saisi 2 champs identiques
    const ok = !!(password && password2 && password.value.trim() !== '' && password.value === password2.value);
    submitBtn.toggleAttribute('disabled', !ok);
  }
});