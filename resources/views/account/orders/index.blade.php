@extends('layouts.account')

@section('account-content')
    <div class="account-header">
        <h1 class="account-header__title">Order history</h1>
        <p class="account-header__lead">Track and review your past purchases.</p>
    </div>

    <div class="account-card">
        @include('partials.account.order-list', ['orders' => $orders])

        @if ($orders->hasPages())
            <div class="account-pagination">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
