@extends('layouts.store', ['cartTotal' => $cartTotal ?? 'Rs. 0'])

@section('title', ($pageTitle ?? 'My Account') . ' — Our site')

@push('styles')
    <link href="{{ asset('css/account.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page">
        @include('partials.account.breadcrumb', [
            'section' => $breadcrumbSection ?? ($pageTitle !== 'Dashboard' ? $pageTitle : null),
        ])

        <section class="account-page">
            <div class="container">
                @if (session('status'))
                    <div class="alert alert-success auth-alert mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="row g-4 account-shell">
                    <div class="col-12 d-lg-none">
                        @include('partials.account.mobile-nav')
                    </div>

                    <div class="col-lg-3 d-none d-lg-block">
                        @include('partials.account.sidebar')
                    </div>

                    <div class="col-lg-9 account-main">
                        @yield('account-content')
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
