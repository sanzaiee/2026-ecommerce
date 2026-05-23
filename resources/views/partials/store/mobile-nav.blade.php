@php
    $wishlistHref =
        auth()->check() && auth()->user()->isCustomer()
            ? route('account.wishlist')
            : route('store.wishlist.show');
    $wishlistOpensDrawer = !auth()->check() || !auth()->user()->isCustomer();
@endphp

<nav class="mobile-bottom-nav d-md-none" aria-label="Mobile navigation">
    <div class="mobile-bottom-nav__inner">
        <a href="{{ url('/') }}" class="mobile-bottom-nav__item @if (request()->routeIs('home')) active @endif"
            data-nav="home" @if (request()->routeIs('home')) aria-current="page" @endif>
            <i class="bi bi-house-door" aria-hidden="true"></i>
            <span>Home</span>
        </a>
        <a href="{{ $wishlistOpensDrawer ? '#' : $wishlistHref }}"
            class="mobile-bottom-nav__item @if (request()->routeIs('account.wishlist') || ($wishlistOpensDrawer && request()->routeIs('store.wishlist.show'))) active @endif"
            data-nav="wishlist"
            @if ($wishlistOpensDrawer) data-drawer-open="wishlist" role="button" @endif
            aria-label="Wishlist">
            <i class="bi bi-heart" aria-hidden="true"></i>
            <span class="mobile-bottom-nav__wishlist-badge is-hidden" data-wishlist-count="0"
                aria-hidden="true">0</span>
            <span>Wishlist</span>
        </a>
        <a href="#" class="mobile-bottom-nav__item" data-nav="cart" data-drawer-open="cart" role="button"
            aria-label="Open cart">
            <i class="bi bi-bag" aria-hidden="true"></i>
            <span class="mobile-bottom-nav__badge is-hidden" data-count="0" aria-label="0 items in cart">0</span>
            <span>Cart</span>
        </a>
        <a href="{{ auth()->check() ? route('account') : route('login') }}"
            class="mobile-bottom-nav__item @if (request()->routeIs('account', 'account.*')) active @endif"
            data-nav="account" @if (request()->routeIs('account', 'account.*')) aria-current="page" @endif>
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>Account</span>
        </a>
    </div>
</nav>
