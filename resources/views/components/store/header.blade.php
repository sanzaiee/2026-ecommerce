@props([
    'promo' => 'Free shipping on orders above Rs. 2,000',
    'promoSecondary' => '100% natural products',
    'cartTotal' => 'Rs. 0',
    'siteName' => null,
    'brandSuffix' => null,
    'logoUrl' => null,
    'menuItems' => [
        ['label' => 'All Products', 'href' => url('/shop')],
        ['label' => 'Dried Fruits', 'href' => url('/shop') . '?category=dried-fruits'],
        ['label' => 'Pickles', 'href' => url('/shop') . '?category=pickles'],
    ],
])

@php
    $site = $site ?? [];
    $siteName = $siteName ?? ($site['siteName'] ?? 'Mandira');
    $brandSuffix = $brandSuffix ?? ($site['brandSuffix'] ?? 'Foods');
    $logoUrl = $logoUrl ?? ($site['logoUrl'] ?? null);
    $promo = $promo ?? ($site['promoPrimary'] ?? null);
    $promoSecondary = $promoSecondary ?? ($site['promoSecondary'] ?? null);
@endphp

@if ($promo || $promoSecondary)
    <div class="top-strip">
        @if ($promo && $promoSecondary)
            {{ $promo }}<span>|</span>{{ $promoSecondary }}
        @elseif ($promo)
            {{ $promo }}
        @else
            {{ $promoSecondary }}
        @endif
    </div>
@endif

<nav class="main-navbar navbar navbar-expand-lg" {{ $attributes }}>
    <div class="container">
        <a class="navbar-brand @if ($logoUrl) navbar-brand--has-logo @endif"
            href="{{ url('/') }}">
            @if ($logoUrl)
                <span class="navbar-brand__mark">
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }} {{ $brandSuffix }}" class="navbar-brand__logo"
                        width="520" height="170" decoding="async" fetchpriority="high">
                </span>
            @else
                <span class="brand-text">{{ $siteName }}<span>{{ $brandSuffix }}</span></span>
            @endif
        </a>

        <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#mobileNavDrawer" aria-controls="mobileNavDrawer" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse d-none d-lg-flex" id="mainNav">
            <div class="navbar-collapse__body">
                @include('partials.store.nav-menu-links', ['menuItems' => $menuItems])

                <div class="navbar-collapse__actions">
                    @include('partials.store.header-search', ['class' => 'nav-search--mobile d-lg-none'])

                    <div class="nav-right-zone">
                        @include('partials.store.header-search', [
                            'class' => 'nav-search--desktop d-none d-lg-block',
                        ])

                        <div class="nav-icons">
                            <a href="{{ auth()->check() && auth()->user()->isCustomer() ? route('account.wishlist') : route('store.wishlist.show') }}"
                                class="nav-icon-btn" aria-label="Open wishlist"
                                @guest
data-drawer-open="wishlist" role="button"
                                @else
                                    @if (!auth()->user()->isCustomer())
                                        data-drawer-open="wishlist" role="button"
                                    @endif @endguest>
                                <i class="bi bi-heart"></i>
                                <span class="nav-icon-btn__badge is-hidden" data-wishlist-count="0"
                                    aria-hidden="true">0</span>
                            </a>
                            @guest
                                <a href="{{ route('login') }}" class="nav-icon-btn" aria-label="Sign in">
                                    <i class="bi bi-person"></i>
                                </a>
                            @else
                                <x-store.account-nav />
                            @endguest
                            <a href="#" class="cart-pill" aria-label="Open cart" data-drawer-open="cart"
                                role="button">
                                <i class="bi bi-bag"></i>
                                <span class="cart-pill__total">{{ $cartTotal }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

@include('partials.store.mobile-nav-drawer', [
    'menuItems' => $menuItems,
    'cartTotal' => $cartTotal,
])
