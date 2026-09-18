@extends('layouts.admin')

@section('title', 'Journey Stages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Journey Stages</h1>
        <p class="text-muted small mb-0">Each product has its own clay-to-pot timeline on the product page.</p>
    </div>
    <a href="{{ route('admin.journey-stages.create', array_filter(['product_id' => $filters->productId])) }}" class="btn btn-dark">Add stage</a>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.journey-stages.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label" for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-select">
                <option value="">All products</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected($filters->productId === $product->id)>{{ $product->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" for="search">Search</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Title or description">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="published">Status</label>
            <select name="published" id="published" class="form-select">
                <option value="" @selected(! $filters->published)>All</option>
                <option value="published" @selected($filters->published === 'published')>Published</option>
                <option value="draft" @selected($filters->published === 'draft')>Draft</option>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label" for="per_page">Per page</label>
            <select name="per_page" id="per_page" class="form-select">
                @foreach ([10, 20, 50] as $size)
                    <option value="{{ $size }}" @selected($filters->perPage === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark">Filter</button>
            <a href="{{ route('admin.journey-stages.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Title</th>
                <th>Description</th>
                <th>Order</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stages as $stage)
                <tr>
                    <td>
                        <x-admin.media-thumb
                            :url="$stage->hasImage() ? $stage->imageUrl('thumbnail') : get_placeholder_image()"
                            :alt="$stage->title"
                        />
                    </td>
                    <td>
                        @if ($stage->product)
                            <a href="{{ route('admin.journey-stages.index', ['product_id' => $stage->product_id]) }}">{{ $stage->product->title }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $stage->title }}</td>
                    <td class="text-truncate" style="max-width: 280px;">{{ Str::limit($stage->text, 80) }}</td>
                    <td>{{ $stage->sort_order }}</td>
                    <td>
                        @if ($stage->is_published)
                            <span class="badge text-bg-success">Published</span>
                        @else
                            <span class="badge text-bg-secondary">Draft</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.journey-stages.edit', $stage) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.journey-stages.destroy', $stage) }}" class="d-inline" onsubmit="return confirm('Delete this journey stage?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">No journey stages yet. Add stages for a product to showcase its making process.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($stages->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">
            <p class="text-muted small mb-0">
                Showing {{ $stages->firstItem() }}–{{ $stages->lastItem() }} of {{ $stages->total() }} stages
            </p>
            {{ $stages->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
