@extends('layouts.account')

@section('account-content')
    <div class="account-header">
        <h1 class="account-header__title">Saved addresses</h1>
        <p class="account-header__lead">Save delivery addresses for faster checkout.</p>
    </div>

    <div class="account-card">
        <h2 class="account-card__title">Delivery address</h2>
        <p class="account-card__lead">These details are pre-filled at checkout. Name and email come from your profile.</p>

        <form method="POST" action="{{ route('account.addresses.update') }}" class="account-form" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="tel" id="phone" name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $user->phone) }}" required placeholder="98XXXXXXXX">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="shipping_address_line1" class="form-label">Street address</label>
                <input type="text" id="shipping_address_line1" name="shipping_address_line1"
                    class="form-control @error('shipping_address_line1') is-invalid @enderror"
                    value="{{ old('shipping_address_line1', $user->shipping_address_line1) }}" required
                    placeholder="House no., street, landmark">
                @error('shipping_address_line1')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="shipping_address_line2" class="form-label">Apartment, suite
                    <span class="text-muted">(optional)</span></label>
                <input type="text" id="shipping_address_line2" name="shipping_address_line2"
                    class="form-control @error('shipping_address_line2') is-invalid @enderror"
                    value="{{ old('shipping_address_line2', $user->shipping_address_line2) }}">
                @error('shipping_address_line2')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="shipping_city" class="form-label">City / municipality</label>
                    <input type="text" id="shipping_city" name="shipping_city"
                        class="form-control @error('shipping_city') is-invalid @enderror"
                        value="{{ old('shipping_city', $user->shipping_city) }}" required>
                    @error('shipping_city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="shipping_district" class="form-label">District</label>
                    <input type="text" id="shipping_district" name="shipping_district"
                        class="form-control @error('shipping_district') is-invalid @enderror"
                        value="{{ old('shipping_district', $user->shipping_district) }}" required>
                    @error('shipping_district')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="shipping_postal_code" class="form-label">Postal code
                    <span class="text-muted">(optional)</span></label>
                <input type="text" id="shipping_postal_code" name="shipping_postal_code"
                    class="form-control @error('shipping_postal_code') is-invalid @enderror"
                    value="{{ old('shipping_postal_code', $user->shipping_postal_code) }}">
                @error('shipping_postal_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="account-btn account-btn--primary account-btn--inline">Save address</button>
        </form>
    </div>
@endsection
