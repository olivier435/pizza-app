(function () {
  "use strict";

  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const $  = (sel, root = document) => root.querySelector(sel);

  const cartRoot     = $('#cart .cart-items');
  const grandNodes   = $$('#cart .summary-value, #cart .summary-total .summary-value');
  const badge        = document.querySelector('.header-action-btn .badge');

  if (!cartRoot) return;

  const sizeDiameters = { M: 28, L: 33, XL: 40 };

  const setGrand = (eurosText) => {
    grandNodes.forEach(n => n.textContent = eurosText);
  };

  const updateBadge = (count) => {
    if (!badge) return;
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = '';
    } else {
      badge.textContent = '';
      badge.style.display = 'none';
    }
  };

  // ------- UPDATE (qty) -------
  const postUpdate = async (index, qty) => {
    const fd = new FormData();
    fd.set('index', String(index));
    fd.set('qty',   String(qty));

    const res = await fetch('/cart/update', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || 'Update failed');
    return out;
  };

  const handleChangeQty = async (index, qtyInput) => {
    let qty = parseInt(qtyInput.value, 10);
    if (!Number.isFinite(qty) || qty < 1) qty = 1;
    qtyInput.value = String(qty);

    try {
      const out = await postUpdate(index, qty);
      const lineTotal = document.querySelector(`.item-total[data-index="${index}"] span`);
      if (lineTotal) lineTotal.textContent = out.lineTotalEuros;
      setGrand(out.grandEuros);
      updateBadge(out.count);
    } catch (e) {
      console.error(e);
      alert('Impossible de mettre à jour la quantité.');
    }
  };

  const debounce = (fn, delay = 300) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), delay);
    };
  };
  const onQtyInputDebounced = debounce(handleChangeQty, 300);

  // ------- REMOVE -------
  const postRemove = async (index) => {
    const fd = new FormData();
    fd.set('index', String(index));
    const res = await fetch('/cart/remove', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || 'Remove failed');
    return out;
  };

  const handleRemove = async (index) => {
    try {
      const out = await postRemove(index);

      const row = document.querySelector(`.cart-item[data-index="${index}"]`);
      if (row && row.parentElement) row.parentElement.removeChild(row);

      $$('.cart-item').forEach((item, newIdx) => {
        item.dataset.index = String(newIdx);
        $$('.edit-item, .remove-item, .quantity-btn.decrease, .quantity-btn.increase, .quantity-input, .item-total', item)
          .forEach(el => el.setAttribute('data-index', String(newIdx)));
      });

      setGrand(out.grandEuros);
      updateBadge(out.count);

      if (out.empty) {
        cartRoot.innerHTML = '<p class="text-muted">Votre panier est vide.</p>';
        setGrand('0,00 €');
      }
    } catch (e) {
      console.error(e);
      alert('Impossible de supprimer cet article.');
    }
  };

  // ------- EDIT (modale Bootstrap) -------
  const editModalEl = document.getElementById('editItemModal');
  const editForm    = document.getElementById('editItemForm');
  const emIndex     = document.getElementById('em-index');
  const emPizzaId   = document.getElementById('em-pizza-id');
  const emName      = document.getElementById('em-name');
  const emPhoto     = document.getElementById('em-photo');
  const emSizes     = document.getElementById('em-sizes');
  const emExtras    = document.getElementById('em-extras');

  let editModal = null;
  if (editModalEl && window.bootstrap) {
    editModal = new bootstrap.Modal(editModalEl, { backdrop: 'static' });
  }

  async function fetchPizzaData(pizzaId) {
    const res = await fetch('/api/pizzas/' + encodeURIComponent(pizzaId), { headers: { 'Accept':'application/json' }});
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  }

  function renderSizes(sizes, currentSize) {
    emSizes.innerHTML = '';
    (sizes || []).forEach((s, i) => {
      const id = 'em_size_' + (s.label || i);
      const checked = (s.label === currentSize) || (!currentSize && i === 0);
      emSizes.insertAdjacentHTML('beforeend', `
        <div class="form-check">
          <input class="form-check-input" type="radio" name="size" id="${id}" value="${s.label}" ${checked ? 'checked' : ''}>
          <label class="form-check-label" for="${id}">
            ${s.label}${s.diameterCm ? ` (${s.diameterCm} cm)` : ''} — ${(s.priceCents/100).toFixed(2).replace('.', ',')} €
          </label>
        </div>
      `);
    });
  }

  function renderExtras(extras, selectedIds) {
    emExtras.innerHTML = '';
    const sel = new Set((selectedIds || []).map(Number));
    (extras || [])
      .slice()
      .sort((a,b)=> String(a.name).localeCompare(String(b.name),'fr',{sensitivity:'base'}))
      .forEach(e => {
        const label = `${e.name} (+${(e.extraPriceCents/100).toFixed(2).replace('.', ',')} €)`;
        emExtras.insertAdjacentHTML('beforeend', `<option value="${e.id}" ${sel.has(Number(e.id)) ? 'selected':''}>${label}</option>`);
      });
  }

  async function openEdit(index) {
    const row = document.querySelector(`.cart-item[data-index="${index}"]`);
    if (!row || !editModal) return;
    const pizzaId = parseInt(row.getAttribute('data-pizza-id') || '0', 10);
    if (!pizzaId) return;

    try {
      const name  = row.querySelector('.product-title')?.textContent?.trim() || '';
      const photo = row.querySelector('.product-image img')?.getAttribute('src') || '/assets/img/restaurant/default.webp';
      const sizeText = row.querySelector('.product-meta .product-size')?.textContent || '';
      const currentSize = (sizeText.split(':')[1] || '').trim().split(' - ')[0] || 'L';

      const data = await fetchPizzaData(pizzaId);

      const currentExtrasNames = Array.from(row.querySelectorAll('.product-meta .d-block'))
        .map(n => n.textContent || '')
        .join(', ')
        .replace(/^\+\s*/, '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);

      const selectedIds = (data.extras || [])
        .filter(e => currentExtrasNames.includes(e.name))
        .map(e => e.id);

      emIndex.value   = String(index);
      emPizzaId.value = String(pizzaId);
      emName.textContent = name;
      emPhoto.setAttribute('src', photo);
      renderSizes(data.sizes || [], currentSize);
      renderExtras(data.extras || [], selectedIds);

      editModal.show();
    } catch (err) {
      console.error(err);
      alert("Impossible d'ouvrir l'éditeur.");
    }
  }

  async function postEdit(index, size, extrasIds) {
    const fd = new FormData();
    fd.set('index', String(index));
    fd.set('size', size);

    // <-- important : gérer le cas 0 sélection
    if (!extrasIds || extrasIds.length === 0) {
      // On envoie quand même un champ extras[] vide pour signaler "aucun ingrédient"
      fd.append('extras[]', '');
    } else {
      extrasIds.forEach(id => fd.append('extras[]', String(id)));
    }

    const res = await fetch('/cart/edit', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const out = await res.json();
    if (!out.ok) throw new Error(out.error || 'Edit failed');
    return out;
  }

  // Toggle sans Ctrl/Cmd
  emExtras?.addEventListener('mousedown', function (e) {
    const opt = e.target;
    if (!(opt instanceof HTMLOptionElement)) return;
    e.preventDefault();
    const select = this;
    const scrollTop = select.scrollTop;
    opt.selected = !opt.selected;
    const evt = new Event('change', { bubbles: true });
    select.dispatchEvent(evt);
    setTimeout(() => { select.scrollTop = scrollTop; }, 0);
  });

  editForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const index = parseInt(emIndex.value, 10);
    const size  = editForm.querySelector('input[name="size"]:checked')?.value || 'L';
    const extrasIds = Array.from(emExtras.selectedOptions).map(o => parseInt(o.value, 10)).filter(Number.isFinite);

    try {
      const out = await postEdit(index, size, extrasIds);

      const row = document.querySelector(`.cart-item[data-index="${index}"]`);
      const sizeNode = row?.querySelector('.product-meta .product-size');
      if (sizeNode && out.size) {
        const cm = sizeDiameters[out.size] || null;
        sizeNode.textContent = 'Taille : ' + out.size + (cm ? ' - ' + cm + 'cm' : '');
      }

      const meta = row?.querySelector('.product-meta');
      if (meta) {
        let extrasLine = meta.querySelector('.d-block');
        if (out.extrasText && out.extrasText.trim() !== '') {
          if (!extrasLine) {
            extrasLine = document.createElement('span');
            extrasLine.className = 'd-block';
            meta.appendChild(extrasLine);
          }
          extrasLine.textContent = '+ ' + out.extrasText;
        } else if (extrasLine) {
          extrasLine.parentElement.removeChild(extrasLine);
        }
      }

      const unitNode = row?.querySelector('.current-price');
      if (unitNode && out.unitEuros) unitNode.textContent = out.unitEuros;
      const totalNode = row?.querySelector('.item-total[data-index="'+index+'"] span');
      if (totalNode && out.lineTotalEuros) totalNode.textContent = out.lineTotalEuros;

      if (out.grandEuros) setGrand(out.grandEuros);
      if (typeof out.count === 'number') updateBadge(out.count);

      editModal?.hide();
    } catch (err) {
      console.error(err);
      alert('Impossible de modifier cet article.');
    }
  });

  // ------- Bind events -------
  cartRoot.addEventListener('click', (e) => {
    const btn = e.target.closest('.edit-item');
    if (!btn) return;
    const index = parseInt(btn.dataset.index, 10);
    openEdit(index);
  });

  cartRoot.addEventListener('click', (e) => {
    const btn = e.target.closest('.quantity-btn.increase');
    if (!btn) return;
    const index = parseInt(btn.dataset.index, 10);
    const input = document.querySelector(`.quantity-input[data-index="${index}"]`);
    if (!input) return;
    input.value = String((parseInt(input.value, 10) || 1) + 1);
    handleChangeQty(index, input);
  });

  cartRoot.addEventListener('click', (e) => {
    const btn = e.target.closest('.quantity-btn.decrease');
    if (!btn) return;
    const index = parseInt(btn.dataset.index, 10);
    const input = document.querySelector(`.quantity-input[data-index="${index}"]`);
    if (!input) return;
    const current = Math.max(1, (parseInt(input.value, 10) || 1) - 1);
    input.value = String(current);
    handleChangeQty(index, input);
  });

  cartRoot.addEventListener('input', (e) => {
    const input = e.target.closest('.quantity-input');
    if (!input) return;
    const index = parseInt(input.dataset.index, 10);
    onQtyInputDebounced(index, input);
  });

  cartRoot.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;
    const index = parseInt(btn.dataset.index, 10);
    handleRemove(index);
  });

  fetch('/cart/count', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(j => updateBadge(j.count ?? 0))
    .catch(() => {});
})();