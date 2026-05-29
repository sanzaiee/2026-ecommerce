@extends('layouts.admin-guest')

@section('title', 'Sign in')

@push('scripts')
    <script src="{{ asset('js/auth.js') }}" defer></script>
@endpush

@section('content')
    <div class="admin-auth">
        <div class="admin-auth__shell">
            <header class="admin-auth__brand">
                <div class="admin-auth__mark" aria-hidden="true">
                    <i class="bi bi-grid-1x2-fill"></i>
                </div>
                <div>
                    <p class="admin-auth__site">{{ $site['siteName'] ?? config('app.name') }}</p>
                    <p class="admin-auth__tag">Content management</p>
                </div>
            </header>

            <div class="admin-auth__card">
                <div class="admin-auth__intro">
                    <h1>Sign in</h1>
                    <p>Use your admin credentials to access the dashboard.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger admin-auth__alert" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form class="admin-auth__form" method="POST" action="{{ route('admin.login') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required autofocus autocomplete="email"
                            placeholder="admin@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-auth.password-input id="password" name="password" label="Password"
                        placeholder="Enter your password" />

                    <div class="admin-auth__remember">
                        <input type="checkbox" name="remember" id="remember" value="1"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="admin-auth__submit">Sign in</button>
                </form>
            </div>

            <footer class="admin-auth__footer">
                <a href="{{ route('home') }}" class="admin-auth__back">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    Back to store
                </a>
            </footer>
        </div>
    </div>
@endsection
