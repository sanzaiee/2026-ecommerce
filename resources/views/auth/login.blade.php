@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', 'Sign In — Mandira Foods')

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page">
        @include('partials.store.content-breadcrumb', ['title' => 'Sign In'])

        <section class="auth-page">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-9">
                        <div class="auth-card">
                            <div class="auth-card__grid">
                                <div class="auth-card__form">
                                    <div class="auth-heading">
                                        <h1>Welcome back</h1>
                                        <p>Sign in to track orders, save favourites, and checkout faster.</p>
                                    </div>

                                    @if (session('status'))
                                        <div class="alert alert-success auth-alert" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form class="auth-form" method="POST" action="{{ route('login') }}" novalidate>
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email address</label>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required autofocus autocomplete="email"
                                                placeholder="you@example.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" required
                                                autocomplete="current-password" placeholder="Enter your password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="auth-form__row">
                                            <label class="auth-form__remember">
                                                <input type="checkbox" name="remember" value="1"
                                                    {{ old('remember') ? 'checked' : '' }}>
                                                Remember me
                                            </label>
                                        </div>

                                        <button type="submit" class="auth-btn">Sign in</button>
                                    </form>

                                    <p class="auth-switch">
                                        New to Mandira Foods?
                                        <a href="{{ route('register') }}">Create an account</a>
                                    </p>
                                </div>

                                <div class="auth-card__aside">
                                    <div>
                                        <h2>Premium natural foods from Nepal</h2>
                                        <p>Your account gives you quick access to orders, addresses, and your wishlist.</p>
                                        <ul>
                                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Track deliveries in real time</li>
                                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Save products to your wishlist</li>
                                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Faster checkout on repeat orders</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
