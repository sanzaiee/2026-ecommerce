@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', 'Checkout — '.config('app.name'))

@push('styles')
    <link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page checkout-page">
        @include('partials.store.content-breadcrumb', ['title' => 'Checkout'])

        <section class="checkout-section">
            <div class="container">
                @if (session('status'))
                    <div class="alert alert-success checkout-alert mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form class="checkout-form" method="POST" action="{{ route('store.checkout.store') }}" novalidate>
                    @csrf

                    <div class="row g-4 g-xl-5">
                        <div class="col-lg-7">
                            <div class="checkout-card">
                                <h2 class="checkout-card__title">Contact &amp; delivery</h2>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="customer_name" class="form-label">Full name</label>
                                        <input type="text" id="customer_name" name="customer_name"
                                            class="form-control @error('customer_name') is-invalid @enderror"
                                            value="{{ $customer['name'] }}" required>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_phone" class="form-label">Phone</label>
                                        <input type="tel" id="customer_phone" name="customer_phone"
                                            class="form-control @error('customer_phone') is-invalid @enderror"
                                            value="{{ $customer['phone'] }}" required placeholder="98XXXXXXXX">
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="customer_email" class="form-label">Email</label>
                                        <input type="email" id="customer_email" name="customer_email"
                                            class="form-control @error('customer_email') is-invalid @enderror"
                                            value="{{ $customer['email'] }}" required>
                                        @error('customer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="shipping_address_line1" class="form-label">Street address</label>
                                        <input type="text" id="shipping_address_line1" name="shipping_address_line1"
                                            class="form-control @error('shipping_address_line1') is-invalid @enderror"
                                            value="{{ $shipping['address_line1'] }}" required
                                            placeholder="House no., street, landmark">
                                        @error('shipping_address_line1')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="shipping_address_line2" class="form-label">Apartment, suite
                                            <span class="text-muted">(optional)</span></label>
                                        <input type="text" id="shipping_address_line2" name="shipping_address_line2"
                                            class="form-control @error('shipping_address_line2') is-invalid @enderror"
                                            value="{{ $shipping['address_line2'] }}">
                                        @error('shipping_address_line2')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="shipping_city" class="form-label">City / municipality</label>
                                        <input type="text" id="shipping_city" name="shipping_city"
                                            class="form-control @error('shipping_city') is-invalid @enderror"
                                            value="{{ $shipping['city'] }}" required>
                                        @error('shipping_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="shipping_district" class="form-label">District</label>
                                        <input type="text" id="shipping_district" name="shipping_district"
                                            class="form-control @error('shipping_district') is-invalid @enderror"
                                            value="{{ $shipping['district'] }}" required>
                                        @error('shipping_district')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="shipping_postal_code" class="form-label">Postal code
                                            <span class="text-muted">(optional)</span></label>
                                        <input type="text" id="shipping_postal_code" name="shipping_postal_code"
                                            class="form-control @error('shipping_postal_code') is-invalid @enderror"
                                            value="{{ $shipping['postal_code'] }}">
                                        @error('shipping_postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="notes" class="form-label">Delivery notes
                                            <span class="text-muted">(optional)</span></label>
                                        <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                            placeholder="Gate code, preferred time, etc.">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="checkout-card checkout-card--payment">
                                <h2 class="checkout-card__title">Payment method</h2>

                                @error('payment_method')
                                    <div class="alert alert-danger py-2 mb-3" role="alert">{{ $message }}</div>
                                @enderror

                                <div class="checkout-payment-options">
                                    @foreach ($paymentOptions as $index => $option)
                                        <label class="checkout-payment-option">
                                            <input type="radio" name="payment_method" value="{{ $option['id'] }}"
                                                @checked(old('payment_method', $defaultPaymentMethod) === $option['id']) required>
                                            <span class="checkout-payment-option__box">
                                                <span class="checkout-payment-option__label">{{ $option['label'] }}</span>
                                                <span
                                                    class="checkout-payment-option__desc">{{ $option['description'] }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <aside class="checkout-summary" aria-label="Order summary">
                                <h2 class="checkout-summary__title">Order summary</h2>

                                <ul class="checkout-summary__items">
                                    @foreach ($items as $item)
                                        <li class="checkout-summary__item">
                                            <div class="checkout-summary__item-main">
                                                <span class="checkout-summary__name">{{ $item['name'] }}</span>
                                                <span class="checkout-summary__meta">Qty {{ $item['qty'] }}</span>
                                            </div>
                                            <span class="checkout-summary__line-total">
                                                Rs. {{ number_format($item['price'] * $item['qty'], 0) }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="checkout-summary__rows">
                                    <div class="checkout-summary__row">
                                        <span>Subtotal</span>
                                        <span>Rs. {{ number_format($totals->subtotal, 0) }}</span>
                                    </div>
                                    <div class="checkout-summary__row">
                                        <span>Shipping</span>
                                        <span>
                                            @if ($totals->shippingAmount > 0)
                                                Rs. {{ number_format($totals->shippingAmount, 0) }}
                                            @else
                                                Free
                                            @endif
                                        </span>
                                    </div>
                                    @if ($totals->shippingAmount > 0)
                                        <p class="checkout-summary__hint">
                                            Free shipping on orders over Rs.
                                            {{ number_format($freeShippingThreshold, 0) }}.
                                        </p>
                                    @endif
                                    <div class="checkout-summary__row checkout-summary__row--total">
                                        <span>Total</span>
                                        <span>Rs. {{ number_format($totals->total, 0) }}</span>
                                    </div>
                                </div>

                                @error('cart')
                                    <div class="alert alert-danger py-2 mt-3" role="alert">{{ $message }}</div>
                                @enderror

                                <button type="submit" class="checkout-submit">Place order</button>
                                <a href="{{ route('shop') }}" class="checkout-back">Continue shopping</a>
                            </aside>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
