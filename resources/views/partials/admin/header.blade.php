<header class="admin-topbar">
    <div class="admin-topbar__start">
        <button type="button" class="admin-topbar__menu-btn d-lg-none" data-admin-sidebar-open
            aria-label="Open menu" aria-controls="adminSidebar" aria-expanded="false">
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>
        <div class="admin-topbar__titles">
            <h1 class="admin-topbar__title">@yield('title', 'Admin')</h1>
            @hasSection('subtitle')
                <p class="admin-topbar__subtitle">@yield('subtitle')</p>
            @endif
        </div>
    </div>

    <div class="admin-topbar__actions">
        @include('partials.admin.theme-toggle')

        <a href="{{ route('home') }}" class="admin-topbar__link d-none d-sm-inline-flex" target="_blank"
            rel="noopener">
            <i class="bi bi-shop" aria-hidden="true"></i>
            <span>Store</span>
        </a>

        <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="admin-topbar__logout">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </button>
        </form>
    </div>
</header>
