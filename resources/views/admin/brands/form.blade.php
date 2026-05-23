@extends('layouts.admin')

@section('title', $brand ? 'Edit Brand' : 'New Brand')

@section('content')
<h1 class="h3 mb-4">{{ $brand ? 'Edit' : 'Create' }} Brand</h1>
<div class="admin-card">
    <form method="POST" action="{{ $brand ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($brand) @method('PUT') @endif
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $brand?->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Slug (optional)</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $brand?->slug) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $brand?->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Logo / image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            @if ($brand)
                <x-admin.media-preview
                    :url="$brand->imageUrl('medium')"
                    :alt="$brand->name"
                    :remove-url="route('admin.brands.image.destroy', $brand)"
                    confirm="Remove this brand logo?"
                />
            @endif
        </div>
        <div class="mb-3">
            <label class="form-label">Meta title</label>
            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $brand?->meta_title) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Meta description</label>
            <input type="text" name="meta_description" class="form-control" value="{{ old('meta_description', $brand?->meta_description) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Meta keywords</label>
            <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $brand?->meta_keywords) }}">
        </div>
        <button type="submit" class="btn btn-dark">Save</button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
