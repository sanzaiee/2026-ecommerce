@extends('layouts.admin')

@section('title', 'Brands')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Brands</h1>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-dark">Add brand</a>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.brands.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label" for="search">Name</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Brand name or slug">
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
            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">Reset</a>
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
            @forelse ($brands as $brand)
                <tr @class(['table-secondary' => $brand->trashed()])>
                    <td>
                        <x-admin.media-thumb :url="$brand->imageUrl('thumbnail')" :alt="$brand->name" />
                        {{ $brand->name }}
                        @if ($brand->trashed())
                            <span class="badge text-bg-secondary ms-1">Deleted</span>
                        @endif
                    </td>
                    <td><a href="{{ route('brand.show', $brand->slug) }}" target="_blank">{{ $brand->slug }}</a></td>
                    <td>{{ $brand->products_count }}</td>
                    <td class="text-nowrap">{{ $brand->created_at?->format('M j, Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        @unless ($brand->trashed())
                            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No brands found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if ($brands->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-3">
            <p class="text-muted small mb-0">
                Showing {{ $brands->firstItem() }}–{{ $brands->lastItem() }} of {{ $brands->total() }} brands
            </p>
            {{ $brands->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
