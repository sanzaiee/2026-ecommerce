@extends('layouts.store', ['cartTotal' => $cartTotal])

@section('title', 'Order confirmed — Our site')

@push('styles')
    <link href="{{ asset('css/checkout.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content-page checkout-page">
        @include('partials.store.content-breadcrumb', ['title' => 'Order confirmed'])

        <section class="checkout-section checkout-section--result">
            <div class="container">
                <div class="checkout-result">
                    <div class="checkout-result__icon checkout-result__icon--success" aria-hidden="true">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                    <h1 class="checkout-result__title">Thank you for your order</h1>

                    @if (session('status'))
                        <p class="checkout-result__lead">{{ session('status') }}</p>
                    @else
                        <p class="checkout-result__lead">We have received your order and will prepare it for delivery.</p>
                    @endif

                    <div class="checkout-result__card">
                        <dl class="checkout-result__meta">
                            <div>
                                <dt>Order number</dt>
                                <dd>{{ $order->order_number }}</dd>
                            </div>
                            <div>
                                <dt>Total</dt>
                                <dd>Rs. {{ number_format((float) $order->total, 0) }}</dd>
                            </div>
                            <div>
                                <dt>Payment</dt>
                                <dd>
                                    @if ($order->payment_method->value === 'cod')
                                        Cash on delivery
                                    @else
                                        {{ ucfirst($order->payment_gateway ?? 'Online') }}
                                        @if ($order->isPaid())
                                            — paid
                                        @endif
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt>Deliver to</dt>
                                <dd>{{ $order->formattedShippingAddress() }}</dd>
                            </div>
                        </dl>

                        <ul class="checkout-result__items">
                            @foreach ($order->items as $item)
                                <li>
                                    <span>{{ $item->product_title }} &times; {{ $item->quantity }}</span>
                                    <span>Rs. {{ number_format((float) $item->line_total, 0) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="checkout-result__actions">
                        <a href="{{ route('shop') }}" class="checkout-submit">Continue shopping</a>
                        @auth
                            @if (auth()->user()->isCustomer())
                                <a href="{{ route('account.orders') }}" class="checkout-back">View order history</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
