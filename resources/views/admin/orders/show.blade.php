@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="text-muted small text-decoration-none">&larr; All orders</a>
        <h1 class="h3 mb-0 mt-1">{{ $order->order_number }}</h1>
        <p class="text-muted mb-0">Placed {{ $order->placed_at?->format('M j, Y g:i A') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-dark" target="_blank">View invoice</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <h2 class="h5 mb-3">Line items</h2>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Unit</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_title }}</td>
                            <td class="text-end">Rs. {{ number_format((float) $item->unit_price, 0) }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">Rs. {{ number_format((float) $item->line_total, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end">Subtotal</td>
                        <td class="text-end">Rs. {{ number_format((float) $order->subtotal, 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">Shipping</td>
                        <td class="text-end">Rs. {{ number_format((float) $order->shipping_amount, 0) }}</td>
                    </tr>
                    <tr class="fw-semibold">
                        <td colspan="3" class="text-end">Total</td>
                        <td class="text-end">Rs. {{ number_format((float) $order->total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="admin-card">
            <h2 class="h5 mb-3">Customer &amp; shipping</h2>
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $order->customer_name }}</dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $order->customer_email }}</dd>
                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $order->customer_phone }}</dd>
                <dt class="col-sm-3">Address</dt>
                <dd class="col-sm-9">{{ $order->formattedShippingAddress() }}</dd>
                @if ($order->notes)
                    <dt class="col-sm-3">Notes</dt>
                    <dd class="col-sm-9">{{ $order->notes }}</dd>
                @endif
                @if ($order->user)
                    <dt class="col-sm-3">Account</dt>
                    <dd class="col-sm-9">{{ $order->user->email }}</dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <h2 class="h5 mb-3">Status</h2>
            <ul class="list-unstyled mb-4">
                <li class="d-flex justify-content-between align-items-center mb-2">
                    <span>Order</span>
                    @include('partials.admin.order-status-badge', ['status' => $order->status])
                </li>
                <li class="d-flex justify-content-between align-items-center mb-2">
                    <span>Delivery</span>
                    @include('partials.admin.delivery-status-badge', ['status' => $order->delivery_status])
                </li>
                <li class="d-flex justify-content-between align-items-center">
                    <span>Payment</span>
                    @include('partials.admin.payment-status-badge', ['status' => $order->payment_status])
                </li>
            </ul>

            <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mb-3">
                @csrf
                @method('PATCH')
                <label class="form-label" for="order-status">Order status</label>
                <div class="input-group">
                    <select name="status" id="order-status" class="form-select">
                        @foreach (\App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($order->status === $status)>{{ ucfirst($status->value) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-dark">Update</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.orders.delivery-status', $order) }}" class="mb-3">
                @csrf
                @method('PATCH')
                <label class="form-label" for="delivery-status">Delivery status</label>
                <div class="input-group">
                    <select name="delivery_status" id="delivery-status" class="form-select">
                        @foreach (\App\Enums\DeliveryStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected($order->delivery_status === $status)>{{ ucfirst($status->value) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-dark">Update</button>
                </div>
            </form>

            @if ($order->shipped_at)
                <p class="small text-muted mb-1">Shipped {{ $order->shipped_at->format('M j, Y g:i A') }}</p>
            @endif
            @if ($order->delivered_at)
                <p class="small text-muted mb-0">Delivered {{ $order->delivered_at->format('M j, Y g:i A') }}</p>
            @endif
        </div>

        <div class="admin-card">
            <h2 class="h5 mb-3">Payment</h2>
            <dl class="row small mb-3">
                <dt class="col-5">Method</dt>
                <dd class="col-7">{{ $order->payment_method->value === 'cod' ? 'Cash on delivery' : 'Online' }}</dd>
                @if ($order->payment_gateway)
                    <dt class="col-5">Gateway</dt>
                    <dd class="col-7">{{ ucfirst($order->payment_gateway) }}</dd>
                @endif
                @if ($order->payment_reference)
                    <dt class="col-5">Reference</dt>
                    <dd class="col-7">{{ $order->payment_reference }}</dd>
                @endif
                @if ($order->paid_at)
                    <dt class="col-5">Paid at</dt>
                    <dd class="col-7">{{ $order->paid_at->format('M j, Y g:i A') }}</dd>
                @endif
            </dl>

            @if (! $order->isPaid())
                <form method="POST" action="{{ route('admin.orders.mark-paid', $order) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success w-100">Mark as paid</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
