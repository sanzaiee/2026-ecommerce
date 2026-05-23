@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-dark">Add category</a>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label" for="search">Name</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Category name or slug">
        </div>
        <div class="col-md-2">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="" @selected(! $filters->visibility)>Active</option>
                <option value="trashed" @selected($filters->visibility === 'trashed')>Deleted</option>
                <option value="all" @selected($filters->visibility === 'all')>All</option>
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
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr @class(['table-secondary' => $category->trashed()])>
                    <td>
                        <x-admin.media-thumb :url="$category->imageUrl('thumbnail')" :alt="$category->name" />
                        {{ $category->name }}
                        @if ($category->trashed())
                            <span class="badge text-bg-secondary ms-1">Deleted</span>
                        @endif
                    </td>
                    <td><a href="{{ route('category.show', $category->slug) }}" target="_blank">{{ $category->slug }}</a></td>
                    <td>{{ $category->products_count }}</td>
                    <td class="text-nowrap">{{ $category->created_at?->format('M j, Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @unless ($category->trashed())
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No categories found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($categories->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">
            <p class="text-muted small mb-0">
                Showing {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }} categories
            </p>
            {{ $categories->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
