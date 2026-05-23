<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — {{ $site['siteName'] ?? config('app.name') }} CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body class="admin-body">
    <nav class="navbar navbar-dark admin-nav">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">{{ $site['siteName'] ?? config('app.name') }} <span>CMS</span></a>
            @auth
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('home') }}" class="text-white-50 small" target="_blank">View store</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                    </form>
                </div>
            @endauth
        </div>
    </nav>

    @auth
        <div class="container-fluid">
            <div class="row">
                <aside class="col-md-3 col-lg-2 admin-sidebar py-4">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                                href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                                href="{{ route('admin.orders.index') }}">Orders</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                                href="{{ route('admin.products.index') }}">Products</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                                href="{{ route('admin.categories.index') }}">Categories</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                                href="{{ route('admin.brands.index') }}">Brands</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.landing-page.*') ? 'active' : '' }}"
                                href="{{ route('admin.landing-page.edit') }}">Landing Page</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.about-page.*') ? 'active' : '' }}"
                                href="{{ route('admin.about-page.edit') }}">About Page</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                                href="{{ route('admin.settings.edit') }}">Settings</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}"
                                href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
                                href="{{ route('admin.reviews.index') }}">Reviews</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.contact-messages.*') ? 'active' : '' }}"
                                href="{{ route('admin.contact-messages.index') }}">Contact Messages</a></li>
                        <li class="nav-item"><a
                                class="nav-link {{ request()->routeIs('admin.newsletter-subscribers.*') ? 'active' : '' }}"
                                href="{{ route('admin.newsletter-subscribers.index') }}">Newsletter</a></li>
                    </ul>
                </aside>
                <main class="col-md-9 col-lg-10 admin-main py-4">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <main class="admin-main py-4">
            @yield('content')
        </main>
    @endauth
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @auth
        <script src="{{ asset('js/admin-media.js') }}" defer></script>
    @endauth
    @stack('scripts')
</body>

</html>
