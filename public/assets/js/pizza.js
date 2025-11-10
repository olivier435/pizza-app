/**
 * Pizza modal logic — configuration et ajout au panier (avec select multiples)
 */
(function () {
  "use strict";

  const modalEl = document.getElementById('pizzaModal');
  if (!modalEl || !window.bootstrap) return;
  const modal = new bootstrap.Modal(modalEl, { backdrop: 'static' });

  const pmId    = document.getElementById('pm-id');
  const pmName  = document.getElementById('pm-name');
  const pmDesc  = document.getElementById('pm-desc');
  const pmPhoto = document.getElementById('pm-photo');
  const pmSizes = document.getElementById('pm-sizes');
  const pmQty   = document.getElementById('pm-qty');
  const pmMinus = document.getElementById('pm-qty-minus');
  const pmPlus  = document.getElementById('pm-qty-plus');
  const pmExtras= document.getElementById('pm-extras');
  const pmTotal = document.getElementById('pm-total');
  const form    = document.getElementById('pizzaModalForm');

  let state = { base: 0, sizes: [], extras: [] };

  const centsToEuro = c => (c / 100).toFixed(2).replace('.', ',');

  const currentSizePriceCents = () => {
    const r = pmSizes.querySelector('input[name="size"]:checked');
    if (!r) return state.base;
    const found = state.sizes.find(s => s.label === r.value);
    return found ? found.priceCents : state.base;
  };

  const selectedExtrasTotalCents = () => {
    let sum = 0;
    pmExtras.querySelectorAll('option:checked').forEach(opt => {
      sum += parseInt(opt.dataset.price, 10) || 0;
    });
    return sum;
  };

  const recomputeTotal = () => {
    const qty  = Math.max(1, parseInt(pmQty.value, 10) || 1);
    const unit = currentSizePriceCents() + selectedExtrasTotalCents();
    pmTotal.textContent = centsToEuro(unit * qty) + ' €';
  };

  // Gestion de la quantité
  pmMinus?.addEventListener('click', () => {
    pmQty.value = Math.max(1, (parseInt(pmQty.value, 10) || 1) - 1);
    recomputeTotal();
  });
  pmPlus?.addEventListener('click', () => {
    pmQty.value = (parseInt(pmQty.value, 10) || 1) + 1;
    recomputeTotal();
  });
  pmQty?.addEventListener('input', recomputeTotal);
  pmSizes?.addEventListener('change', recomputeTotal);
  pmExtras?.addEventListener('change', recomputeTotal);

  // Ouvre la modale au clic sur une card
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.open-pizza-modal');
    if (!btn) return;
    e.preventDefault();

    const id = btn.dataset.pizzaId;
    if (!id) return;

    const res = await fetch('/api/pizzas/' + encodeURIComponent(id), {
      headers: { 'Accept': 'application/json' }
    });

    const ct = (res.headers.get('content-type') || '').toLowerCase();
    if (!ct.includes('application/json')) {
      const raw = await res.text();
      console.error('Non-JSON response:', raw);
      alert('Erreur lors du chargement de la pizza.');
      return;
    }

    const data = await res.json();

    // Hydrate la modale
    pmId.value = data.id;
    pmName.textContent = data.name || '';
    pmDesc.textContent = data.description || '';
    pmPhoto.src = data.photoUrl || '/assets/img/restaurant/default.webp';
    state.base  = data.basePriceCents || 0;
    state.sizes = Array.isArray(data.sizes) ? data.sizes : [];
    state.extras= Array.isArray(data.extras) ? data.extras : [];

    // --- Tailles avec diamètre en cm ---
    pmSizes.innerHTML = '';
    if (state.sizes.length) {
      state.sizes.forEach(s => {
        const rid = 'size_' + s.label;
        const euros = centsToEuro(s.priceCents);
        const cm = s.diameterCm ? String(s.diameterCm).replace('.0', '') : '';
        pmSizes.insertAdjacentHTML('beforeend', `
          <div class="form-check me-3 mb-2">
            <input class="form-check-input" type="radio" name="size" id="${rid}" value="${s.label}" ${s.label === 'L' ? 'checked' : ''}>
            <label class="form-check-label" for="${rid}">
              ${s.label} ${cm ? `(${cm} cm)` : ''} — ${euros} €
            </label>
          </div>
        `);
      });
    } else {
      pmSizes.innerHTML = '<div class="text-muted small">Aucune taille disponible.</div>';
    }

    // --- Ingrédients additionnels (Select multiple trié) ---
    pmExtras.innerHTML = '';
    if (state.extras.length) {
      state.extras.sort((a, b) => a.name.localeCompare(b.name, 'fr', { sensitivity: 'base' }));
      state.extras.forEach(ex => {
        const price = parseInt(ex.extraPriceCents, 10) || 0;
        const label = `${ex.name} (+${centsToEuro(price)} €)`;
        pmExtras.insertAdjacentHTML('beforeend', `<option value="${ex.id}" data-price="${price}">${label}</option>`);
      });
    } else {
      pmExtras.insertAdjacentHTML('beforeend', `<option disabled>Aucun ingrédient additionnel disponible.</option>`);
    }

    pmQty.value = 1;
    recomputeTotal();
    modal.show();
  });

  // Envoi AJAX "ajouter au panier"
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(form);
    if (!fd.get('size')) fd.set('size', 'L');

    const res = await fetch('/cart/add', {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      body: fd
    });

    let out = {};
    try { out = await res.json(); } catch (e) {}

    if (out && out.ok) {
      window.location.href = out.redirect || '/panier';
    } else {
      window.location.href = '/panier';
    }
  });
})();