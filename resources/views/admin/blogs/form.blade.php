@extends('layouts.admin')

@section('title', $blog ? 'Edit Blog Post' : 'New Blog Post')

@section('content')
<h1 class="h3 mb-4">{{ $blog ? 'Edit' : 'Create' }} Blog Post</h1>
<div class="admin-card">
    <form method="POST" action="{{ $blog ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" enctype="multipart/form-data" data-rich-text>
        @csrf
        @if ($blog) @method('PUT') @endif
        <div class="mb-3">
            <label class="form-label" for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $blog?->title) }}" required maxlength="255">
        </div>
        <div class="mb-3">
            <label class="form-label" for="blog_category_id">Category</label>
            <select name="blog_category_id" id="blog_category_id" class="form-select">
                <option value="">Uncategorized</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('blog_category_id', $blog?->blog_category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">Group this article under a Newari tradition topic for the blog hub.</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="excerpt">Excerpt</label>
            <textarea name="excerpt" id="excerpt" class="form-control" rows="3" maxlength="500">{{ old('excerpt', $blog?->excerpt) }}</textarea>
            <div class="form-text">Short summary for search results and article cards. Leave blank to auto-generate from content.</div>
        </div>
        <div class="mb-3">
            <label class="form-label" for="image">Featured image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/webp">
            <div class="form-text">JPG, PNG, or WebP up to 5 MB. Shown on the blog listing and post page.</div>
            @if ($blog?->hasImage())
                <x-admin.media-preview
                    :url="$blog->imageUrl('medium')"
                    :alt="$blog->title"
                    :remove-url="route('admin.blogs.image.destroy', $blog)"
                    confirm="Remove this blog image?"
                    :width="200"
                    :height="200"
                />
            @endif
        </div>
        <div class="mb-3">
            <label class="form-label" for="content">Content</label>
            <textarea name="content" id="content" class="form-control rich-text" rows="12">{{ old('content', $blog?->content) }}</textarea>
            <div class="form-text">Use the editor for headings, lists, and links. Scripts and unsafe HTML are stripped on save.</div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label" for="position">Position</label>
                <input type="number" name="position" id="position" class="form-control" min="0" max="9999" value="{{ old('position', $blog?->position ?? 0) }}">
                <div class="form-text">Higher numbers appear first. Featured posts still sort above standard posts.</div>
            </div>
            <div class="col-md-6 mb-3 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="is_featured" id="is_featured" class="form-check-input" value="1" @checked(old('is_featured', $blog?->is_featured ?? false))>
                    <label class="form-check-label" for="is_featured">Featured on the blog listing</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-dark">Save</button>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
    <script src="{{ asset('js/admin-rich-text.js') }}"></script>
@endpush
