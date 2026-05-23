@extends('layouts.admin')

@section('title', $category ? 'Edit Category' : 'New Category')

@section('content')
    <h1 class="h3 mb-4">{{ $category ? 'Edit' : 'Create' }} Category</h1>
    <div class="admin-card">
        <form method="POST"
            action="{{ $category ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
            enctype="multipart/form-data">
            @csrf
            @if ($category)
                @method('PUT')
            @endif
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category?->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug (optional)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $category?->slug) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $category?->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if ($category)
                    <x-admin.media-preview :url="$category->imageUrl('medium')" :alt="$category->name" :remove-url="route('admin.categories.image.destroy', $category)"
                        confirm="Remove this category image?" />
                @endif
            </div>
            <button type="submit" class="btn btn-dark">Save</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
@endsection
