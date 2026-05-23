@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Reviews</h1>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-select">
                <option value="">All products</option>
                @foreach ($filterProducts as $product)
                    <option value="{{ $product->id }}" @selected($filters->productId === $product->id)>{{ $product->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="" @selected($filters->isAll())>All</option>
                @foreach (\App\Enums\ReviewStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($filters->status === $status)>{{ ucfirst($status->value) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark">Filter</button>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Product</th>
                <th>Name</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Comment</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td>{{ $review->product?->title }}</td>
                    <td>{{ $review->name }}</td>
                    <td>{{ $review->rating }}/5</td>
                    <td>
                        <span class="badge {{ $review->status === \App\Enums\ReviewStatus::Approved ? 'text-bg-success' : 'text-bg-warning' }}">
                            {{ ucfirst($review->status->value) }}
                        </span>
                    </td>
                    <td>{{ Str::limit($review->comment, 80) }}</td>
                    <td class="text-end text-nowrap">
                        @if ($review->status === \App\Enums\ReviewStatus::Pending)
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-success">Approve</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted">
                        @if ($filters->productId)
                            No reviews found for this product.
                        @elseif ($filters->isAll())
                            No reviews found.
                        @elseif ($filters->status === \App\Enums\ReviewStatus::Approved)
                            No approved reviews.
                        @else
                            No pending reviews.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $reviews->withQueryString()->links() }}</div>
</div>
@endsection
