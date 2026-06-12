@extends('layouts.admin')

@section('title', $product ? 'Edit Product' : 'New Product')

@section('content')
<h1 class="h3 mb-4">{{ $product ? 'Edit' : 'Create' }} Product</h1>

<div class="admin-card">
    <form method="POST" action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($product) @method('PUT') @endif

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $product?->title) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Slug (optional)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $product?->slug) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Short description</label>
                <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product?->short_description) }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $product?->description) }}</textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Price (Rs.)</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product?->price) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Old price</label>
                <input type="number" step="0.01" name="old_price" class="form-control" value="{{ old('old_price', $product?->old_price) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Stock status</label>
                <select name="stock_status" class="form-select" required>
                    @foreach ($stockStatuses as $status)
                        <option value="{{ $status->value }}" @selected(old('stock_status', $product?->stock_status?->value) === $status->value)>{{ str_replace('_', ' ', $status->value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stock quantity</label>
                <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product?->stock_quantity ?? 0) }}" min="0" required>
                <small class="text-muted">Set to 0 for out of stock</small>
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">—</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('category_id', $product?->category_id) == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Brand</label>
                <select name="brand_id" class="form-select">
                    <option value="">—</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(old('brand_id', $product?->brand_id) == $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Product images</label>
                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                @if ($product?->hasImages())
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach ($product->galleryMedia() as $media)
                            <x-admin.media-preview
                                class="admin-media-preview--sm"
                                :url="$product->mediaPreviewUrl($media, 'thumbnail')"
                                :alt="$product->title"
                                :remove-url="route('admin.products.media.destroy', [$product, $media])"
                                confirm="Remove this product image?"
                                :width="64"
                                :height="64"
                            />
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-md-4 form-check mt-4">
                <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured" @checked(old('is_featured', $product?->is_featured))>
                <label class="form-check-label" for="is_featured">Featured</label>
            </div>
            <div class="col-md-4">
                <label class="form-label">Meta title</label>
                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $product?->meta_title) }}">
            </div>
            <div class="col-md-8">
                <label class="form-label">Meta description</label>
                <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $product?->meta_description) }}">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-dark">Save</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-link">Cancel</a>
        </div>
    </form>
</div>
@endsection
