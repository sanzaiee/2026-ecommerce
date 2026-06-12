@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Customers</h1>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="search">Search</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Name, email, or phone">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="is_banned">Status</label>
            <select name="is_banned" id="is_banned" class="form-select">
                <option value="">All</option>
                <option value="1" @selected($filters->isBanned === true)>Banned</option>
                <option value="0" @selected($filters->isBanned === false)>Active</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="has_orders">Orders</label>
            <select name="has_orders" id="has_orders" class="form-select">
                <option value="">All</option>
                <option value="1" @selected($filters->hasOrders === true)">Has orders</option>
                <option value="0" @selected($filters->hasOrders === false)">No orders</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="sorted_by">Sort by</label>
            <select name="sorted_by" id="sorted_by" class="form-select">
                <option value="">Created at</option>
                <option value="orders_count" @selected($filters->orderBy === 'orders_count')">Order count</option>
                <option value="total_spent" @selected($filters->orderBy === 'total_spent')">Total spent</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark">Filter</button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Orders</th>
                <th>Total Spent</th>
                <th>Reviews</th>
                <th>Avg Rating</th>
                <th>Joined</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $customer->name }}</div>
                        <div class="small text-muted">{{ $customer->email }}</div>
                        @if ($customer->phone)
                            <div class="small text-muted">{{ $customer->phone }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $customer->orders_count }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">Rs. {{ number_format((float) ($customer->total_spent ?? 0), 0) }}</div>
                    </td>
                    <td>{{ $customer->reviews_count }}</td>
                    <td>
                        @if ($customer->reviews_count > 0)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <span>{{ number_format($customer->avg_rating ?? 0, 1) }}</span>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $customer->created_at->format('M j, Y') }}</td>
                    <td>
                        @if ($customer->is_banned)
                            <span class="badge text-bg-danger">
                                <i class="bi bi-ban me-1"></i>Banned
                            </span>
                        @else
                            <span class="badge text-bg-success">Active</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-muted">No customers found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $customers->withQueryString()->links() }}</div>
</div>
@endsection