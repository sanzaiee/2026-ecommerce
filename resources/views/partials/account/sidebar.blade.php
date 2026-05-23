<aside class="account-sidebar" aria-label="Account navigation">
    <div class="account-card account-sidebar__profile">
        <div class="account-card__avatar" aria-hidden="true">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="account-card__name">{{ $user->name }}</p>
        <p class="account-card__email">{{ $user->email }}</p>
        <p class="account-card__meta">
            Member since {{ $user->created_at->format('F Y') }}
        </p>
    </div>

    <nav>
        <ul class="account-nav">
            <li>
                <a href="{{ route('account') }}"
                    class="account-nav__link @if (request()->routeIs('account')) active @endif"
                    @if (request()->routeIs('account')) aria-current="page" @endif>
                    <i class="bi bi-grid" aria-hidden="true"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('account.orders') }}"
                    class="account-nav__link @if (request()->routeIs('account.orders*')) active @endif"
                    @if (request()->routeIs('account.orders*')) aria-current="page" @endif>
                    <i class="bi bi-clock-history" aria-hidden="true"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="{{ route('account.wishlist') }}"
                    class="account-nav__link @if (request()->routeIs('account.wishlist')) active @endif"
                    @if (request()->routeIs('account.wishlist')) aria-current="page" @endif>
                    <i class="bi bi-heart" aria-hidden="true"></i>
                    <span>Wishlist</span>
                </a>
            </li>
            <li>
                <a href="{{ route('account.addresses') }}"
                    class="account-nav__link @if (request()->routeIs('account.addresses')) active @endif"
                    @if (request()->routeIs('account.addresses')) aria-current="page" @endif>
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    <span>Addresses</span>
                </a>
            </li>
            <li>
                <a href="{{ route('account.profile') }}"
                    class="account-nav__link @if (request()->routeIs('account.profile', 'account.profile.update', 'account.password.update')) active @endif"
                    @if (request()->routeIs('account.profile', 'account.profile.update', 'account.password.update')) aria-current="page" @endif>
                    <i class="bi bi-person-gear" aria-hidden="true"></i>
                    <span>Profile &amp; security</span>
                </a>
            </li>
        </ul>

        <form method="POST" action="{{ route('logout') }}" class="account-sidebar__logout">
            @csrf
            <button type="submit" class="account-btn account-btn--outline">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                Sign out
            </button>
        </form>
    </nav>
</aside>
