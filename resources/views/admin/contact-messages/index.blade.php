@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Contact Messages</h1>
</div>

<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label" for="search">Search</label>
            <input type="search" name="search" id="search" class="form-control" value="{{ $filters->search }}" placeholder="Name, email, subject, message">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="read">Status</label>
            <select name="read" id="read" class="form-select">
                <option value="">All</option>
                <option value="unread" @selected($filters->read === 'unread')>Unread</option>
                <option value="read" @selected($filters->read === 'read')>Read</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark">Filter</button>
            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>From</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Received</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messages as $message)
                <tr class="{{ $message->isUnread() ? 'table-warning' : '' }}">
                    <td>
                        <div class="fw-semibold">{{ $message->name }}</div>
                        <div class="small text-muted">{{ $message->email }}</div>
                    </td>
                    <td>{{ $message->subject }}</td>
                    <td>{{ Str::limit($message->message, 60) }}</td>
                    <td class="text-nowrap">{{ $message->created_at?->format('M j, Y g:i A') }}</td>
                    <td>
                        <span class="badge {{ $message->isUnread() ? 'text-bg-warning' : 'text-bg-secondary' }}">
                            {{ $message->isUnread() ? 'Unread' : 'Read' }}
                        </span>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.contact-messages.show', $message) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted">No contact messages found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $messages->withQueryString()->links() }}</div>
</div>
@endsection
