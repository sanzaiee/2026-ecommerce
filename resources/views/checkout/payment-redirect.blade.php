@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', 'Redirecting to payment — Mandira Foods')

@push('styles')
    <link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page checkout-page">
        <section class="checkout-section checkout-section--result">
            <div class="container">
                <div class="checkout-result">
                    <div class="checkout-result__icon" aria-hidden="true">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading</span>
                        </div>
                    </div>
                    <h1 class="checkout-result__title">Redirecting to payment</h1>
                    <p class="checkout-result__lead">
                        Please wait while we connect you to our secure payment partner for order
                        <strong>{{ $order->order_number }}</strong>.
                    </p>

                    <form id="paymentRedirectForm" method="POST" action="{{ $formAction }}">
                        @foreach ($fields as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                    </form>

                    <p class="checkout-result__hint">
                        If you are not redirected automatically,
                        <button type="submit" form="paymentRedirectForm" class="checkout-result__link-btn">click here</button>.
                    </p>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('paymentRedirectForm')?.submit();
    </script>
@endpush
