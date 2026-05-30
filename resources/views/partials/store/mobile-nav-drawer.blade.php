@php
    $menuItems = $menuItems ?? [];
    $cartTotal = $cartTotal ?? 'Rs. 0';
@endphp

<div class="offcanvas offcanvas-start mobile-nav-drawer" tabindex="-1" id="mobileNavDrawer"
    aria-labelledby="mobileNavDrawerTitle" data-bs-backdrop="true" data-bs-scroll="false">
    <div class="mobile-nav-drawer__inner">
        <header class="mobile-nav-drawer__header">
            <p class="mobile-nav-drawer__title" id="mobileNavDrawerTitle">Menu</p>
            <button type="button" class="mobile-nav-drawer__close" data-bs-dismiss="offcanvas"
                aria-label="Close menu">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </header>

        <div class="mobile-nav-drawer__body">
            @include('partials.store.nav-menu-links', [
                'menuItems' => $menuItems,
                'dismissOnNavigate' => true,
            ])

            @include('partials.store.header-search', ['class' => 'nav-search--mobile'])

            <div class="mobile-nav-drawer__actions">
                <a href="{{ auth()->check() && auth()->user()->isCustomer() ? route('account.wishlist') : route('store.wishlist.show') }}"
                    class="mobile-nav-drawer__action"
                    aria-label="Open wishlist"
                    @guest
                        data-drawer-open="wishlist" data-bs-dismiss="offcanvas" role="button"
                    @else
                        @if (!auth()->user()->isCustomer())
                            data-drawer-open="wishlist" data-bs-dismiss="offcanvas" role="button"
                        @else
                            data-bs-dismiss="offcanvas"
                        @endif
                    @endguest>
                    <i class="bi bi-heart" aria-hidden="true"></i>
                    <span>Wishlist</span>
                </a>

                @guest
                    <a href="{{ route('login') }}" class="mobile-nav-drawer__action" data-bs-dismiss="offcanvas">
                        <i class="bi bi-person" aria-hidden="true"></i>
                        <span>Sign in</span>
                    </a>
                @else
                    @if (auth()->user()->isCustomer())
                        <a href="{{ route('account') }}" class="mobile-nav-drawer__action" data-bs-dismiss="offcanvas">
                            <i class="bi bi-grid" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('account.orders') }}" class="mobile-nav-drawer__action"
                            data-bs-dismiss="offcanvas">
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('account.profile') }}" class="mobile-nav-drawer__action"
                            data-bs-dismiss="offcanvas">
                            <i class="bi bi-person-gear" aria-hidden="true"></i>
                            <span>Profile</span>
                        </a>
                    @endif
                @endguest

                <a href="#" class="mobile-nav-drawer__action mobile-nav-drawer__action--cart"
                    aria-label="Open cart" data-drawer-open="cart" data-bs-dismiss="offcanvas" role="button">
                    <i class="bi bi-bag" aria-hidden="true"></i>
                    <span>Cart</span>
                    <span class="mobile-nav-drawer__cart-total">{{ $cartTotal }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
