<nav class="mobile-bottom-nav d-md-none" aria-label="Mobile navigation">
    <div class="mobile-bottom-nav__inner">
        <a href="{{ url('/') }}" class="mobile-bottom-nav__item active" data-nav="home" aria-current="page">
            <i class="bi bi-house-door" aria-hidden="true"></i>
            <span>Home</span>
        </a>
        <a href="#" class="mobile-bottom-nav__item" data-nav="wishlist" data-drawer-open="wishlist" role="button"
            aria-label="Open wishlist">
            <i class="bi bi-heart" aria-hidden="true"></i>
            <span class="mobile-bottom-nav__wishlist-badge" data-wishlist-count="0" aria-hidden="true">0</span>
            <span>Wishlist</span>
        </a>
        <a href="#" class="mobile-bottom-nav__item" data-nav="cart" data-drawer-open="cart" role="button"
            aria-label="Open cart">
            <i class="bi bi-bag" aria-hidden="true"></i>
            <span class="mobile-bottom-nav__badge" data-count="2" aria-label="2 items in cart">2</span>
            <span>Cart</span>
        </a>
        <a href="{{ auth()->check() ? url('/') : route('login') }}" class="mobile-bottom-nav__item" data-nav="account">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>Account</span>
        </a>
    </div>
</nav>
