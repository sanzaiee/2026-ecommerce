@extends('layouts.admin')

@section('title', 'Blog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Blog</h1>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-dark">Add post</a>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.blogs.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label" for="search">Search</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Title or content">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="featured">Featured</label>
            <select name="featured" id="featured" class="form-select">
                <option value="" @selected(! $filters->featured)>All</option>
                <option value="featured" @selected($filters->featured === 'featured')>Featured</option>
                <option value="standard" @selected($filters->featured === 'standard')>Standard</option>
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
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th style="width: 72px;"></th>
                <th>Title</th>
                <th>Category</th>
                <th>Position</th>
                <th>Featured</th>
                <th>Updated</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($blogs as $blog)
                <tr>
                    <td>
                        @if ($blog->hasImage())
                            <img src="{{ $blog->imageUrl('thumbnail') }}" alt="" width="48" height="48" class="rounded object-fit-cover">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" rel="noopener">{{ $blog->title }}</a>
                    </td>
                    <td>{{ $blog->category?->name ?? '—' }}</td>
                    <td>{{ $blog->position }}</td>
                    <td>
                        @if ($blog->is_featured)
                            <span class="badge text-bg-success">Featured</span>
                        @else
                            <span class="badge text-bg-secondary">Standard</span>
                        @endif
                    </td>
                    <td>{{ $blog->updated_at?->format('M j, Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" class="d-inline" onsubmit="return confirm('Delete this blog post?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted">No blog posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($blogs->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">
            <p class="text-muted small mb-0">
                Showing {{ $blogs->firstItem() }}–{{ $blogs->lastItem() }} of {{ $blogs->total() }} posts
            </p>
            {{ $blogs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
