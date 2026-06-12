@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Store overview for ' . now()->format('l, F j, Y'))

@section('content')
    @php
        $orderStats = $analytics['orders'] ?? [];
        $revenue = $analytics['revenue'] ?? [];
        $attention = $analytics['attention'] ?? [];
        $chartLabels = collect($revenue['daily'] ?? [])
            ->map(fn($row) => \Illuminate\Support\Carbon::parse($row['date'])->format('M j'))
            ->values();
        $chartRevenue = collect($revenue['daily'] ?? [])
            ->pluck('revenue')
            ->values();
        $chartOrders = collect($revenue['daily'] ?? [])
            ->pluck('orders')
            ->values();
        $statusLabels = collect($orderStats['by_status'] ?? [])
            ->keys()
            ->map(fn($s) => ucfirst($s))
            ->values();
        $statusCounts = collect($orderStats['by_status'] ?? [])->values();
        $paymentLabels = collect($orderStats['by_payment'] ?? [])
            ->keys()
            ->map(fn($s) => ucfirst($s))
            ->values();
        $paymentCounts = collect($orderStats['by_payment'] ?? [])->values();
    @endphp

    <div class="row g-3 mb-4 align-items-stretch">
        @if (($attention['total'] ?? 0) > 0)
            <div class="col-lg-8">
                <div
                    class="admin-card admin-card--compact d-flex flex-wrap align-items-center justify-content-between gap-2 h-100">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="dashboard-attention-badge">
                            <i class="bi bi-bell-fill me-1"></i>{{ $attention['total'] }} need attention
                        </span>
                        <span class="small admin-text-muted">Review pending orders and messages</span>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm admin-btn-outline">View orders</a>
                </div>
            </div>
        @endif
        <div class="col-lg-4 @if (($attention['total'] ?? 0) === 0) ms-lg-auto @endif">
            <div class="admin-card admin-appearance h-100">
                <div class="admin-appearance__head">
                    <div>
                        <h2 class="admin-appearance__title">Appearance</h2>
                        <p class="admin-appearance__desc">Override the site default for this browser. Set the global default
                            in Settings → Appearance.</p>
                    </div>
                    <i class="bi bi-palette admin-appearance__icon" aria-hidden="true"></i>
                </div>
                @include('partials.admin.theme-toggle')
            </div>
        </div>
    </div>

    {{-- Sales KPIs --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="admin-card dashboard-kpi">
                <div class="dashboard-kpi__icon text-success"><i class="bi bi-currency-rupee"></i></div>
                <p class="text-muted small mb-1">Total revenue (paid)</p>
                <h3 class="mb-0">Rs. {{ number_format($revenue['total'] ?? 0, 0) }}</h3>
                <p class="small text-muted mb-0 mt-2">
                    Rs. {{ number_format($revenue['this_month'] ?? 0, 0) }} this month
                </p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="admin-card dashboard-kpi">
                <div class="dashboard-kpi__icon text-primary"><i class="bi bi-bag-check"></i></div>
                <p class="text-muted small mb-1">Orders</p>
                <h3 class="mb-0">{{ number_format($orderStats['total'] ?? 0) }}</h3>
                <p class="small text-muted mb-0 mt-2">
                    {{ $orderStats['today'] ?? 0 }} today · {{ $orderStats['this_month'] ?? 0 }} this month
                </p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="admin-card dashboard-kpi">
                <div class="dashboard-kpi__icon text-info"><i class="bi bi-people"></i></div>
                <p class="text-muted small mb-1">Registered customers</p>
                <h3 class="mb-0">{{ number_format($analytics['customers'] ?? 0) }}</h3>
                <p class="small text-muted mb-0 mt-2">
                    Rs. {{ number_format($revenue['today'] ?? 0, 0) }} revenue today
                </p>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="admin-card dashboard-kpi">
                <div class="dashboard-kpi__icon text-warning"><i class="bi bi-truck"></i></div>
                <p class="text-muted small mb-1">Fulfillment queue</p>
                <h3 class="mb-0">{{ number_format($orderStats['awaiting_delivery'] ?? 0) }}</h3>
                <p class="small text-muted mb-0 mt-2">
                    {{ $orderStats['pending'] ?? 0 }} pending confirmation
                </p>
            </div>
        </div>
    </div>

    {{-- Attention + catalog stats --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="admin-card h-100">
                <h2 class="h6 mb-3">Needs attention</h2>
                <ul class="list-unstyled dashboard-attention-list mb-0">
                    <li>
                        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                            class="dashboard-attention-link">
                            <span>Pending orders</span>
                            <span
                                class="badge {{ $attention['pending_orders'] ?? 0 ? 'text-bg-warning' : 'text-bg-secondary' }}">{{ $attention['pending_orders'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reviews.index') }}" class="dashboard-attention-link">
                            <span>Reviews to moderate</span>
                            <span
                                class="badge {{ $attention['pending_reviews'] ?? 0 ? 'text-bg-warning' : 'text-bg-secondary' }}">{{ $attention['pending_reviews'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.contact-messages.index') }}" class="dashboard-attention-link">
                            <span>Unread messages</span>
                            <span
                                class="badge {{ $attention['unread_messages'] ?? 0 ? 'text-bg-danger' : 'text-bg-secondary' }}">{{ $attention['unread_messages'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="dashboard-attention-link">
                            <span>Out of stock products</span>
                            <span
                                class="badge {{ $attention['out_of_stock'] ?? 0 ? 'text-bg-danger' : 'text-bg-secondary' }}">{{ $attention['out_of_stock'] ?? 0 }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="admin-card h-100">
                <h2 class="h6 mb-3">Catalog & engagement</h2>
                <div class="row g-3">
                    @foreach ([
            'total_products' => ['label' => 'Products', 'icon' => 'bi-box-seam'],
            'total_categories' => ['label' => 'Categories', 'icon' => 'bi-grid'],
            'total_brands' => ['label' => 'Brands', 'icon' => 'bi-award'],
            'total_reviews' => ['label' => 'Reviews', 'icon' => 'bi-star'],
            'newsletter_subscribers' => ['label' => 'Newsletter', 'icon' => 'bi-envelope-heart'],
            'out_of_stock_products' => ['label' => 'Out of stock', 'icon' => 'bi-exclamation-triangle'],
        ] as $key => $meta)
                        <div class="col-6 col-md-4">
                            <div class="dashboard-mini-stat">
                                <i class="bi {{ $meta['icon'] }}"></i>
                                <div>
                                    <p class="text-muted small mb-0">{{ $meta['label'] }}</p>
                                    <strong>{{ number_format($stats[$key] ?? 0) }}</strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="admin-card h-100 mb-3">
                <h2 class="h6 mb-3">Orders by status</h2>
                <div class="dashboard-chart-wrap dashboard-chart-wrap--donut">
                    <canvas id="orderStatusChart" aria-label="Orders by status chart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-card h-100">
                <h2 class="h6 mb-3">Payment status</h2>
                <div class="dashboard-chart-wrap dashboard-chart-wrap--donut">
                    <canvas id="paymentStatusChart" aria-label="Payment status chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent orders + top products --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="admin-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0">Recent orders</h2>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Placed</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($analytics['recent_orders'] ?? [] as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="fw-semibold text-decoration-none">{{ $order->order_number }}</a>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 10rem;">{{ $order->customer_name }}
                                        </div>
                                    </td>
                                    <td class="text-nowrap small text-muted">{{ $order->placed_at?->format('M j, g:i A') }}
                                    </td>
                                    <td class="text-nowrap">Rs. {{ number_format((float) $order->total, 0) }}</td>
                                    <td>@include('partials.admin.order-status-badge', [
                                        'status' => $order->status,
                                    ])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-4">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="admin-card h-100">
                <h2 class="h6 mb-3">Top selling products</h2>
                @if (count($analytics['top_products'] ?? []) > 0)
                    <ul class="list-unstyled dashboard-top-products mb-0">
                        @foreach ($analytics['top_products'] as $index => $product)
                            <li>
                                <span class="dashboard-top-products__rank">{{ $index + 1 }}</span>
                                <div class="flex-grow-1 min-w-0">
                                    <p class="mb-0 text-truncate fw-semibold">{{ $product['product_title'] }}</p>
                                    <p class="small text-muted mb-0">
                                        {{ $product['units_sold'] }} sold · Rs.
                                        {{ number_format($product['revenue'], 0) }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No paid order data yet.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const palette = {
                brown: '#3d2914',
                gold: '#c9a227',
                sand: '#e8e0d5',
                green: '#2d6a4f',
                red: '#9b2226',
                blue: '#4a6fa5',
            };

            const isDark = document.documentElement.getAttribute('data-admin-theme') === 'dark';
            const chartText = isDark ? '#b5a99a' : '#7a6b5c';
            const chartGrid = isDark ? '#3d342c' : '#f0ebe3';

            const doughnutOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: chartText
                        },
                    },
                },
            };

            const orderStatusCtx = document.getElementById('orderStatusChart');
            if (orderStatusCtx) {
                new Chart(orderStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{
                            data: @json($statusCounts),
                            backgroundColor: [palette.gold, palette.green, palette.red],
                        }],
                    },
                    options: doughnutOptions,
                });
            }

            const paymentStatusCtx = document.getElementById('paymentStatusChart');
            if (paymentStatusCtx) {
                new Chart(paymentStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($paymentLabels),
                        datasets: [{
                            data: @json($paymentCounts),
                            backgroundColor: [palette.red, palette.green, palette.gold],
                        }],
                    },
                    options: doughnutOptions,
                });
            }
        })();
    </script>
@endpush
