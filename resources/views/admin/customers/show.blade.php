@extends('layouts.admin')

@section('title', $customer->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.customers.index') }}" class="text-muted small text-decoration-none">&larr; All customers</a>
        <h1 class="h3 mb-0 mt-1">{{ $customer->name }}</h1>
        <p class="text-muted mb-0">Member since {{ $customer->created_at->format('M j, Y g:i A') }}</p>
    </div>
    <div class="d-flex gap-2">
        @if ($customer->is_banned)
            <form method="POST" action="{{ route('admin.customers.unban', $customer) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-unlock me-1"></i>Unban Customer
                </button>
            </form>
        @else
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#banModal">
                <i class="bi bi-ban me-1"></i>Ban Customer
            </button>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <h2 class="h5 mb-3">Customer Information</h2>
            <dl class="row mb-0">
                <dt class="col-5">Name</dt>
                <dd class="col-7">{{ $customer->name }}</dd>
                <dt class="col-5">Email</dt>
                <dd class="col-7">{{ $customer->email }}</dd>
                @if ($customer->phone)
                    <dt class="col-5">Phone</dt>
                    <dd class="col-7">{{ $customer->phone }}</dd>
                @endif
                @if ($customer->shipping_address_line1)
                    <dt class="col-5">Address</dt>
                    <dd class="col-7">
                        {{ $customer->shipping_address_line1 }}
                        @if ($customer->shipping_address_line2) <br>{{ $customer->shipping_address_line2 }} @endif
                        <br>
                        @if ($customer->shipping_city) {{ $customer->shipping_city }} @endif
                        @if ($customer->shipping_district) {{ $customer->shipping_district }} @endif
                        @if ($customer->shipping_postal_code) {{ $customer->shipping_postal_code }} @endif
                    </dd>
                @endif
            </dl>
        </div>

        <div class="admin-card mb-4">
            <h2 class="h5 mb-3">Account Status</h2>
            @if ($customer->is_banned)
                <div class="alert alert-danger mb-3">
                    <strong><i class="bi bi-ban me-1"></i>Banned</strong>
                    @if ($customer->banned_at)
                        <p class="mb-0 small">Since {{ $customer->banned_at->format('M j, Y g:i A') }}</p>
                    @endif
                    @if ($customer->ban_reason)
                        <p class="mb-0 small mt-2"><strong>Reason:</strong> {{ $customer->ban_reason }}</p>
                    @endif
                </div>
            @else
                <div class="alert alert-success mb-0">
                    <strong><i class="bi bi-check-circle me-1"></i>Active</strong>
                    <p class="mb-0 small">Account is in good standing</p>
                </div>
            @endif
        </div>

        <div class="admin-card">
            <h2 class="h5 mb-3">Statistics</h2>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <div class="stat-box">
                        <i class="bi bi-bag-check text-primary"></i>
                        <div class="stat-value">{{ $customer->orders_count }}</div>
                        <div class="stat-label">Orders</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <i class="bi bi-star text-warning"></i>
                        <div class="stat-value">{{ $customer->reviews_count }}</div>
                        <div class="stat-label">Reviews</div>
                    </div>
                </div>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <div class="stat-box">
                        <i class="bi bi-currency-rupee text-success"></i>
                        <div class="stat-value small">Rs. {{ number_format((float) ($customer->total_spent ?? 0), 0) }}</div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box">
                        <i class="bi bi-star-fill text-info"></i>
                        <div class="stat-value">{{ number_format($customer->avg_rating ?? 0, 1) }}</div>
                        <div class="stat-label">Avg Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Orders ({{ $customer->orders_count }})</h2>
                @if ($customer->orders_count > 3)
                    <small class="text-muted">Showing 3 most recent</small>
                @endif
            </div>
            @if ($customer->orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->placed_at?->format('M j, Y') }}</td>
                                    <td>Rs. {{ number_format((float) $order->total, 0) }}</td>
                                    <td>@include('partials.admin.order-status-badge', ['status' => $order->status])</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No orders yet.</p>
            @endif
        </div>

        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Reviews ({{ $customer->reviews_count }})</h2>
            </div>
            @if ($customer->reviews->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Comment</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->reviews as $review)
                                <tr>
                                    <td>{{ $review->product?->title }}</td>
                                    <td>
                                        <span class="badge text-bg-warning">
                                            <i class="bi bi-star-fill me-1"></i>{{ $review->rating }}/5
                                        </span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($review->comment, 80) }}</td>
                                    <td>
                                        <span class="badge {{ $review->status === \App\Enums\ReviewStatus::Approved ? 'text-bg-success' : 'text-bg-warning' }}">
                                            {{ ucfirst($review->status->value) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No reviews yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- Ban Modal -->
<div class="modal fade" id="banModal" tabindex="-1" aria-labelledby="banModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.customers.ban', $customer) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="banModalLabel">Ban Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <strong>Warning:</strong> Banning this customer will prevent them from logging in and placing new orders.
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reason">Reason for banning <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Enter the reason for banning this customer..."></textarea>
                        <div class="form-text">This reason will be visible to other admins and for audit purposes.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-ban me-1"></i>Confirm Ban
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .stat-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stat-box i {
            font-size: 24px;
            margin-bottom: 8px;
            display: block;
        }
        .stat-value {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .stat-value.small {
            font-size: 14px;
        }
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin: 0;
        }
    </style>
@endpush
@endsection