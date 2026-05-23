@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Orders</h1>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="search">Search</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Order #, name, email, phone">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="status">Order status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All</option>
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($filters->status === $status)>{{ ucfirst($status->value) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="delivery_status">Delivery</label>
            <select name="delivery_status" id="delivery_status" class="form-select">
                <option value="">All</option>
                @foreach (\App\Enums\DeliveryStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($filters->deliveryStatus === $status)>{{ ucfirst($status->value) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="payment_status">Payment</label>
            <select name="payment_status" id="payment_status" class="form-select">
                <option value="">All</option>
                @foreach (\App\Enums\PaymentStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($filters->paymentStatus === $status)>{{ ucfirst($status->value) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Placed</th>
                <th>Total</th>
                <th>Order</th>
                <th>Delivery</th>
                <th>Payment</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a>
                    </td>
                    <td>
                        <div>{{ $order->customer_name }}</div>
                        <div class="small text-muted">{{ $order->customer_email }}</div>
                    </td>
                    <td>{{ $order->placed_at?->format('M j, Y g:i A') }}</td>
                    <td>Rs. {{ number_format((float) $order->total, 0) }}</td>
                    <td>@include('partials.admin.order-status-badge', ['status' => $order->status])</td>
                    <td>@include('partials.admin.delivery-status-badge', ['status' => $order->delivery_status])</td>
                    <td>@include('partials.admin.payment-status-badge', ['status' => $order->payment_status])</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-sm btn-outline-dark" target="_blank">Invoice</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-muted">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
