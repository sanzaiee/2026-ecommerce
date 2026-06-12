@extends('layouts.admin')

@section('title', 'Stock Management')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Stock Management</h1>
    </div>

    <div class="admin-card mb-4">
        <form method="GET" action="{{ route('admin.stock.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="search">Search products</label>
                <input type="search" name="search" id="search" class="form-control" value="{{ $search }}"
                    placeholder="Product name">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="stock_status">Stock Filter</label>
                <select name="stock_status" id="stock_status" class="form-select">
                    <option value="" @selected($stockStatus === '')>All</option>
                    <option value="low" @selected($stockStatus === 'low')">Low Stock (1-10)</option>
                    <option value="out" @selected($stockStatus === 'out')">Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark">Filter</button>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:48px"></th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                    <th>Quick Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <x-admin.media-thumb :url="$product->primaryImageUrl('thumbnail') ?: get_placeholder_image()" :alt="$product->title" />
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $product) }}" class="fw-bold">{{ $product->title }}</a>
                            <div class="small text-muted">
                                <a href="{{ route('product.show', $product->slug) }}" target="_blank">View on site</a>
                                ·
                                <a href="{{ route('admin.products.edit', $product) }}">Edit product</a>
                            </div>
                        </td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td>
                            <span class="h5 mb-0">{{ $product->stock_quantity }}</span>
                        </td>
                        <td>
                            @php($stockQty = (int) $product->stock_quantity)
                            <span @class([
                                'badge',
                                'text-bg-success' => $stockQty > 10,
                                'text-bg-warning' => $stockQty > 0 && $stockQty <= 10,
                                'text-bg-danger' => $stockQty <= 0,
                            ])>
                                @if ($stockQty <= 0)
                                    Out of Stock
                                @elseif ($stockQty <= 10)
                                    Low Stock
                                @else
                                    In Stock
                                @endif
                            </span>
                        </td>
                        <td style="min-width: 200px">
                            <form method="POST" action="{{ route('admin.stock.adjust', $product) }}" class="d-flex gap-2 align-items-center">
                                @csrf
                                @method('PATCH')
                                <div class="input-group input-group-sm" style="max-width: 140px">
                                    <input type="number" name="adjustment" class="form-control"
                                        placeholder="±" min="-99" max="99" required>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" title="Adjust stock">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit full product">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($products->total() > 0)
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">
                <p class="text-muted small mb-0">
                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
                </p>
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <div class="mt-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Stock Management Guide
                        </h5>
                        <ul class="mb-0 small">
                            <li><strong>Green badge:</strong> Stock > 10 (Healthy)</li>
                            <li><strong>Yellow badge:</strong> Stock 1-10 (Low stock - consider reordering)</li>
                            <li><strong>Red badge:</strong> Stock = 0 (Out of stock - cannot be ordered)</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-lightbulb me-2"></i>Quick Actions
                        </h5>
                        <ul class="mb-0 small">
                            <li>Use the <strong>Quick Update</strong> input to add/remove stock</li>
                            <li>Enter <strong>positive numbers</strong> to add stock</li>
                            <li>Enter <strong>negative numbers</strong> to remove stock</li>
                            <li>Click <strong>Edit</strong> button to update all product details</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection