<nav class="account-mobile-nav" aria-label="Account sections">
    <a href="{{ route('account') }}"
        class="account-mobile-nav__link @if (request()->routeIs('account')) active @endif">
        <i class="bi bi-grid" aria-hidden="true"></i>
        Dashboard
    </a>
    <a href="{{ route('account.orders') }}"
        class="account-mobile-nav__link @if (request()->routeIs('account.orders*')) active @endif">
        <i class="bi bi-clock-history" aria-hidden="true"></i>
        Orders
        @if ($orderCount > 0)
            <span class="account-mobile-nav__badge">{{ $orderCount }}</span>
        @endif
    </a>
    <a href="{{ route('account.wishlist') }}"
        class="account-mobile-nav__link @if (request()->routeIs('account.wishlist')) active @endif">
        <i class="bi bi-heart" aria-hidden="true"></i>
        Wishlist
        @if (($wishlistCount ?? 0) > 0)
            <span class="account-mobile-nav__badge">{{ $wishlistCount }}</span>
        @endif
    </a>
    <a href="{{ route('account.profile') }}"
        class="account-mobile-nav__link @if (request()->routeIs('account.profile', 'account.profile.update', 'account.password.update')) active @endif">
        <i class="bi bi-person-gear" aria-hidden="true"></i>
        Profile
    </a>
</nav>
