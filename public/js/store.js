(function () {
  const CART_KEY = 'mandira_cart';
  const WISHLIST_KEY = 'mandira_wishlist';

  const formatRs = (n) => 'Rs. ' + n.toLocaleString('en-NP');

  function readStorage(key) {
    try {
      const raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : [];
    } catch {
      return [];
    }
  }

  function writeStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
  }

  function getCart() {
    return readStorage(CART_KEY);
  }

  function saveCart(items) {
    writeStorage(CART_KEY, items);
    renderCart();
    updateCartBadges();
  }

  function getWishlist() {
    return readStorage(WISHLIST_KEY);
  }

  function saveWishlist(items) {
    writeStorage(WISHLIST_KEY, items);
    renderWishlist();
    updateWishlistBadges();
    syncWishlistButtons();
  }

  function normalizeCartItem(item) {
    return {
      id: item.id,
      name: item.name,
      price: Number(item.price) || 0,
      image: item.image || '',
      qty: Number(item.qty ?? item.quantity ?? 1) || 1,
    };
  }

  function normalizeWishlistItem(item) {
    return {
      id: item.id,
      name: item.name,
      price: Number(item.price) || 0,
      image: item.image || '',
    };
  }

  // Smooth horizontal scroll with mouse wheel on reels (desktop)
  const reels = document.getElementById('reelsScroll');
  if (reels) {
    reels.addEventListener(
      'wheel',
      (e) => {
        if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
          e.preventDefault();
          reels.scrollLeft += e.deltaY;
        }
      },
      { passive: false }
    );
  }

  // Mobile bottom nav — active state (skip cart/wishlist — open drawer)
  const mobileNavItems = document.querySelectorAll('.mobile-bottom-nav__item');
  mobileNavItems.forEach((item) => {
    item.addEventListener('click', (e) => {
      if (item.dataset.nav === 'cart' || item.dataset.nav === 'wishlist') return;
      e.preventDefault();
      mobileNavItems.forEach((el) => {
        el.classList.remove('active');
        el.removeAttribute('aria-current');
      });
      item.classList.add('active');
      item.setAttribute('aria-current', 'page');
      if (item.dataset.nav === 'home') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  });

  // Side drawer (cart + wishlist)
  const cartDrawer = document.getElementById('cartDrawer');
  if (!cartDrawer) return;

  const cartPanel = cartDrawer.querySelector('[data-panel="cart"]');
  const wishlistPanel = cartDrawer.querySelector('[data-panel="wishlist"]');
  const cartItemsList = document.getElementById('cartDrawerItems');
  const wishlistItemsList = document.getElementById('wishlistDrawerItems');
  const cartCountEl = document.getElementById('cartDrawerCount');
  const wishlistCountEl = document.getElementById('wishlistDrawerCount');
  const cartSubtotalEl = document.getElementById('cartSubtotal');
  const cartTotalEl = document.getElementById('cartTotal');
  const cartPillTotal = document.querySelector('.cart-pill__total');
  const cartBadge = document.querySelector('.mobile-bottom-nav__badge[data-count]');
  const wishlistNavBadge = document.querySelector('.nav-icon-btn__badge[data-wishlist-count]');
  const wishlistMobileBadge = document.querySelector('.mobile-bottom-nav__wishlist-badge[data-wishlist-count]');
  const cartCheckout = document.getElementById('cartCheckoutBtn');
  const wishlistMoveAllBtn = document.getElementById('wishlistMoveAllBtn');

  let drawerMode = 'cart';
  let offcanvasInstance = null;

  function getOffcanvas() {
    if (!offcanvasInstance) {
      offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(cartDrawer);
    }
    return offcanvasInstance;
  }

  function setDrawerMode(mode) {
    drawerMode = mode === 'wishlist' ? 'wishlist' : 'cart';
    cartDrawer.dataset.drawerMode = drawerMode;
    cartPanel.hidden = drawerMode !== 'cart';
    wishlistPanel.hidden = drawerMode !== 'wishlist';
  }

  function openDrawer(mode) {
    setDrawerMode(mode);
    getOffcanvas().show();
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  function buildCartItemEl(item) {
    const li = document.createElement('li');
    li.className = 'cart-drawer__item';
    li.dataset.productId = item.id;
    li.dataset.unitPrice = String(item.price);

    li.innerHTML = `
      <a href="#" class="cart-drawer__thumb" tabindex="-1" aria-hidden="true">
        <img src="${escapeHtml(item.image)}" alt="">
      </a>
      <div class="cart-drawer__details">
        <div class="cart-drawer__row-top">
          <h3 class="cart-drawer__name">${escapeHtml(item.name)}</h3>
          <button type="button" class="cart-drawer__remove" aria-label="Remove ${escapeHtml(item.name)}">
            <i class="bi bi-trash3" aria-hidden="true"></i>
          </button>
        </div>
        <p class="cart-drawer__price">${formatRs(item.price)}</p>
        <div class="cart-drawer__row-bottom">
          <div class="cart-drawer__qty" role="group" aria-label="Quantity">
            <button type="button" class="cart-drawer__qty-btn" data-action="decrease" aria-label="Decrease quantity">
              <i class="bi bi-dash" aria-hidden="true"></i>
            </button>
            <span class="cart-drawer__qty-value">${item.qty}</span>
            <button type="button" class="cart-drawer__qty-btn" data-action="increase" aria-label="Increase quantity">
              <i class="bi bi-plus" aria-hidden="true"></i>
            </button>
          </div>
          <span class="cart-drawer__line-total">${formatRs(item.price * item.qty)}</span>
        </div>
      </div>
    `;

    return li;
  }

  function buildWishlistItemEl(item) {
    const li = document.createElement('li');
    li.className = 'cart-drawer__item cart-drawer__item--wishlist';
    li.dataset.productId = item.id;
    li.dataset.unitPrice = String(item.price);

    li.innerHTML = `
      <a href="#" class="cart-drawer__thumb" tabindex="-1" aria-hidden="true">
        <img src="${escapeHtml(item.image)}" alt="">
      </a>
      <div class="cart-drawer__details">
        <div class="cart-drawer__row-top">
          <h3 class="cart-drawer__name">${escapeHtml(item.name)}</h3>
          <button type="button" class="cart-drawer__remove" aria-label="Remove ${escapeHtml(item.name)}">
            <i class="bi bi-trash3" aria-hidden="true"></i>
          </button>
        </div>
        <p class="cart-drawer__price">${formatRs(item.price)}</p>
        <div class="cart-drawer__row-bottom">
          <button type="button" class="cart-drawer__move-cart" data-action="move-to-cart">
            <i class="bi bi-bag-plus" aria-hidden="true"></i>
            Move to cart
          </button>
        </div>
      </div>
    `;

    return li;
  }

  function renderCart() {
    const items = getCart().map(normalizeCartItem);
    cartItemsList.innerHTML = '';
    items.forEach((item) => cartItemsList.appendChild(buildCartItemEl(item)));
    updateCartTotals();
  }

  function renderWishlist() {
    const items = getWishlist().map(normalizeWishlistItem);
    wishlistItemsList.innerHTML = '';
    items.forEach((item) => wishlistItemsList.appendChild(buildWishlistItemEl(item)));
    updateWishlistTotals();
  }

  function updateCartTotals() {
    const items = Array.from(cartItemsList.querySelectorAll('.cart-drawer__item'));
    let itemCount = 0;
    let subtotal = 0;

    items.forEach((row) => {
      const unit = Number(row.dataset.unitPrice) || 0;
      const qty = Number(row.querySelector('.cart-drawer__qty-value')?.textContent) || 1;
      const lineTotal = unit * qty;
      itemCount += qty;
      subtotal += lineTotal;
      const lineEl = row.querySelector('.cart-drawer__line-total');
      if (lineEl) lineEl.textContent = formatRs(lineTotal);
      const decBtn = row.querySelector('[data-action="decrease"]');
      if (decBtn) decBtn.disabled = qty <= 1;
    });

    const isEmpty = items.length === 0;
    cartPanel.classList.toggle('is-empty', isEmpty);

    const countLabel = itemCount === 1 ? '1 item' : itemCount + ' items';
    cartCountEl.textContent = isEmpty ? '0 items' : countLabel;
    cartSubtotalEl.textContent = formatRs(subtotal);
    cartTotalEl.textContent = formatRs(subtotal);
    if (cartPillTotal) cartPillTotal.textContent = formatRs(subtotal);
    if (cartCheckout) cartCheckout.disabled = isEmpty;

    persistCartFromDom();
  }

  function updateWishlistTotals() {
    const items = Array.from(wishlistItemsList.querySelectorAll('.cart-drawer__item'));
    const count = items.length;
    const isEmpty = count === 0;

    wishlistPanel.classList.toggle('is-empty', isEmpty);
    wishlistCountEl.textContent = count === 1 ? '1 item' : count + ' items';
    if (wishlistMoveAllBtn) wishlistMoveAllBtn.disabled = isEmpty;
  }

  function persistCartFromDom() {
    const items = Array.from(cartItemsList.querySelectorAll('.cart-drawer__item')).map((row) => ({
      id: row.dataset.productId,
      name: row.querySelector('.cart-drawer__name')?.textContent?.trim() || '',
      price: Number(row.dataset.unitPrice) || 0,
      image: row.querySelector('.cart-drawer__thumb img')?.src || '',
      qty: Number(row.querySelector('.cart-drawer__qty-value')?.textContent) || 1,
    }));
    writeStorage(CART_KEY, items);
    updateCartBadges();
  }

  function updateCartBadges() {
    const items = getCart().map(normalizeCartItem);
    const itemCount = items.reduce((sum, item) => sum + item.qty, 0);
    const subtotal = items.reduce((sum, item) => sum + item.price * item.qty, 0);

    if (cartBadge) {
      cartBadge.textContent = itemCount;
      cartBadge.dataset.count = String(itemCount);
      cartBadge.setAttribute(
        'aria-label',
        itemCount + (itemCount === 1 ? ' item' : ' items') + ' in cart'
      );
    }

    if (cartPillTotal) cartPillTotal.textContent = formatRs(subtotal);
  }

  function updateWishlistBadges() {
    const count = getWishlist().length;
    [wishlistNavBadge, wishlistMobileBadge].forEach((badge) => {
      if (!badge) return;
      badge.textContent = count;
      badge.dataset.wishlistCount = String(count);
      badge.setAttribute(
        'aria-label',
        count + (count === 1 ? ' item' : ' items') + ' in wishlist'
      );
    });
  }

  function syncWishlistButtons() {
    const ids = new Set(getWishlist().map((item) => item.id));
    document.querySelectorAll('[data-action="wishlist"]').forEach((btn) => {
      const id = btn.dataset.productId || btn.closest('[data-product-id]')?.dataset.productId;
      if (!id) return;
      const active = ids.has(id);
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      btn.setAttribute('aria-label', active ? 'Remove from wishlist' : 'Add to wishlist');
      const icon = btn.querySelector('i');
      if (icon) icon.className = active ? 'bi bi-heart-fill' : 'bi bi-heart';
    });
  }

  function productFromCard(card, btn) {
    const id = btn?.dataset.productId || card?.dataset.productId;
    if (!id || !card) return null;

    const name =
      btn?.dataset.productName ||
      card.querySelector('.product-name')?.textContent?.trim() ||
      card.querySelector('.product-card__title-link')?.textContent?.trim();

    const price =
      Number(btn?.dataset.productPrice || card.dataset.price || 0) || 0;

    const image =
      btn?.dataset.productImage ||
      card.querySelector('.product-card__img--primary')?.src ||
      '';

    return { id, name, price, image };
  }

  function toggleWishlist(product) {
    if (!product?.id) return false;

    const items = getWishlist().map(normalizeWishlistItem);
    const index = items.findIndex((item) => item.id === product.id);

    if (index >= 0) {
      items.splice(index, 1);
      saveWishlist(items);
      return false;
    }

    items.push(normalizeWishlistItem(product));
    saveWishlist(items);
    return true;
  }

  function addToCart(product, qty = 1) {
    if (!product?.id) return;

    const items = getCart().map(normalizeCartItem);
    const existing = items.find((item) => item.id === product.id);

    if (existing) {
      existing.qty += qty;
    } else {
      items.push({
        id: product.id,
        name: product.name,
        price: Number(product.price) || 0,
        image: product.image || '',
        qty,
      });
    }

    saveCart(items);
  }

  function moveWishlistItemToCart(productId) {
    const wishlist = getWishlist().map(normalizeWishlistItem);
    const item = wishlist.find((entry) => entry.id === productId);
    if (!item) return;

    addToCart(item, 1);
    saveWishlist(wishlist.filter((entry) => entry.id !== productId));
  }

  function moveAllWishlistToCart() {
    const wishlist = getWishlist().map(normalizeWishlistItem);
    if (!wishlist.length) return;

    const cart = getCart().map(normalizeCartItem);
    wishlist.forEach((item) => {
      const existing = cart.find((entry) => entry.id === item.id);
      if (existing) {
        existing.qty += 1;
      } else {
        cart.push({ ...item, qty: 1 });
      }
    });

    saveCart(cart);
    saveWishlist([]);
    setDrawerMode('cart');
  }

  function removeRowWithAnimation(row, onDone) {
    row.classList.add('is-removing');
    row.addEventListener(
      'animationend',
      () => {
        row.remove();
        onDone();
      },
      { once: true }
    );
  }

  cartItemsList.addEventListener('click', (e) => {
    const item = e.target.closest('.cart-drawer__item');
    if (!item) return;

    if (e.target.closest('.cart-drawer__remove')) {
      removeRowWithAnimation(item, updateCartTotals);
      return;
    }

    const action = e.target.closest('[data-action]')?.dataset.action;
    if (!action) return;

    const qtyEl = item.querySelector('.cart-drawer__qty-value');
    let qty = Number(qtyEl.textContent) || 1;
    if (action === 'increase') qty += 1;
    if (action === 'decrease' && qty > 1) qty -= 1;
    qtyEl.textContent = qty;
    updateCartTotals();
  });

  wishlistItemsList.addEventListener('click', (e) => {
    const item = e.target.closest('.cart-drawer__item');
    if (!item) return;

    const productId = item.dataset.productId;

    if (e.target.closest('.cart-drawer__remove')) {
      const wishlist = getWishlist().filter((entry) => entry.id !== productId);
      removeRowWithAnimation(item, () => {
        writeStorage(WISHLIST_KEY, wishlist);
        updateWishlistTotals();
        updateWishlistBadges();
        syncWishlistButtons();
      });
      return;
    }

    if (e.target.closest('[data-action="move-to-cart"]')) {
      moveWishlistItemToCart(productId);
    }
  });

  wishlistMoveAllBtn?.addEventListener('click', moveAllWishlistToCart);

  cartDrawer.addEventListener('show.bs.offcanvas', () => {
    requestAnimationFrame(() => {
      document.querySelector('.offcanvas-backdrop')?.classList.add('cart-drawer-backdrop');
    });
  });

  cartDrawer.addEventListener('hidden.bs.offcanvas', () => {
    document.querySelector('.cart-drawer-backdrop')?.classList.remove('cart-drawer-backdrop');
  });

  document.querySelectorAll('[data-drawer-open]').forEach((trigger) => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      openDrawer(trigger.dataset.drawerOpen || 'cart');
    });
  });

  document.querySelectorAll('[data-bs-target="#cartDrawer"]:not([data-drawer-open])').forEach((trigger) => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      openDrawer('cart');
    });
  });

  document.addEventListener('click', (e) => {
    const wishlistBtn = e.target.closest('[data-action="wishlist"]');
    if (wishlistBtn) {
      e.preventDefault();
      e.stopPropagation();

      const card = wishlistBtn.closest('.product-card');
      const root = document.getElementById('productDetail');
      let product = null;

      if (root && (wishlistBtn.id === 'productWishlist' || wishlistBtn.closest('#productDetail'))) {
        product = {
          id: root.dataset.productId,
          name: root.dataset.productName,
          price: Number(root.dataset.productPrice) || 0,
          image: root.dataset.productImage || '',
        };
      } else {
        product = productFromCard(card, wishlistBtn);
      }

      if (product) toggleWishlist(product);
      return;
    }

    const cartBtn = e.target.closest('[data-action="add-to-cart"]');
    if (cartBtn) {
      e.preventDefault();
      e.stopPropagation();

      const card = cartBtn.closest('.product-card');
      const product = productFromCard(card, cartBtn);
      if (product) {
        addToCart(product, 1);
        cartBtn.classList.add('is-added');
        window.setTimeout(() => cartBtn.classList.remove('is-added'), 700);
        openDrawer('cart');
      }
    }
  });

  // Seed demo cart items when storage is empty (matches original UI)
  if (!getCart().length) {
    saveCart([
      {
        id: 'premium-dried-mango',
        name: 'Premium Dried Mango Slices',
        price: 450,
        image: 'https://images.unsplash.com/photo-1606313564200-e75d5e304d0e?w=160&q=80',
        qty: 1,
      },
      {
        id: 'mixed-berry-medley',
        name: 'Mixed Berry Medley Pack',
        price: 680,
        image: 'https://images.unsplash.com/photo-1615485925511-ef4e2b6d4e6e?w=160&q=80',
        qty: 1,
      },
    ]);
  } else {
    renderCart();
    updateCartBadges();
  }

  renderWishlist();
  updateWishlistBadges();
  syncWishlistButtons();

  window.MandiraStore = {
    openDrawer,
    toggleWishlist,
    addToCart,
    getCart,
    getWishlist,
    moveWishlistItemToCart,
    moveAllWishlistToCart,
    formatRs,
  };
})();
