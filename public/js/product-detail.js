(function () {
  const root = document.getElementById('productDetail');
  if (!root) return;

  const productId = root.dataset.productId;
  const inStock = root.dataset.inStock === '1';

  const mainImage = document.getElementById('productMainImage');
  const thumbs = document.querySelectorAll('.product-gallery__thumb');
  const qtyInput = document.getElementById('productQty');
  const qtyDecrease = document.getElementById('qtyDecrease');
  const qtyIncrease = document.getElementById('qtyIncrease');
  const addToCartBtn = document.getElementById('addToCartBtn');
  const wishlistBtn = document.getElementById('productWishlist');
  const reviewForm = document.getElementById('reviewForm');
  const reviewsList = document.getElementById('reviewsList');

  const store = () => window.MandiraStore;

  function productPayload() {
    if (store()?.normalizeProduct) {
      return store().normalizeProduct({
        id: root.dataset.productId,
        name: root.dataset.productName,
        price: root.dataset.productPrice,
        image: root.dataset.productImage,
        url: root.dataset.productUrl,
        inStock,
      });
    }

    return {
      id: productId,
      name: root.dataset.productName,
      price: Number(root.dataset.productPrice) || 0,
      image: root.dataset.productImage || '',
      url: root.dataset.productUrl || '',
      inStock,
    };
  }

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

  function getQty() {
    const val = parseInt(qtyInput.value, 10);
    return Number.isFinite(val) && val >= 1 ? val : 1;
  }

  function setQty(val) {
    qtyInput.value = Math.min(99, Math.max(1, val));
  }

  qtyDecrease?.addEventListener('click', () => setQty(getQty() - 1));
  qtyIncrease?.addEventListener('click', () => setQty(getQty() + 1));
  qtyInput?.addEventListener('change', () => setQty(getQty()));

  addToCartBtn?.addEventListener('click', () => {
    const payload = productPayload();
    if (!payload || !store()) return;

    const added = store().addToCart(payload, getQty(), { openDrawer: true });
    if (added) {
      addToCartBtn.classList.add('is-added');
      window.setTimeout(() => addToCartBtn.classList.remove('is-added'), 700);
    }
  });

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
    if (!reviewsList) return;

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

  reviewForm?.addEventListener('submit', (e) => {
    e.preventDefault();

    const name = document.getElementById('reviewName')?.value.trim();
    const rating = Number(document.getElementById('reviewRating')?.value);
    const text = document.getElementById('reviewText')?.value.trim();

    if (!name || !rating || !text) return;

    appendReview({ name, rating, text });
    reviewForm.reset();
    store()?.showToast?.('Thank you! Your review has been posted.');
  });
})();
