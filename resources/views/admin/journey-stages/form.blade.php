@extends('layouts.admin')

@section('title', $stage ? 'Edit Journey Stage' : 'New Journey Stage')

@section('content')
<h1 class="h3 mb-4">{{ $stage ? 'Edit' : 'Create' }} Journey Stage</h1>

<div class="admin-card">
    <form method="POST"
        action="{{ $stage ? route('admin.journey-stages.update', $stage) : route('admin.journey-stages.store') }}"
        enctype="multipart/form-data">
        @csrf
        @if ($stage) @method('PUT') @endif

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label" for="product_id">Product</label>
                <select name="product_id" id="product_id" class="form-select" required>
                    <option value="">Select a product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            @selected((int) old('product_id', $selectedProductId) === $product->id)>
                            {{ $product->title }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">This stage appears only on that product’s detail page.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="sort_order">Sort order</label>
                <input type="number" name="sort_order" id="sort_order" class="form-control" min="0"
                    value="{{ old('sort_order', $stage?->sort_order ?? 0) }}">
            </div>
            <div class="col-md-8">
                <label class="form-label" for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control"
                    value="{{ old('title', $stage?->title) }}" required maxlength="120"
                    placeholder="e.g. Clay">
            </div>
            <div class="col-12">
                <label class="form-label" for="text">Description</label>
                <textarea name="text" id="text" class="form-control" rows="3" required maxlength="1000"
                    placeholder="Describe this stage of the making process for this product.">{{ old('text', $stage?->text) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="image">Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                @if ($stage?->hasImage())
                    <x-admin.media-preview
                        :url="$stage->imageUrl('medium')"
                        :alt="$stage->title"
                        :remove-url="route('admin.journey-stages.image.destroy', $stage)"
                        confirm="Remove this journey stage image?"
                    />
                @endif
            </div>
            <div class="col-md-6 form-check mt-4">
                <input type="checkbox" name="is_published" value="1" class="form-check-input" id="is_published"
                    @checked(old('is_published', $stage?->is_published ?? true))>
                <label class="form-check-label" for="is_published">Published on this product’s page</label>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-dark">Save</button>
            <a href="{{ route('admin.journey-stages.index', array_filter(['product_id' => old('product_id', $selectedProductId)])) }}"
                class="btn btn-link">Cancel</a>
        </div>
    </form>
</div>
@endsection
