(function () {
  const CART_KEY = 'mandira_cart';
  const root = document.getElementById('productDetail');
  if (!root) return;

  const productId = root.dataset.productId;
  const productName = root.dataset.productName;
  const productPrice = Number(root.dataset.productPrice) || 0;
  const productImage = root.dataset.productImage;
  const inStock = root.dataset.inStock === '1';

  const mainImage = document.getElementById('productMainImage');
  const thumbs = document.querySelectorAll('.product-gallery__thumb');
  const qtyInput = document.getElementById('productQty');
  const qtyDecrease = document.getElementById('qtyDecrease');
  const qtyIncrease = document.getElementById('qtyIncrease');
  const addToCartBtn = document.getElementById('addToCartBtn');
  const wishlistBtn = document.getElementById('productWishlist');
  const toast = document.getElementById('productToast');
  const toastMessage = document.getElementById('productToastMessage');
  const reviewForm = document.getElementById('reviewForm');
  const reviewsList = document.getElementById('reviewsList');

  let toastTimer = null;

  // Image gallery
  thumbs.forEach((thumb) => {
    thumb.addEventListener('click', () => {
      const src = thumb.dataset.image;
      if (!src || !mainImage) return;

      mainImage.src = src;
      thumbs.forEach((t) => {
        t.classList.remove('is-active');
        t.setAttribute('aria-pressed', 'false');
      });
      thumb.classList.add('is-active');
      thumb.setAttribute('aria-pressed', 'true');
    });
  });

  // Quantity
  function getQty() {
    const val = parseInt(qtyInput.value, 10);
    return Number.isFinite(val) && val >= 1 ? val : 1;
  }

  function setQty(val) {
    qtyInput.value = Math.min(99, Math.max(1, val));
  }

  if (qtyDecrease) {
    qtyDecrease.addEventListener('click', () => setQty(getQty() - 1));
  }

  if (qtyIncrease) {
    qtyIncrease.addEventListener('click', () => setQty(getQty() + 1));
  }

  if (qtyInput) {
    qtyInput.addEventListener('change', () => setQty(getQty()));
  }

  // Wishlist — handled by store.js (MandiraStore); sync initial state on load
  if (wishlistBtn && window.MandiraStore) {
    const inWishlist = window.MandiraStore.getWishlist().some((item) => item.id === productId);
    wishlistBtn.classList.toggle('is-active', inWishlist);
    wishlistBtn.setAttribute('aria-pressed', inWishlist ? 'true' : 'false');
    wishlistBtn.setAttribute(
      'aria-label',
      inWishlist ? 'Remove from wishlist' : 'Add to wishlist'
    );
    const icon = wishlistBtn.querySelector('i');
    if (icon) icon.className = inWishlist ? 'bi bi-heart-fill' : 'bi bi-heart';
  }

  // Cart (localStorage)
  function getCart() {
    try {
      const raw = localStorage.getItem(CART_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch {
      return [];
    }
  }

  function saveCart(items) {
    localStorage.setItem(CART_KEY, JSON.stringify(items));
    updateCartBadge(items);
  }

  function formatRs(n) {
    return 'Rs. ' + n.toLocaleString('en-NP');
  }

  function updateCartBadge(items) {
    const count = items.reduce((sum, item) => sum + (item.quantity || 0), 0);
    const subtotal = items.reduce(
      (sum, item) => sum + (item.price || 0) * (item.quantity || 0),
      0
    );

    const badge = document.querySelector('.mobile-bottom-nav__badge[data-count]');
    if (badge) {
      badge.textContent = count;
      badge.dataset.count = String(count);
      badge.setAttribute(
        'aria-label',
        count + (count === 1 ? ' item' : ' items') + ' in cart'
      );
    }

    const pillTotal = document.querySelector('.cart-pill__total');
    if (pillTotal) pillTotal.textContent = formatRs(subtotal);
  }

  function addToCart() {
    if (!inStock) return;

    const qty = getQty();

    if (window.MandiraStore) {
      window.MandiraStore.addToCart(
        { id: productId, name: productName, price: productPrice, image: productImage },
        qty
      );
      window.MandiraStore.openDrawer('cart');
      showToast(`Added ${qty} × ${productName} to cart`);
      return;
    }

    const items = getCart();
    const existing = items.find((item) => item.id === productId);

    if (existing) {
      existing.quantity += qty;
    } else {
      items.push({
        id: productId,
        name: productName,
        price: productPrice,
        image: productImage,
        quantity: qty,
      });
    }

    saveCart(items);
    showToast(`Added ${qty} × ${productName} to cart`);
  }

  function showToast(message) {
    if (!toast || !toastMessage) return;

    toastMessage.textContent = message;
    toast.hidden = false;
    toast.classList.add('is-visible');

    if (toastTimer) window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => {
      toast.classList.remove('is-visible');
      window.setTimeout(() => {
        toast.hidden = true;
      }, 300);
    }, 2800);
  }

  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', addToCart);
  }

  // Sync badge on load from localStorage (store.js owns badges when present)
  if (!window.MandiraStore) {
    updateCartBadge(getCart());
  }

  // Reviews
  function formatReviewDate(date) {
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  }

  function renderStars(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
      html += `<i class="bi bi-star${i <= rating ? '-fill' : ''}"></i>`;
    }
    return html;
  }

  function appendReview({ name, rating, text }) {
    const article = document.createElement('article');
    article.className = 'review-card';
    const now = new Date();
    const iso = now.toISOString().slice(0, 10);

    article.innerHTML = `
      <div class="review-card__header">
        <strong class="review-card__name"></strong>
        <time class="review-card__date" datetime="${iso}"></time>
      </div>
      <div class="review-card__stars" aria-label="${rating} out of 5 stars"></div>
      <p class="review-card__text"></p>
    `;

    article.querySelector('.review-card__name').textContent = name;
    article.querySelector('.review-card__date').textContent = formatReviewDate(now);
    article.querySelector('.review-card__stars').innerHTML = renderStars(rating);
    article.querySelector('.review-card__text').textContent = text;

    reviewsList.prepend(article);
  }

  if (reviewForm) {
    reviewForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const name = document.getElementById('reviewName').value.trim();
      const rating = Number(document.getElementById('reviewRating').value);
      const text = document.getElementById('reviewText').value.trim();

      if (!name || !rating || !text) return;

      appendReview({ name, rating, text });
      reviewForm.reset();
      showToast('Thank you! Your review has been posted.');
    });
  }
})();
