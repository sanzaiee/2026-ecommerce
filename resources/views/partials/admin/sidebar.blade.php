@php
    $navGroups = [
        [
            'label' => 'Overview',
            'items' => [
                ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
            ],
        ],
        [
            'label' => 'Commerce',
            'items' => [
                ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'label' => 'Orders', 'icon' => 'bi-bag-check'],
                ['route' => 'admin.customers.index', 'match' => 'admin.customers.*', 'label' => 'Customers', 'icon' => 'bi-people'],
                ['route' => 'admin.stock.index', 'match' => 'admin.stock.*', 'label' => 'Stock', 'icon' => 'bi-box-seam'],
                ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'label' => 'Products', 'icon' => 'bi-box-seam'],
                ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'label' => 'Categories', 'icon' => 'bi-tags'],
                ['route' => 'admin.brands.index', 'match' => 'admin.brands.*', 'label' => 'Brands', 'icon' => 'bi-award'],
            ],
        ],
        [
            'label' => 'Content',
            'items' => [
                ['route' => 'admin.landing-page.edit', 'match' => 'admin.landing-page.*', 'label' => 'Landing Page', 'icon' => 'bi-house-door'],
                ['route' => 'admin.about-page.edit', 'match' => 'admin.about-page.*', 'label' => 'About Page', 'icon' => 'bi-info-circle'],
                ['route' => 'admin.testimonials.index', 'match' => 'admin.testimonials.*', 'label' => 'Testimonials', 'icon' => 'bi-chat-quote'],
                ['route' => 'admin.reviews.index', 'match' => 'admin.reviews.*', 'label' => 'Reviews', 'icon' => 'bi-star'],
            ],
        ],
        [
            'label' => 'Engagement',
            'items' => [
                ['route' => 'admin.contact-messages.index', 'match' => 'admin.contact-messages.*', 'label' => 'Contact Messages', 'icon' => 'bi-envelope'],
                ['route' => 'admin.newsletter-subscribers.index', 'match' => 'admin.newsletter-subscribers.*', 'label' => 'Newsletter', 'icon' => 'bi-newspaper'],
            ],
        ],
        [
            'label' => 'System',
            'items' => [
                ['route' => 'admin.settings.edit', 'match' => 'admin.settings.*', 'label' => 'Settings', 'icon' => 'bi-gear'],
            ],
        ],
    ];
@endphp

<aside class="admin-sidebar" id="adminSidebar" aria-label="Admin navigation">
    <div class="admin-sidebar__head">
        <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand">
            <span class="admin-sidebar__mark" aria-hidden="true"><i class="bi bi-grid-1x2-fill"></i></span>
            <span class="admin-sidebar__brand-text">
                <span class="admin-sidebar__site">{{ $site['siteName'] ?? config('app.name') }}</span>
                <span class="admin-sidebar__cms">CMS</span>
            </span>
        </a>
        <button type="button" class="admin-sidebar__close d-lg-none" data-admin-sidebar-close
            aria-label="Close menu">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="admin-sidebar__nav">
        @foreach ($navGroups as $group)
            <div class="admin-sidebar__group">
                <p class="admin-sidebar__group-label">{{ $group['label'] }}</p>
                <ul class="admin-sidebar__list">
                    @foreach ($group['items'] as $item)
                        @php $active = request()->routeIs($item['match']); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="admin-sidebar__link @if ($active) is-active @endif"
                                @if ($active) aria-current="page" @endif>
                                <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="admin-sidebar__foot">
        <a href="{{ route('home') }}" class="admin-sidebar__foot-link" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
            <span>View store</span>
        </a>
    </div>
</aside>
<div class="admin-sidebar-backdrop" id="adminSidebarBackdrop" hidden data-admin-sidebar-close></div>
