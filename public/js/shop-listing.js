(function () {
  const shopPage = document.getElementById('shopPage');
  if (!shopPage) return;

  const grid = document.getElementById('shopProductGrid');
  const emptyState = document.getElementById('shopEmpty');
  const resultCount = document.getElementById('shopResultCount');
  const activeFiltersEl = document.getElementById('shopActiveFilters');
  const filterBadge = document.getElementById('shopFilterBadge');
  const sortSelect = document.querySelector('[data-shop-sort]');
  const toast = document.getElementById('shopToast');
  const toastMessage = document.getElementById('shopToastMessage');
  const filterDrawer = document.getElementById('shopFilterDrawer');
  const desktopForm = document.getElementById('shopFiltersDesktop');
  const mobileForm = document.getElementById('shopFiltersMobile');

  const CART_KEY = 'mandira_cart';
  const PAGE_SIZE = 8;
  let visibleLimit = PAGE_SIZE;
  let toastTimer;

  const cols = () => Array.from(grid.querySelectorAll('.shop-product-col'));

  function getFilterState(form) {
    if (!form) return null;
    const fd = new FormData(form);
    const categories = fd.getAll('category');
    const availability = fd.getAll('availability');
    const rating = fd.get('rating') || '';
    const priceMin = fd.get('price_min');
    const priceMax = fd.get('price_max');

    return {
      categories,
      availability,
      rating: rating === '' ? null : Number(rating),
      priceMin: priceMin !== '' ? Number(priceMin) : null,
      priceMax: priceMax !== '' ? Number(priceMax) : null,
    };
  }

  function syncForms(sourceForm) {
    const target = sourceForm === desktopForm ? mobileForm : desktopForm;
    if (!sourceForm || !target) return;

    sourceForm.querySelectorAll('input').forEach((input) => {
      const match = target.querySelector(`[name="${input.name}"][value="${CSS.escape(input.value)}"]`)
        || target.querySelector(`[name="${input.name}"]`);
      if (!match) return;
      if (input.type === 'checkbox' || input.type === 'radio') {
        const twin = target.querySelector(
          `[name="${input.name}"][value="${CSS.escape(input.value)}"]`
        );
        if (twin) twin.checked = input.checked;
      } else {
        const field = target.querySelector(`[name="${input.name}"]`);
        if (field) field.value = input.value;
      }
    });
  }

  function countActiveFilters(state) {
    let n = 0;
    n += state.categories.length;
    n += state.availability.length;
    if (state.rating !== null) n += 1;
    if (state.priceMin !== null) n += 1;
    if (state.priceMax !== null) n += 1;
    return n;
  }

  function productMatches(col, state) {
    const card = col.querySelector('.product-card');
    const category = col.dataset.category || '';
    const price = Number(col.dataset.price || card?.dataset.price || 0);
    const rating = Number(col.dataset.rating || card?.dataset.rating || 0);
    const inStock = (col.dataset.inStock ?? '1') === '1';

    if (state.categories.length && !state.categories.includes(category)) {
      return false;
    }

    if (state.availability.length) {
      const wantsIn = state.availability.includes('in-stock');
      const wantsOut = state.availability.includes('out-of-stock');
      if (wantsIn && !wantsOut && !inStock) return false;
      if (wantsOut && !wantsIn && inStock) return false;
    }

    if (state.rating !== null && rating < state.rating) return false;
    if (state.priceMin !== null && price < state.priceMin) return false;
    if (state.priceMax !== null && price > state.priceMax) return false;

    return true;
  }

  function sortCols(matched) {
    const sort = sortSelect?.value || 'default';

    matched.sort((a, b) => {
      const priceA = Number(a.col.dataset.price || 0);
      const priceB = Number(b.col.dataset.price || 0);
      const ratingA = Number(a.col.dataset.rating || 0);
      const ratingB = Number(b.col.dataset.rating || 0);

      switch (sort) {
        case 'price-asc':
          return priceA - priceB;
        case 'price-desc':
          return priceB - priceA;
        case 'rating-desc':
          return ratingB - ratingA;
        default:
          return a.index - b.index;
      }
    });

    matched.forEach(({ col }) => grid.appendChild(col));
  }

  function renderActiveChips(state) {
    if (!activeFiltersEl) return;
    activeFiltersEl.innerHTML = '';
    const chips = [];

    state.categories.forEach((c) => {
      const label = c === 'dried-fruits' ? 'Dried Fruits' : 'Pickles';
      chips.push({ key: 'category', value: c, label });
    });

    state.availability.forEach((a) => {
      chips.push({
        key: 'availability',
        value: a,
        label: a === 'in-stock' ? 'In stock' : 'Out of stock',
      });
    });

    if (state.rating !== null) {
      chips.push({ key: 'rating', value: String(state.rating), label: `${state.rating}★ & above` });
    }

    if (state.priceMin !== null) {
      chips.push({ key: 'price_min', value: String(state.priceMin), label: `From Rs. ${state.priceMin}` });
    }

    if (state.priceMax !== null) {
      chips.push({ key: 'price_max', value: String(state.priceMax), label: `Up to Rs. ${state.priceMax}` });
    }

    if (!chips.length) {
      activeFiltersEl.hidden = true;
      return;
    }

    activeFiltersEl.hidden = false;
    chips.forEach((chip) => {
      const el = document.createElement('span');
      el.className = 'shop-filter-chip';
      el.innerHTML = `${chip.label}<button type="button" aria-label="Remove ${chip.label}" data-chip-key="${chip.key}" data-chip-value="${chip.value}"><i class="bi bi-x" aria-hidden="true"></i></button>`;
      activeFiltersEl.appendChild(el);
    });
  }

  function applyFilters(sourceForm) {
    const state = getFilterState(sourceForm || desktopForm || mobileForm);
    if (!state) return;

    if (sourceForm) syncForms(sourceForm);

    grid.classList.add('is-updating');

    const all = cols().map((col, index) => ({ col, index }));
    const matched = [];

    all.forEach(({ col }) => col.classList.remove('is-hidden'));

    all.forEach(({ col, index }) => {
      const match = productMatches(col, state);
      if (!match) {
        col.classList.add('is-hidden');
        return;
      }
      matched.push({ col, index });
    });

    sortCols(matched);

    const total = matched.length;
    let shown = 0;

    matched.forEach(({ col }, i) => {
      const show = i < visibleLimit;
      col.classList.toggle('is-hidden', !show);
      if (show) shown += 1;
    });

    const hiddenByPagination = total > visibleLimit;
    const loadMoreBtn = document.getElementById('shopLoadMore');
    if (loadMoreBtn) {
      loadMoreBtn.hidden = !hiddenByPagination;
      loadMoreBtn.textContent = `Load more (${total - visibleLimit} remaining)`;
    }

    const anyVisible = shown > 0;
    emptyState.hidden = anyVisible;
    grid.hidden = !anyVisible;

    resultCount.textContent = total === 1 ? '1 product' : `${total} products`;
    if (shown < total) {
      resultCount.textContent += ` · showing ${shown}`;
    }

    const activeCount = countActiveFilters(state);
    document.querySelectorAll('[data-action="clear-filters"]').forEach((btn) => {
      btn.hidden = activeCount === 0;
    });

    if (filterBadge) {
      filterBadge.hidden = activeCount === 0;
      filterBadge.textContent = String(activeCount);
    }

    renderActiveChips(state);

    requestAnimationFrame(() => grid.classList.remove('is-updating'));
  }

  function clearFilters() {
    [desktopForm, mobileForm].forEach((form) => {
      if (!form) return;
      form.reset();
      const anyRating = form.querySelector('[name="rating"][value=""]');
      if (anyRating) anyRating.checked = true;
    });
    visibleLimit = PAGE_SIZE;
    applyFilters();
  }

  function showToast(message) {
    if (!toast || !toastMessage) return;
    toastMessage.textContent = message;
    toast.hidden = false;
    toast.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toast.classList.remove('is-visible');
      setTimeout(() => { toast.hidden = true; }, 300);
    }, 2800);
  }

  function getCart() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
    } catch {
      return [];
    }
  }

  function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  }

  function addToCart(productId, name, price) {
    const cart = getCart();
    const existing = cart.find((item) => item.id === productId);

    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({ id: productId, name, price, qty: 1 });
    }

    saveCart(cart);
    showToast(`${name} added to cart`);
  }

  // Filter form listeners
  [desktopForm, mobileForm].forEach((form) => {
    if (!form) return;
    form.addEventListener('change', () => {
      visibleLimit = PAGE_SIZE;
      applyFilters(form);
    });
    form.addEventListener('input', (e) => {
      if (e.target.matches('[data-filter-price-min], [data-filter-price-max]')) {
        clearTimeout(form._priceDebounce);
        form._priceDebounce = setTimeout(() => {
          visibleLimit = PAGE_SIZE;
          applyFilters(form);
        }, 400);
      }
    });
  });

  document.querySelectorAll('[data-action="clear-filters"]').forEach((btn) => {
    btn.addEventListener('click', clearFilters);
  });

  document.querySelectorAll('[data-action="apply-filters"]').forEach((btn) => {
    btn.addEventListener('click', () => {
      applyFilters(mobileForm);
      bootstrap.Offcanvas.getInstance(filterDrawer)?.hide();
    });
  });

  activeFiltersEl?.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-chip-key]');
    if (!btn) return;

    const { chipKey, chipValue } = btn.dataset;
    [desktopForm, mobileForm].forEach((form) => {
      if (!form) return;
      if (chipKey === 'category' || chipKey === 'availability') {
        const input = form.querySelector(`[name="${chipKey}"][value="${chipValue}"]`);
        if (input) input.checked = false;
      } else if (chipKey === 'rating') {
        const any = form.querySelector('[name="rating"][value=""]');
        if (any) any.checked = true;
      } else {
        const input = form.querySelector(`[name="${chipKey}"]`);
        if (input) input.value = '';
      }
    });
    visibleLimit = PAGE_SIZE;
    applyFilters();
  });

  sortSelect?.addEventListener('change', () => applyFilters());

  document.querySelectorAll('.shop-view-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const view = btn.dataset.view;
      document.querySelectorAll('.shop-view-btn').forEach((b) => {
        b.classList.toggle('is-active', b === btn);
        b.setAttribute('aria-pressed', b === btn ? 'true' : 'false');
      });
      grid.classList.toggle('is-list-view', view === 'list');
    });
  });

  document.getElementById('shopLoadMore')?.addEventListener('click', () => {
    visibleLimit += PAGE_SIZE;
    applyFilters();
  });

  // Quick add — delegated to MandiraStore when available
  shopPage.addEventListener('click', (e) => {
    const cartBtn = e.target.closest('[data-action="add-to-cart"]');
    if (!cartBtn || window.MandiraStore) return;

    e.preventDefault();
    const card = cartBtn.closest('.product-card');
    const id = cartBtn.dataset.productId || card?.dataset.productId;
    const name = cartBtn.dataset.productName || card?.querySelector('.product-name')?.textContent?.trim();
    const price = cartBtn.dataset.productPrice || card?.dataset.price;

    cartBtn.classList.add('is-added');
    setTimeout(() => cartBtn.classList.remove('is-added'), 900);

    if (id && name) addToCart(id, name, price);
  });

  // Pre-select category from URL (?category=dried-fruits|pickles)
  const params = new URLSearchParams(window.location.search);
  const urlCategory = params.get('category');
  if (urlCategory && ['dried-fruits', 'pickles'].includes(urlCategory)) {
    [desktopForm, mobileForm].forEach((form) => {
      const input = form?.querySelector(`[name="category"][value="${urlCategory}"]`);
      if (input) input.checked = true;
    });
  }

  applyFilters();
})();
