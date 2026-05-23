@extends('layouts.admin')

@section('title', 'Products')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Products</h1>
        <a href="{{ route('admin.products.create') }}" class="btn btn-dark">Add product</a>
    </div>

    <div class="admin-card mb-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="search">Name</label>
                <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}"
                    placeholder="Product name">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters->categoryId === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="brand_id">Brand</label>
                <select name="brand_id" id="brand_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected($filters->brandId === $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="status">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="" @selected(!$filters->visibility)>Active</option>
                    <option value="trashed" @selected($filters->visibility === 'trashed')>Deleted</option>
                    <option value="all" @selected($filters->visibility === 'all')>All</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="stock_status">Stock</label>
                <select name="stock_status" id="stock_status" class="form-select">
                    <option value="">All</option>
                    @foreach ($stockStatuses as $stockStatus)
                        <option value="{{ $stockStatus->value }}" @selected($filters->stockStatus === $stockStatus->value)>
                            {{ str_replace('_', ' ', ucfirst($stockStatus->value)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="date_from">Date from</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $filters->dateFrom }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="date_to">Date to</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $filters->dateTo }}">
            </div>
            <div class="col-md-2">
                <label class="form-label" for="per_page">Per page</label>
                <select name="per_page" id="per_page" class="form-select">
                    @foreach ([10, 20, 50] as $size)
                        <option value="{{ $size }}" @selected($filters->perPage === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark">Filter</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="admin-card">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width:48px"></th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr @class(['table-secondary' => $product->trashed()])>
                        <td>
                            <x-admin.media-thumb :url="$product->primaryImageUrl('thumbnail') ?: get_placeholder_image()" :alt="$product->title" />
                        </td>
                        <td>
                            <a href="{{ route('product.show', $product->slug) }}"
                                target="_blank">{{ $product->title }}</a>
                            @if ($product->trashed())
                                <span class="badge text-bg-secondary ms-1">Deleted</span>
                            @endif
                        </td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td>{{ $product->brand?->name ?? '—' }}</td>
                        <td>Rs. {{ number_format($product->price, 0) }}</td>
                        <td>{{ str_replace('_', ' ', $product->stock_status->value) }}</td>
                        <td class="text-nowrap">{{ $product->created_at?->format('M j, Y') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}"
                                class="btn btn-sm btn-outline-secondary">Edit</a>
                            @unless ($product->trashed())
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted">No products found.</td>
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
@endsection
