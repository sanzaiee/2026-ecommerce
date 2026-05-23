@props([
    'promo' => 'Free shipping on orders above Rs. 2,000',
    'promoSecondary' => '100% natural products',
    'cartTotal' => 'Rs. 0',
    'menuItems' => [
        ['label' => 'All Products', 'href' => url('/shop')],
        ['label' => 'Dried Fruits', 'href' => url('/shop') . '?category=dried-fruits'],
        ['label' => 'Pickles', 'href' => url('/shop') . '?category=pickles'],
    ],
])

<div class="top-strip">
    {{ $promo }}<span>|</span>{{ $promoSecondary }}
</div>

<nav class="main-navbar navbar navbar-expand-lg" {{ $attributes }}>
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <div class="brand-text">Mandira<span>Foods</span></div>
        </a>

        <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav nav-menu mb-2 mb-lg-0">
                @foreach ($menuItems as $item)
                    <li class="nav-item">
                        <a class="nav-link nav-menu__link" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                    </li>
                @endforeach
            </ul>

            <div class="nav-right-zone">
                <div class="search-bar d-none d-lg-block">
                    <input type="search" placeholder="Search products..." aria-label="Search">
                    <i class="bi bi-search search-icon"></i>
                </div>

                <div class="nav-icons">
                    <a href="#" class="nav-icon-btn" aria-label="Open wishlist" data-drawer-open="wishlist"
                        role="button">
                        <i class="bi bi-heart"></i>
                        <span class="nav-icon-btn__badge" data-wishlist-count="0" aria-hidden="true">0</span>
                    </a>
                    <a href="{{ auth()->check() ? url('/') : route('login') }}" class="nav-icon-btn"
                        aria-label="{{ auth()->check() ? 'My account' : 'Sign in' }}">
                        <i class="bi bi-person"></i>
                    </a>
                    <a href="#" class="cart-pill" aria-label="Open cart" data-drawer-open="cart" role="button">
                        <i class="bi bi-bag"></i>
                        <span class="cart-pill__total">{{ $cartTotal }}</span>
                    </a>
                </div>
            </div>

            <div class="search-bar d-lg-none w-100">
                <input type="search" placeholder="Search products..." aria-label="Search">
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
    </div>
</nav>
