@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', 'Payment failed — Our site')

@push('styles')
    <link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page checkout-page">
        @include('partials.store.content-breadcrumb', ['title' => 'Payment failed'])

        <section class="checkout-section checkout-section--result">
            <div class="container">
                <div class="checkout-result">
                    <div class="checkout-result__icon checkout-result__icon--failure" aria-hidden="true">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>

                    <h1 class="checkout-result__title">Payment not completed</h1>

                    <p class="checkout-result__lead">
                        {{ session('status', 'Your payment was not completed. No charge was made for order ' . $order->order_number . '.') }}
                    </p>

                    <div class="checkout-result__actions">
                        <a href="{{ route('store.checkout.payment', $order->order_number) }}" class="checkout-submit">Try
                            payment again</a>
                        <a href="{{ route('shop') }}" class="checkout-back">Back to shop</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
