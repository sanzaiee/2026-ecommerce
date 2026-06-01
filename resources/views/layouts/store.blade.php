<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $site = $site ?? []; @endphp
    <title>@yield('title', ($site['defaultMetaTitle'] ?? null) ?: (($site['siteName'] ?? 'Mandira') . ' ' . ($site['brandSuffix'] ?? 'Foods') . ' — Premium Dried Fruits & Pickles'))</title>
    @if (!empty($site['defaultMetaDescription']))
        <meta name="description" content="{{ $site['defaultMetaDescription'] }}">
    @endif
    @if (!empty($site['faviconUrl']))
        <link rel="icon" href="{{ $site['faviconUrl'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/store.css') }}?v={{ filemtime(public_path('css/store.css')) ?: 1 }}" rel="stylesheet">
    @include('partials.store.theme-vars')
    @stack('styles')
</head>
<body>
    <x-store.header :cart-total="$cartTotal ?? 'Rs. 0'" />

    @yield('content')

    <x-store.footer />

    @include('partials.store.mobile-nav')
    @include('partials.store.cart-drawer')

    <div class="store-toast" id="storeToast" role="status" aria-live="polite" aria-atomic="true" hidden>
        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <span id="storeToastMessage"></span>
    </div>

    <script>
        window.MandiraStoreConfig = {
            placeholderImage: @json(get_placeholder_image()),
            auth: {
                isCustomer: @json(auth()->check() && auth()->user()?->isCustomer()),
                login: @json(route('login')),
            },
            routes: {
                cart: @json(route('store.cart.show')),
                cartItems: @json(route('store.cart.items.store')),
                cartSync: @json(route('store.cart.sync')),
                cartItem: @json(url('/store/cart/items/__ID__')),
                wishlist: @json(route('store.wishlist.show')),
                wishlistItems: @json(route('store.wishlist.items.store')),
                wishlistSync: @json(route('store.wishlist.sync')),
                wishlistItem: @json(url('/store/wishlist/items/__ID__')),
                wishlistMove: @json(url('/store/wishlist/items/__ID__/move-to-cart')),
                wishlistMoveAll: @json(route('store.wishlist.move-all')),
                checkout: @json(route('store.checkout.show')),
            },
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/store.js') }}?v={{ filemtime(public_path('js/store.js')) ?: 1 }}" defer></script>
    <script src="{{ asset('js/store-search.js') }}?v={{ filemtime(public_path('js/store-search.js')) ?: 1 }}" defer></script>
    @stack('scripts')
</body>
</html>
