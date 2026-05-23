@extends('layouts.admin')

@section('title', $testimonial ? 'Edit Testimonial' : 'New Testimonial')

@section('content')
<h1 class="h3 mb-4">{{ $testimonial ? 'Edit' : 'Create' }} Testimonial</h1>
<div class="admin-card">
    <form method="POST" action="{{ $testimonial ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}">
        @csrf
        @if ($testimonial) @method('PUT') @endif
        <div class="mb-3">
            <label class="form-label" for="name">Customer name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $testimonial?->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="rating">Rating</label>
            <select name="rating" id="rating" class="form-select" required>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected((int) old('rating', $testimonial?->rating ?? 5) === $i)>{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label" for="text">Quote</label>
            <textarea name="text" id="text" class="form-control" rows="4" required>{{ old('text', $testimonial?->text) }}</textarea>
            <div class="form-text">Shown on the landing page testimonial section.</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="product_id">Related product (optional)</label>
            <select name="product_id" id="product_id" class="form-select">
                <option value="">None</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected((int) old('product_id', $testimonial?->product_id) === $product->id)>{{ $product->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="sort_order">Display order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" min="0" max="9999" value="{{ old('sort_order', $testimonial?->sort_order ?? 0) }}">
                <div class="form-text">Higher numbers appear first.</div>
            </div>
            <div class="col-md-6 mb-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="is_published" id="is_published" class="form-check-input" value="1" @checked(old('is_published', $testimonial?->is_published ?? true))>
                    <label class="form-check-label" for="is_published">Published on landing page</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-dark">Save</button>
        <a href="{{ route('admin.testimonials.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
