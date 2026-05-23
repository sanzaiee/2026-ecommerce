@auth
    @if (auth()->user()->isCustomer())
        <div class="nav-account dropdown">
            <button type="button" class="nav-icon-btn dropdown-toggle border-0 bg-transparent p-0"
                data-bs-toggle="dropdown" aria-expanded="false"
                aria-label="Account menu for {{ auth()->user()->name }}">
                <i class="bi bi-person"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end nav-account__menu">
                <li class="nav-account__header">
                    <span class="nav-account__name">{{ auth()->user()->name }}</span>
                    <span class="nav-account__email">{{ auth()->user()->email }}</span>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('account') }}">
                        <i class="bi bi-grid" aria-hidden="true"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('account.orders') }}">
                        <i class="bi bi-clock-history" aria-hidden="true"></i> Orders
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('account.profile') }}">
                        <i class="bi bi-person-gear" aria-hidden="true"></i> Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('account.wishlist') }}">
                        <i class="bi bi-heart" aria-hidden="true"></i> Wishlist
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item nav-account__logout">
                            <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Sign out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    @endif
@endauth
