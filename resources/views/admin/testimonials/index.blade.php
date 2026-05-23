@extends('layouts.admin')

@section('title', 'Testimonials')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Testimonials</h1>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-dark">Add testimonial</a>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.testimonials.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label" for="search">Search</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Name or quote">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="published">Status</label>
            <select name="published" id="published" class="form-select">
                <option value="" @selected(! $filters->published)>All</option>
                <option value="published" @selected($filters->published === 'published')>Published</option>
                <option value="draft" @selected($filters->published === 'draft')>Draft</option>
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
            <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Rating</th>
                <th>Quote</th>
                <th>Product</th>
                <th>Order</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($testimonials as $testimonial)
                <tr>
                    <td>{{ $testimonial->name }}</td>
                    <td class="text-nowrap">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill text-warning' : '' }}"></i>
                        @endfor
                    </td>
                    <td class="text-truncate" style="max-width: 280px;">{{ Str::limit($testimonial->text, 80) }}</td>
                    <td>
                        @if ($testimonial->product)
                            <a href="{{ route('product.show', $testimonial->product->slug) }}" target="_blank">{{ $testimonial->product->title }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $testimonial->sort_order }}</td>
                    <td>
                        @if ($testimonial->is_published)
                            <span class="badge text-bg-success">Published</span>
                        @else
                            <span class="badge text-bg-secondary">Draft</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="d-inline" onsubmit="return confirm('Delete this testimonial?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">No testimonials yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($testimonials->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">
            <p class="text-muted small mb-0">
                Showing {{ $testimonials->firstItem() }}–{{ $testimonials->lastItem() }} of {{ $testimonials->total() }} testimonials
            </p>
            {{ $testimonials->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
