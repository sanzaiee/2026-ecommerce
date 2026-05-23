@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', 'Wishlist — Mandira Foods')

@push('styles')
    <link href="{{ asset('css/account.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page store-wishlist-page">
        @include('partials.store.content-breadcrumb', ['title' => 'Wishlist'])

        <section>
            <div class="container">
                <div class="account-header">
                    <h1 class="account-header__title">Your wishlist</h1>
                    <p class="account-header__lead">
                        @auth
                            Sign in with a customer account to sync your wishlist across devices.
                        @else
                            Items are saved on this device. Sign in to keep your wishlist in your account.
                        @endauth
                    </p>
                </div>

                <div class="account-card account-wishlist-page @if (($wishlist['count'] ?? 0) === 0) is-empty @endif" data-wishlist-page>
                    <div class="account-wishlist-toolbar">
                        <p class="account-wishlist-count" id="wishlistPageCount">
                            {{ ($wishlist['count'] ?? 0) === 1 ? '1 item saved' : ($wishlist['count'] ?? 0) . ' items saved' }}
                        </p>
                        <button type="button" class="account-btn account-btn--primary account-btn--inline" id="wishlistPageMoveAll"
                            @if (($wishlist['count'] ?? 0) === 0) disabled @endif>
                            Move all to cart
                        </button>
                    </div>

                    @include('partials.account.wishlist-items', ['items' => $wishlist['items'] ?? []])

                    @guest
                        <p class="text-center mt-4 mb-0">
                            <a href="{{ route('login') }}">Sign in</a> or
                            <a href="{{ route('register') }}">create an account</a> to save your wishlist permanently.
                        </p>
                    @endguest
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('wishlistPageMoveAll')?.addEventListener('click', () => {
            document.getElementById('wishlistMoveAllBtn')?.click();
        });
    </script>
@endpush
