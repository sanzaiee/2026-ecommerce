@extends('layouts.account')

@section('account-content')
    <div class="account-header">
        <h1 class="account-header__title">Profile &amp; security</h1>
        <p class="account-header__lead">Update your personal details and password.</p>
    </div>

    <div class="account-card mb-4">
        <h2 class="account-card__title">Personal details</h2>
        <form method="POST" action="{{ route('account.profile.update') }}" class="account-form" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Full name</label>
                <input type="text" id="name" name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled
                    aria-describedby="email-help">
                <div id="email-help" class="form-text">Contact support if you need to change your email address.</div>
            </div>

            <button type="submit" class="account-btn account-btn--primary account-btn--inline">Save changes</button>
        </form>
    </div>

    <div class="account-card">
        <h2 class="account-card__title">Change password</h2>
        <form method="POST" action="{{ route('account.password.update') }}" class="account-form" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="current_password" class="form-label">Current password</label>
                <input type="password" id="current_password" name="current_password"
                    class="form-control @error('current_password') is-invalid @enderror" required
                    autocomplete="current-password">
                @error('current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New password</label>
                <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" required
                    autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm new password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    required autocomplete="new-password">
            </div>

            <button type="submit" class="account-btn account-btn--primary account-btn--inline">Update password</button>
        </form>
    </div>
@endsection
