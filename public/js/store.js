(function () {
    const CART_KEY = "mandira_cart";
    const WISHLIST_KEY = "mandira_wishlist";
    const MIGRATION_KEY = "mandira_basket_db_v1";

    const config = window.MandiraStoreConfig || {};
    const routes = config.routes || {};
    const PLACEHOLDER_IMAGE =
        config.placeholderImage || "https://placehold.co/600x400.png";

    let cartCache = [];
    let wishlistCache = [];
    let cartBusy = false;

    const formatRs = (n) => "Rs. " + n.toLocaleString("en-NP");

    function readStorage(key) {
        try {
            const raw = localStorage.getItem(key);
            return raw ? JSON.parse(raw) : [];
        } catch {
            return [];
        }
    }

    function routeFor(template, id) {
        return String(template).replace("__ID__", encodeURIComponent(id));
    }

    async function api(method, url, body) {
        const csrf =
            document.querySelector('meta[name="csrf-token"]')?.content || "";
        const response = await fetch(url, {
            method,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": csrf,
                "X-Requested-With": "XMLHttpRequest",
            },
            credentials: "same-origin",
            body: body !== undefined ? JSON.stringify(body) : undefined,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const message =
                data.message ||
                data.errors?.product?.[0] ||
                Object.values(data.errors || {})?.flat?.()?.[0] ||
                "Something went wrong. Please try again.";
            throw new Error(message);
        }

        return data;
    }

    function getCart() {
        return cartCache;
    }

    function getWishlist() {
        return wishlistCache;
    }

    function applyCartState(data) {
        cartCache = (data.items || []).map((item) => ({
            ...normalizeProduct(item),
            qty: Number(item.qty) || 1,
        }));

        cartItemsList.innerHTML = "";
        cartCache.forEach((item) =>
            cartItemsList.appendChild(buildCartItemEl(item)),
        );
        refreshCartSummary(data);
    }

    function applyWishlistState(data) {
        wishlistCache = (data.items || [])
            .map((item) => normalizeProduct(item))
            .filter(Boolean);

        if (wishlistItemsList) {
            wishlistItemsList.innerHTML = "";
            wishlistCache.forEach((item) =>
                wishlistItemsList.appendChild(buildWishlistItemEl(item)),
            );
        }

        const isPaginatedWishlistPage = wishlistPageRoot?.hasAttribute(
            "data-wishlist-paginated",
        );

        if (wishlistPageList && !isPaginatedWishlistPage) {
            wishlistPageList.innerHTML = "";
            wishlistCache.forEach((item) =>
                wishlistPageList.appendChild(buildWishlistPageItemEl(item)),
            );
            wishlistPageList.hidden = wishlistCache.length === 0;
        }

        const count = data.count ?? wishlistCache.length;
        const isEmpty = count === 0;

        if (wishlistPanel) {
            wishlistPanel.classList.toggle("is-empty", isEmpty);
        }
        if (wishlistCountEl) {
            wishlistCountEl.textContent =
                count === 1 ? "1 item" : count + " items";
        }
        if (wishlistMoveAllBtn) {
            wishlistMoveAllBtn.disabled = isEmpty;
        }
        if (wishlistPageEmpty) {
            wishlistPageEmpty.hidden = !isEmpty;
        }
        if (wishlistPageCount) {
            wishlistPageCount.textContent =
                count === 1 ? "1 item saved" : count + " items saved";
        }
        if (wishlistPageMoveAll) {
            wishlistPageMoveAll.disabled = isEmpty;
        }
        if (wishlistPageRoot) {
            wishlistPageRoot.classList.toggle("is-empty", isEmpty);
        }

        updateWishlistBadges();
        syncWishlistButtons();
    }

    function refreshCartSummary(data) {
        const itemCount =
            data?.itemCount ??
            cartCache.reduce((sum, item) => sum + item.qty, 0);
        const subtotal =
            data?.subtotal ??
            cartCache.reduce((sum, item) => sum + item.price * item.qty, 0);
        const isEmpty = cartCache.length === 0;

        cartPanel.classList.toggle("is-empty", isEmpty);
        cartCountEl.textContent = isEmpty
            ? "0 items"
            : itemCount === 1
              ? "1 item"
              : itemCount + " items";
        cartSubtotalEl.textContent = formatRs(subtotal);
        cartTotalEl.textContent = formatRs(subtotal);
        if (cartPillTotal) cartPillTotal.textContent = formatRs(subtotal);
        if (cartCheckout) cartCheckout.disabled = isEmpty;

        setBadgeEl(cartBadge, itemCount, "count", "cart");
    }

    async function migrateLocalStorageIfNeeded() {
        if (localStorage.getItem(MIGRATION_KEY)) return;

        const legacyCart = readStorage(CART_KEY);
        const legacyWishlist = readStorage(WISHLIST_KEY);

        if (!legacyCart.length && !legacyWishlist.length) {
            localStorage.setItem(MIGRATION_KEY, "1");
            return;
        }

        try {
            if (legacyCart.length && routes.cartSync) {
                await api("POST", routes.cartSync, {
                    items: legacyCart.map((item) => ({
                        id: item.id,
                        qty: Number(item.qty ?? item.quantity ?? 1) || 1,
                    })),
                });
            }

            if (legacyWishlist.length && routes.wishlistSync) {
                await api("POST", routes.wishlistSync, {
                    items: legacyWishlist.map((item) => ({ id: item.id })),
                });
            }

            localStorage.removeItem(CART_KEY);
            localStorage.removeItem(WISHLIST_KEY);
            localStorage.setItem(MIGRATION_KEY, "1");
        } catch {
            // Keep legacy data if migration fails (offline, etc.)
        }
    }

    async function loadFromServer() {
        const [cartData, wishlistData] = await Promise.all([
            routes.cart
                ? api("GET", routes.cart)
                : Promise.resolve({ items: [] }),
            routes.wishlist
                ? api("GET", routes.wishlist)
                : Promise.resolve({ items: [] }),
        ]);

        applyCartState(cartData);
        applyWishlistState(wishlistData);
    }

    function escapeHtml(str) {
        const div = document.createElement("div");
        div.textContent = str;
        return div.innerHTML;
    }

    /** @typedef {{ id: string, name: string, price: number, image: string, url?: string, inStock?: boolean }} StoreProduct */

    /** @param {Partial<StoreProduct>} raw @returns {StoreProduct|null} */
    function normalizeProduct(raw) {
        if (!raw?.id) return null;

        return {
            id: String(raw.id),
            name: String(raw.name || "").trim(),
            price: Number(raw.price) || 0,
            image: String(raw.image || PLACEHOLDER_IMAGE),
            url: raw.url ? String(raw.url) : "",
            inStock: raw.inStock !== false && raw.inStock !== "0",
        };
    }

    /** @param {Record<string, string>|HTMLElement} source */
    function productFromDataset(source) {
        const el = source instanceof HTMLElement ? source : null;
        const d = el ? el.dataset : source;

        return normalizeProduct({
            id: d.productId,
            name: d.productName,
            price: d.productPrice,
            image: d.productImage,
            url: d.productUrl,
            inStock: d.inStock !== "0",
        });
    }

    function productFromCard(card, btn) {
        if (!card) return null;

        const id = btn?.dataset.productId || card.dataset.productId;
        if (!id) return null;

        const name =
            btn?.dataset.productName ||
            card.dataset.productName ||
            card
                .querySelector(".product-card__title-link")
                ?.textContent?.trim() ||
            card.querySelector(".product-name")?.textContent?.trim();

        const price =
            Number(
                btn?.dataset.productPrice ||
                    card.dataset.price ||
                    card.dataset.productPrice ||
                    0,
            ) || 0;

        const image =
            btn?.dataset.productImage ||
            card.dataset.productImage ||
            card.querySelector(".product-card__img--primary")?.src ||
            "";

        const url =
            btn?.dataset.productUrl ||
            card.dataset.productUrl ||
            card.querySelector(".product-card__title-link")?.href ||
            card.querySelector(".product-card__media-link")?.href ||
            "";

        const inStock =
            btn?.dataset.inStock !== "0" &&
            card.dataset.inStock !== "0" &&
            !card.classList.contains("product-card--oos");

        return normalizeProduct({ id, name, price, image, url, inStock });
    }

    function productFromDetailRoot(root) {
        if (!root) return null;

        return normalizeProduct({
            id: root.dataset.productId,
            name: root.dataset.productName,
            price: root.dataset.productPrice,
            image: root.dataset.productImage,
            url: root.dataset.productUrl,
            inStock: root.dataset.inStock === "1",
        });
    }

    // ——— Toast ———
    let toastTimer = null;

    function showToast(message, type = "success") {
        const toast = document.getElementById("storeToast");
        const messageEl = document.getElementById("storeToastMessage");
        const icon = toast?.querySelector("i");

        if (!toast || !messageEl) return;

        messageEl.textContent = message;
        toast.dataset.toastType = type;
        toast.hidden = false;
        toast.classList.add("is-visible");

        if (icon) {
            icon.className =
                type === "error"
                    ? "bi bi-exclamation-circle-fill"
                    : "bi bi-check-circle-fill";
        }

        if (toastTimer) window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => {
            toast.classList.remove("is-visible");
            window.setTimeout(() => {
                toast.hidden = true;
            }, 300);
        }, 2800);
    }

    // ——— Cart drawer refs (initialized after DOM check) ———
    const cartDrawer = document.getElementById("cartDrawer");
    if (!cartDrawer) return;

    const cartPanel = cartDrawer.querySelector('[data-panel="cart"]');
    const wishlistPanel = cartDrawer.querySelector('[data-panel="wishlist"]');
    const cartItemsList = document.getElementById("cartDrawerItems");
    const wishlistItemsList = document.getElementById("wishlistDrawerItems");
    const cartCountEl = document.getElementById("cartDrawerCount");
    const wishlistCountEl = document.getElementById("wishlistDrawerCount");
    const cartSubtotalEl = document.getElementById("cartSubtotal");
    const cartTotalEl = document.getElementById("cartTotal");
    const cartPillTotal = document.querySelector(".cart-pill__total");
    const cartBadge = document.querySelector(
        ".mobile-bottom-nav__badge[data-count]",
    );
    const wishlistNavBadge = document.querySelector(
        ".nav-icon-btn__badge[data-wishlist-count]",
    );
    const wishlistMobileBadge = document.querySelector(
        ".mobile-bottom-nav__wishlist-badge[data-wishlist-count]",
    );
    const cartCheckout = document.getElementById("cartCheckoutBtn");
    const wishlistMoveAllBtn = document.getElementById("wishlistMoveAllBtn");
    const wishlistPageList = document.getElementById("wishlistPageItems");
    const wishlistPageEmpty = document.getElementById("wishlistPageEmpty");
    const wishlistPageCount = document.getElementById("wishlistPageCount");
    const wishlistPageMoveAll = document.getElementById("wishlistPageMoveAll");
    const wishlistPageRoot = document.querySelector("[data-wishlist-page]");

    let drawerMode = "cart";
    let offcanvasInstance = null;

    function getOffcanvas() {
        if (!offcanvasInstance) {
            offcanvasInstance =
                bootstrap.Offcanvas.getOrCreateInstance(cartDrawer);
        }
        return offcanvasInstance;
    }

    function setDrawerMode(mode) {
        drawerMode = mode === "wishlist" ? "wishlist" : "cart";
        cartDrawer.dataset.drawerMode = drawerMode;
        cartPanel.hidden = drawerMode !== "cart";
        wishlistPanel.hidden = drawerMode !== "wishlist";
    }

    function closeMobileNavDrawer() {
        const mobileNav = document.getElementById("mobileNavDrawer");
        if (!mobileNav || typeof bootstrap === "undefined") {
            return;
        }

        bootstrap.Offcanvas.getInstance(mobileNav)?.hide();
    }

    function openDrawer(mode) {
        closeMobileNavDrawer();
        setDrawerMode(mode);
        getOffcanvas().show();
    }

    function buildCartItemEl(item) {
        const li = document.createElement("li");
        li.className = "cart-drawer__item";
        li.dataset.productId = item.id;
        li.dataset.unitPrice = String(item.price);

        const thumbHref = item.url || "#";
        const thumbLinkAttrs =
            item.url && item.url !== "#"
                ? `href="${escapeHtml(item.url)}"`
                : 'href="#" tabindex="-1" aria-hidden="true"';

        li.innerHTML = `
      <a ${thumbLinkAttrs} class="cart-drawer__thumb">
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
        const li = document.createElement("li");
        li.className = "cart-drawer__item cart-drawer__item--wishlist";
        li.dataset.productId = item.id;
        li.dataset.unitPrice = String(item.price);

        const thumbLinkAttrs =
            item.url && item.url !== "#"
                ? `href="${escapeHtml(item.url)}"`
                : 'href="#" tabindex="-1" aria-hidden="true"';

        li.innerHTML = `
      <a ${thumbLinkAttrs} class="cart-drawer__thumb">
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

    function buildWishlistPageItemEl(item) {
        const li = document.createElement("li");
        li.className = "account-wishlist-item";
        li.dataset.productId = item.id;
        li.dataset.unitPrice = String(item.price);

        const thumbLinkAttrs =
            item.url && item.url !== "#"
                ? `href="${escapeHtml(item.url)}"`
                : 'href="#" tabindex="-1" aria-hidden="true"';

        const stockHtml = item.inStock
            ? ""
            : '<p class="account-wishlist-item__stock">Out of stock</p>';
        const moveDisabled = item.inStock ? "" : " disabled";

        li.innerHTML = `
      <a ${thumbLinkAttrs} class="account-wishlist-item__media">
        <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" loading="lazy" decoding="async">
      </a>
      <div class="account-wishlist-item__body">
        <div class="account-wishlist-item__top">
          <h3 class="account-wishlist-item__name">
            <a href="${escapeHtml(item.url || "#")}">${escapeHtml(item.name)}</a>
          </h3>
          <button type="button" class="account-wishlist-item__remove cart-drawer__remove" aria-label="Remove ${escapeHtml(item.name)} from wishlist">
            <i class="bi bi-trash3" aria-hidden="true"></i>
          </button>
        </div>
        <p class="account-wishlist-item__price">${formatRs(item.price)}</p>
        ${stockHtml}
        <div class="account-wishlist-item__actions">
          <button type="button" class="account-btn account-btn--primary account-btn--sm" data-action="move-to-cart"${moveDisabled}>
            <i class="bi bi-bag-plus" aria-hidden="true"></i>
            Move to cart
          </button>
          <a href="${escapeHtml(item.url || "#")}" class="account-btn account-btn--outline account-btn--sm">View product</a>
        </div>
      </div>
    `;

        return li;
    }

    function setBadgeEl(badge, count, countAttr, labelPrefix) {
        if (!badge) return;

        badge.textContent = count;
        if (countAttr) badge.dataset[countAttr] = String(count);
        badge.classList.toggle("is-hidden", count <= 0);
        badge.setAttribute(
            "aria-label",
            count + (count === 1 ? " item" : " items") + " in " + labelPrefix,
        );
    }

    function updateWishlistBadges() {
        const count = getWishlist().length;
        [wishlistNavBadge, wishlistMobileBadge].forEach((badge) => {
            setBadgeEl(badge, count, "wishlistCount", "wishlist");
        });
    }

    function syncWishlistButtons() {
        const ids = new Set(getWishlist().map((item) => item.id));

        document.querySelectorAll('[data-action="wishlist"]').forEach((btn) => {
            const card = btn.closest(".product-card");
            const root = document.getElementById("productDetail");
            let id = btn.dataset.productId;

            if (!id && root && btn.closest("#productDetail")) {
                id = root.dataset.productId;
            }
            if (!id) {
                id = card?.dataset.productId;
            }
            if (!id) return;

            const active = ids.has(id);
            btn.classList.toggle("is-active", active);
            btn.setAttribute("aria-pressed", active ? "true" : "false");
            btn.setAttribute(
                "aria-label",
                active ? "Remove from wishlist" : "Add to wishlist",
            );
            const icon = btn.querySelector("i");
            if (icon)
                icon.className = active ? "bi bi-heart-fill" : "bi bi-heart";
        });
    }

    async function toggleWishlist(product) {
        const normalized = normalizeProduct(product);
        if (!normalized || !routes.wishlistItems) return false;

        try {
            const data = await api("POST", routes.wishlistItems, {
                id: normalized.id,
            });
            applyWishlistState(data.wishlist || data);
            showToast(
                data.added
                    ? `Saved ${normalized.name} to wishlist`
                    : `Removed ${normalized.name} from wishlist`,
            );
            return Boolean(data.added);
        } catch (error) {
            showToast(error.message, "error");
            return false;
        }
    }

    async function addToCart(product, qty = 1, options = {}) {
        const normalized = normalizeProduct(product);
        if (!normalized || !routes.cartItems) return false;

        if (!normalized.inStock) {
            showToast("This product is out of stock", "error");
            return false;
        }

        if (cartBusy) return false;
        cartBusy = true;

        try {
            const amount = Math.max(1, Number(qty) || 1);
            const data = await api("POST", routes.cartItems, {
                id: normalized.id,
                qty: amount,
            });
            applyCartState(data);

            if (options.toast !== false) {
                const label =
                    amount > 1
                        ? `${amount} × ${normalized.name}`
                        : normalized.name;
                showToast(`Added ${label} to cart`);
            }

            if (options.openDrawer) {
                openDrawer("cart");
            }

            return true;
        } catch (error) {
            showToast(error.message, "error");
            return false;
        } finally {
            cartBusy = false;
        }
    }

    async function updateCartQuantity(productId, qty) {
        if (!routes.cartItem) return;

        const data = await api("PATCH", routeFor(routes.cartItem, productId), {
            qty,
        });
        applyCartState(data);
    }

    async function removeCartItem(productId) {
        if (!routes.cartItem) return;

        const data = await api("DELETE", routeFor(routes.cartItem, productId));
        applyCartState(data);
    }

    async function removeWishlistItem(productId) {
        if (!routes.wishlistItem) return;

        const data = await api(
            "DELETE",
            routeFor(routes.wishlistItem, productId),
        );
        applyWishlistState(data);
    }

    async function moveWishlistItemToCart(productId) {
        if (!routes.wishlistMove) return;

        try {
            const data = await api(
                "POST",
                routeFor(routes.wishlistMove, productId),
            );
            if (data.cart) applyCartState(data.cart);
            if (data.wishlist) applyWishlistState(data.wishlist);
            const item = wishlistCache.find((entry) => entry.id === productId);
            showToast(`Moved ${item?.name || "item"} to cart`);
        } catch (error) {
            showToast(error.message, "error");
        }
    }

    async function moveAllWishlistToCart() {
        if (!routes.wishlistMoveAll) return;

        try {
            const data = await api("POST", routes.wishlistMoveAll);
            if (data.cart) applyCartState(data.cart);
            if (data.wishlist) applyWishlistState(data.wishlist);
            setDrawerMode("cart");
            showToast("Wishlist items moved to cart");
        } catch (error) {
            showToast(error.message, "error");
        }
    }

    function removeRowWithAnimation(row, onDone) {
        row.classList.add("is-removing");
        row.addEventListener(
            "animationend",
            () => {
                row.remove();
                onDone();
            },
            { once: true },
        );
    }

    // ——— Reels scroll ———
    const reels = document.getElementById("reelsScroll");
    if (reels) {
        reels.addEventListener(
            "wheel",
            (e) => {
                if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                    e.preventDefault();
                    reels.scrollLeft += e.deltaY;
                }
            },
            { passive: false },
        );
    }

    // ——— Mobile nav ———
    const mobileNavItems = document.querySelectorAll(
        ".mobile-bottom-nav__item",
    );
    const onHomePage =
        window.location.pathname === "/" || window.location.pathname === "";

    mobileNavItems.forEach((item) => {
        item.addEventListener("click", (e) => {
            if (
                item.dataset.nav === "cart" ||
                item.dataset.nav === "wishlist"
            ) {
                return;
            }

            if (item.dataset.nav === "home" && onHomePage) {
                e.preventDefault();
                mobileNavItems.forEach((el) => {
                    el.classList.remove("active");
                    el.removeAttribute("aria-current");
                });
                item.classList.add("active");
                item.setAttribute("aria-current", "page");
                window.scrollTo({ top: 0, behavior: "smooth" });
            }
        });
    });

    // ——— Drawer interactions ———
    cartItemsList.addEventListener("click", async (e) => {
        const item = e.target.closest(".cart-drawer__item");
        if (!item || cartBusy) return;

        const productId = item.dataset.productId;

        if (e.target.closest(".cart-drawer__remove")) {
            cartBusy = true;
            try {
                await removeCartItem(productId);
            } catch (error) {
                showToast(error.message, "error");
            } finally {
                cartBusy = false;
            }
            return;
        }

        const action = e.target.closest("[data-action]")?.dataset.action;
        if (!action) return;

        const qtyEl = item.querySelector(".cart-drawer__qty-value");
        let qty = Number(qtyEl.textContent) || 1;
        if (action === "increase") qty += 1;
        if (action === "decrease" && qty > 1) qty -= 1;

        cartBusy = true;
        try {
            await updateCartQuantity(productId, qty);
        } catch (error) {
            showToast(error.message, "error");
        } finally {
            cartBusy = false;
        }
    });

    async function handleWishlistItemClick(e, itemSelector) {
        const item = e.target.closest(itemSelector);
        if (!item) return;

        const productId = item.dataset.productId;
        const isPaginatedPage = wishlistPageRoot?.hasAttribute(
            "data-wishlist-paginated",
        );

        if (e.target.closest(".cart-drawer__remove")) {
            try {
                await removeWishlistItem(productId);
                if (isPaginatedPage) {
                    item.remove();
                    if (
                        wishlistPageList &&
                        wishlistPageList.children.length === 0
                    ) {
                        window.location.reload();
                    }
                }
            } catch (error) {
                showToast(error.message, "error");
            }
            return;
        }

        if (e.target.closest('[data-action="move-to-cart"]')) {
            await moveWishlistItemToCart(productId);
            if (isPaginatedPage) {
                item.remove();
                if (
                    wishlistPageList &&
                    wishlistPageList.children.length === 0
                ) {
                    window.location.reload();
                }
            }
        }
    }

    wishlistItemsList?.addEventListener("click", (e) => {
        handleWishlistItemClick(e, ".cart-drawer__item");
    });

    wishlistPageList?.addEventListener("click", (e) => {
        handleWishlistItemClick(e, ".account-wishlist-item");
    });

    wishlistMoveAllBtn?.addEventListener("click", () => {
        moveAllWishlistToCart();
    });

    cartCheckout?.addEventListener("click", () => {
        if (cartCache.length === 0) return;
        if (config.auth && !config.auth.isCustomer) {
            window.location.href = config.auth.login || "/login";
            return;
        }
        const checkoutUrl = routes.checkout || "/store/checkout";
        window.location.href = checkoutUrl;
    });

    cartDrawer.addEventListener("show.bs.offcanvas", () => {
        requestAnimationFrame(() => {
            document
                .querySelector(".offcanvas-backdrop")
                ?.classList.add("cart-drawer-backdrop");
        });
    });

    cartDrawer.addEventListener("hidden.bs.offcanvas", () => {
        document
            .querySelector(".cart-drawer-backdrop")
            ?.classList.remove("cart-drawer-backdrop");
    });

    document.querySelectorAll("[data-drawer-open]").forEach((trigger) => {
        trigger.addEventListener("click", (e) => {
            e.preventDefault();
            openDrawer(trigger.dataset.drawerOpen || "cart");
        });
    });

    document
        .querySelectorAll(
            '[data-bs-target="#cartDrawer"]:not([data-drawer-open])',
        )
        .forEach((trigger) => {
            trigger.addEventListener("click", (e) => {
                e.preventDefault();
                openDrawer("cart");
            });
        });

    document.addEventListener("click", (e) => {
        const wishlistBtn = e.target.closest('[data-action="wishlist"]');
        if (wishlistBtn) {
            e.preventDefault();
            e.stopPropagation();

            const card = wishlistBtn.closest(".product-card");
            const root = document.getElementById("productDetail");
            let product = productFromCard(card, wishlistBtn);

            if (!product && root && wishlistBtn.closest("#productDetail")) {
                product = productFromDetailRoot(root);
            }

            if (!product) {
                product = productFromDataset(wishlistBtn);
            }

            if (product) toggleWishlist(product);
            return;
        }

        const cartBtn = e.target.closest('[data-action="add-to-cart"]');
        if (cartBtn) {
            e.preventDefault();
            e.stopPropagation();

            const card = cartBtn.closest(".product-card");
            let product = productFromCard(card, cartBtn);

            if (!product) {
                product = productFromDataset(cartBtn);
            }

            if (!product) return;

            addToCart(product, 1, { openDrawer: true }).then((added) => {
                if (added) {
                    cartBtn.classList.add("is-added");
                    window.setTimeout(
                        () => cartBtn.classList.remove("is-added"),
                        700,
                    );
                }
            });
        }
    });

    // ——— Boot ———
    (async function boot() {
        try {
            await migrateLocalStorageIfNeeded();
            await loadFromServer();
        } catch {
            showToast(
                "Could not load your cart. Please refresh the page.",
                "error",
            );
        }
    })();

    window.MandiraStore = {
        openDrawer,
        toggleWishlist,
        addToCart,
        getCart,
        getWishlist,
        moveWishlistItemToCart,
        moveAllWishlistToCart,
        formatRs,
        showToast,
        normalizeProduct,
    };
})();
