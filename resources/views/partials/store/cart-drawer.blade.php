<div class="offcanvas offcanvas-end cart-drawer" tabindex="-1" id="cartDrawer" data-drawer-mode="cart"
    aria-labelledby="cartDrawerTitle" data-bs-backdrop="true" data-bs-scroll="false">
    <div class="cart-drawer__inner">
        {{-- Cart panel --}}
        <div class="cart-drawer__panel" data-panel="cart">
            <header class="cart-drawer__header">
                <div class="cart-drawer__header-text">
                    <h2 class="cart-drawer__title" id="cartDrawerTitle">Your Cart</h2>
                    <p class="cart-drawer__count" id="cartDrawerCount">0 items</p>
                </div>
                <button type="button" class="cart-drawer__close" data-bs-dismiss="offcanvas"
                    aria-label="Close cart">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </header>

            <div class="cart-drawer__body">
                <div class="cart-drawer__empty" id="cartDrawerEmpty">
                    <div class="cart-drawer__empty-icon" aria-hidden="true">
                        <i class="bi bi-bag"></i>
                    </div>
                    <p class="cart-drawer__empty-title">Your cart is empty</p>
                    <p class="cart-drawer__empty-text">Looks like you haven&rsquo;t added anything yet. Explore our
                        premium dried fruits and pickles.</p>
                    <a href="{{ url('/shop') }}" class="cart-drawer__empty-btn" data-bs-dismiss="offcanvas">Continue
                        shopping</a>
                </div>

                <ul class="cart-drawer__items" id="cartDrawerItems" aria-label="Cart items"></ul>
            </div>

            <footer class="cart-drawer__footer">
                <div class="cart-drawer__summary">
                    <div class="cart-drawer__summary-row">
                        <span>Subtotal</span>
                        <span id="cartSubtotal">Rs. 0</span>
                    </div>
                    <div class="cart-drawer__summary-row cart-drawer__summary-row--total">
                        <span>Total</span>
                        <span id="cartTotal">Rs. 0</span>
                    </div>
                </div>
                <button type="button" class="cart-drawer__checkout" id="cartCheckoutBtn">
                    @auth
                        @if (auth()->user()->isCustomer())
                            Proceed to Checkout
                        @else
                            Sign in to Checkout
                        @endif
                    @else
                        Sign in to Checkout
                    @endauth
                </button>
            </footer>
        </div>

        {{-- Wishlist panel --}}
        <div class="cart-drawer__panel" data-panel="wishlist" hidden>
            <header class="cart-drawer__header">
                <div class="cart-drawer__header-text">
                    <h2 class="cart-drawer__title" id="wishlistDrawerTitle">Your Wishlist</h2>
                    <p class="cart-drawer__count" id="wishlistDrawerCount">0 items</p>
                </div>
                <button type="button" class="cart-drawer__close" data-bs-dismiss="offcanvas"
                    aria-label="Close wishlist">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </header>

            <div class="cart-drawer__body">
                <div class="cart-drawer__empty" id="wishlistDrawerEmpty">
                    <div class="cart-drawer__empty-icon cart-drawer__empty-icon--wishlist" aria-hidden="true">
                        <i class="bi bi-heart"></i>
                    </div>
                    <p class="cart-drawer__empty-title">Your wishlist is empty</p>
                    <p class="cart-drawer__empty-text">Save items you love and come back to them anytime.</p>
                    <a href="{{ url('/shop') }}" class="cart-drawer__empty-btn" data-bs-dismiss="offcanvas">Browse
                        products</a>
                </div>

                <ul class="cart-drawer__items" id="wishlistDrawerItems" aria-label="Wishlist items"></ul>
            </div>

            <footer class="cart-drawer__footer">
                <button type="button" class="cart-drawer__checkout" id="wishlistMoveAllBtn">Move all to cart</button>
            </footer>
        </div>
    </div>
</div>
