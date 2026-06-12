@extends('layouts.account')

@section('account-content')
    <div class="account-header account-header--with-action">
        <div>
            <a href="{{ route('account.orders') }}" class="account-back">&larr; All orders</a>
            <h1 class="account-header__title">{{ $order->order_number }}</h1>
            <p class="account-header__lead">
                Placed {{ $order->placed_at->format('M j, Y \a\t g:i A') }}
            </p>
        </div>
        <div class="account-order-header-actions">
            @if ($order->hasCustomerInvoice())
                <a href="{{ route('account.orders.invoice', $order->order_number) }}"
                    class="account-btn account-btn--outline account-btn--sm" target="_blank" rel="noopener">
                    View invoice
                </a>
            @endif
            <div class="account-order-badges">
                <span class="account-order-badge">{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</span>
                <span class="account-order-badge account-order-badge--muted">
                    {{ ucfirst(str_replace('_', ' ', $order->delivery_status->value)) }}
                </span>
            </div>
        </div>
    </div>

    <div class="account-card mb-4">
        <h2 class="account-card__title">Items</h2>
        <ul class="account-order-items">
            @foreach ($order->items as $item)
                <li class="account-order-items__row">
                    <div>
                        <strong>{{ $item->product_title }}</strong>
                        <span>Qty {{ $item->quantity }}</span>
                    </div>
                    <span>Rs. {{ number_format((float) $item->line_total, 0) }}</span>
                </li>
            @endforeach
        </ul>
        <dl class="account-order-totals">
            <div>
                <dt>Subtotal</dt>
                <dd>Rs. {{ number_format((float) $order->subtotal, 0) }}</dd>
            </div>
            <div>
                <dt>Shipping</dt>
                <dd>Rs. {{ number_format((float) $order->shipping_amount, 0) }}</dd>
            </div>
            <div class="account-order-totals__total">
                <dt>Total</dt>
                <dd>Rs. {{ number_format((float) $order->total, 0) }}</dd>
            </div>
        </dl>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="account-card h-100">
                <h2 class="account-card__title">Delivery</h2>
                <dl class="account-details">
                    <div>
                        <dt>Name</dt>
                        <dd>{{ $order->customer_name }}</dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $order->customer_phone }}</dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd>{{ $order->customer_email }}</dd>
                    </div>
                    <div>
                        <dt>Address</dt>
                        <dd>{{ $order->formattedShippingAddress() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="col-md-6">
            <div class="account-card h-100">
                <h2 class="account-card__title">Payment</h2>
                <dl class="account-details">
                    <div>
                        <dt>Method</dt>
                        <dd>
                            @if ($order->payment_method->value === 'cod')
                                Cash on delivery
                            @else
                                Online ({{ ucfirst($order->payment_gateway ?? 'gateway') }})
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd>{{ ucfirst(str_replace('_', ' ', $order->payment_status->value)) }}</dd>
                    </div>
                    @if ($order->notes)
                        <div>
                            <dt>Notes</dt>
                            <dd>{{ $order->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection
