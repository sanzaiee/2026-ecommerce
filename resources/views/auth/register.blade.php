@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', 'Create Account — Our site')

@push('styles')
    <link href="{{ asset('css/content-pages.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/auth.js') }}" defer></script>
@endpush

@section('content')
    <div class="content-page">
        @include('partials.store.content-breadcrumb', ['title' => 'Register'])

        <section class="auth-page">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-10 col-xl-9">
                        <div class="auth-card">
                            <div class="auth-card__grid">
                                <div class="auth-card__form">
                                    <div class="auth-heading">
                                        <h1>Create your account</h1>
                                        <p>Join Our site for a smoother shopping experience.</p>
                                    </div>

                                    <form class="auth-form" method="POST" action="{{ route('register') }}" novalidate>
                                        @csrf

                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full name</label>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" required autofocus autocomplete="name"
                                                placeholder="Your full name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email address</label>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required autocomplete="email"
                                                placeholder="you@example.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <x-auth.password-input id="password" name="password" label="Password"
                                            placeholder="At least 8 characters" autocomplete="new-password" />

                                        <x-auth.password-input id="password_confirmation" name="password_confirmation"
                                            label="Confirm password" placeholder="Re-enter your password"
                                            autocomplete="new-password" :required="true" />

                                        <label class="auth-form__terms">
                                            <input type="checkbox" name="terms" value="1"
                                                {{ old('terms') ? 'checked' : '' }} required>
                                            <span>
                                                I agree to the
                                                <a href="{{ route('terms') }}" target="_blank" rel="noopener">Terms &amp;
                                                    Conditions</a>
                                                and
                                                <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Privacy
                                                    Policy</a>.
                                            </span>
                                        </label>
                                        @error('terms')
                                            <div class="text-danger small mb-3">{{ $message }}</div>
                                        @enderror

                                        <button type="submit" class="auth-btn">Create account</button>
                                    </form>

                                    <p class="auth-switch">
                                        Already have an account?
                                        <a href="{{ route('login') }}">Sign in</a>
                                    </p>
                                </div>

                                <div class="auth-card__aside">
                                    <div>
                                        <h2>Why register?</h2>
                                        <p>Members enjoy exclusive perks on dried fruits, pickles, and gift boxes.</p>
                                        <ul>
                                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Order history
                                                &amp; invoices</li>
                                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Saved shipping
                                                addresses</li>
                                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Early access to
                                                seasonal offers</li>
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
