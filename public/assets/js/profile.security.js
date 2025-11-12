import {
  evaluatePasswordStrength,
  updateEntropy,
  bindPasswordGenerator
} from '/assets/js/modules/passwordUtils.js';

const $ = (s) => document.querySelector(s);

document.addEventListener('DOMContentLoaded', () => {
  const current = $('#currentPassword');
  const p1 = $('#newPassword');
  const p2 = $('#confirmPassword');
  const toggle = $('#togglePasswordSec');
  const genBtn = $('#generate-password-sec');
  const entropyEl = $('#entropy-sec');
  const progress = $('#password-progress-sec');
  const submit = $('#pw-submit-btn');

  // Générateur
  bindPasswordGenerator(genBtn, p1 || undefined);

  // Toggle
  if (toggle && p1 && p2) {
    toggle.addEventListener('click', () => {
      const show = p1.type === 'password';
      p1.type = show ? 'text' : 'password';
      p2.type = show ? 'text' : 'password';
      const icon = toggle.querySelector('i');
      if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  }

  function updateProgress(label) {
    if (!progress) return;
    progress.classList.remove('bg-danger', 'bg-warning', 'bg-success');
    let w = '5%', cls = 'bg-danger';
    switch (label) {
      case 'Très faible': w='5%'; cls='bg-danger'; break;
      case 'Faible':     w='25%'; cls='bg-danger'; break;
      case 'Moyen':      w='50%'; cls='bg-warning'; break;
      case 'Fort':       w='75%'; cls='bg-success'; break;
      case 'Très fort':  w='100%';cls='bg-success'; break;
    }
    progress.style.width = w;
    progress.classList.add(cls);
  }

  const state = {
    haveCurrent: false,
    pairOk: false,
    strong: false,
  };

  function setSubmit() {
    if (!submit) return;
    const ok = state.haveCurrent && state.pairOk && state.strong;
    submit.toggleAttribute('disabled', !ok);
  }

  function onCurrent() {
    state.haveCurrent = !!(current && current.value.trim() !== '');
    setSubmit();
  }

  function onPassword() {
    if (!p1) return;
    const label = evaluatePasswordStrength(p1.value);
    // badge et barre
    updateEntropy(entropyEl, label);
    updateProgress(label);
    // force minimale: Fort
    state.strong = (label === 'Fort' || label === 'Très fort');
    onPair();
  }

  function onPair() {
    state.pairOk = !!(p1 && p2 && p1.value !== '' && p1.value === p2.value);
    setSubmit();
  }

  current && current.addEventListener('input', onCurrent);
  p1 && p1.addEventListener('input', onPassword);
  p2 && p2.addEventListener('input', onPair);

  // init
  onCurrent();
  onPassword();
  onPair();
});