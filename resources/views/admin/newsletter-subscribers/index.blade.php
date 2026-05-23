@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Newsletter Subscribers</h1>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.newsletter-subscribers.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="search">Search</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Email address">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="active" @selected($filters->status === 'active')>Active</option>
                <option value="unsubscribed" @selected($filters->status === 'unsubscribed')>Unsubscribed</option>
                <option value="all" @selected($filters->status === 'all')>All</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark">Filter</button>
            <a href="{{ route('admin.newsletter-subscribers.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>Email</th>
                <th>Subscribed</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subscribers as $subscriber)
                <tr>
                    <td><a href="mailto:{{ $subscriber->email }}">{{ $subscriber->email }}</a></td>
                    <td class="text-nowrap">{{ $subscriber->subscribed_at?->format('M j, Y g:i A') }}</td>
                    <td>
                        <span class="badge {{ $subscriber->isActive() ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $subscriber->isActive() ? 'Active' : 'Unsubscribed' }}
                        </span>
                        @if ($subscriber->unsubscribed_at)
                            <span class="small text-muted d-block">{{ $subscriber->unsubscribed_at->format('M j, Y') }}</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <form method="POST" action="{{ route('admin.newsletter-subscribers.destroy', $subscriber) }}" class="d-inline" onsubmit="return confirm('Remove this subscriber?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted">No subscribers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $subscribers->withQueryString()->links() }}</div>
</div>
@endsection
