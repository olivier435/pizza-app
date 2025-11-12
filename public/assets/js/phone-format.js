(function () {
  function formatFrPhone(raw) {
    if (!raw) return '';
    // garde uniquement les chiffres
    let d = raw.replace(/\D+/g, '');
    // accepte 33XXXXXXXXX → 0XXXXXXXXX (simple)
    if (d.startsWith('33')) d = '0' + d.slice(2);
    // limite à 10 chiffres (format FR simple)
    d = d.slice(0, 10);
    // groupe par paires
    return d.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
  }

  function bind(el) {
    if (!el) return;
    const handler = () => { el.value = formatFrPhone(el.value); };
    el.addEventListener('input', handler);
    el.addEventListener('change', handler);
    // format initial si prérempli
    handler();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[data-format="fr-phone"]').forEach(bind);
  });
})();