@extends('layouts.account')

@section('account-content')
    <div class="account-header">
        <h1 class="account-header__title">Welcome back, {{ strtok($user->name, ' ') }}</h1>
        <p class="account-header__lead">Manage orders, saved products, and your profile from one place.</p>
    </div>

    <div class="account-stats">
        <div class="account-stat">
            <span class="account-stat__icon" aria-hidden="true"><i class="bi bi-clock-history"></i></span>
            <span class="account-stat__label">Orders</span>
            <strong class="account-stat__value">{{ $orderCount }}</strong>
            <a href="{{ route('account.orders') }}" class="account-stat__link">View order history</a>
        </div>
        <div class="account-stat">
            <span class="account-stat__icon" aria-hidden="true"><i class="bi bi-heart"></i></span>
            <span class="account-stat__label">Wishlist</span>
            <strong class="account-stat__value">{{ $wishlistCount ?? 0 }}</strong>
            <a href="{{ route('account.wishlist') }}" class="account-stat__link">Open wishlist</a>
        </div>
    </div>

    <div class="account-card">
        <div class="account-card__head">
            <h2 class="account-card__title">Recent orders</h2>
            @if ($orders->isNotEmpty())
                <a href="{{ route('account.orders') }}" class="account-card__action">View all</a>
            @endif
        </div>

        @include('partials.account.order-list', [
            'orders' => $orders,
            'emptyMessage' => 'You have not placed any orders yet.',
        ])
    </div>

    <div class="account-card">
        <h2 class="account-card__title">Quick links</h2>
        <ul class="account-links">
            <li>
                <a href="{{ route('shop') }}">
                    <i class="bi bi-bag" aria-hidden="true"></i>
                    <span>
                        <strong>Continue shopping</strong>
                        <small>Browse dried fruits, pickles &amp; gift boxes</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('account.wishlist') }}">
                    <i class="bi bi-heart" aria-hidden="true"></i>
                    <span>
                        <strong>View wishlist</strong>
                        <small>{{ ($wishlistCount ?? 0) > 0 ? $wishlistCount . ' saved item' . (($wishlistCount ?? 0) === 1 ? '' : 's') : 'Save products you love' }}</small>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('account.profile') }}">
                    <i class="bi bi-person-gear" aria-hidden="true"></i>
                    <span>
                        <strong>Profile &amp; security</strong>
                        <small>Update your name or password</small>
                    </span>
                </a>
            </li>
        </ul>
    </div>
@endsection
