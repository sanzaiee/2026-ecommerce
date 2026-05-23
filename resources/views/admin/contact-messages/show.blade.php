@extends('layouts.admin')

@section('title', 'Contact Message')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.contact-messages.index') }}" class="text-muted small text-decoration-none">&larr; All messages</a>
        <h1 class="h3 mb-0 mt-1">{{ $message->subject }}</h1>
        <p class="text-muted mb-0">Received {{ $message->created_at?->format('M j, Y g:i A') }}</p>
    </div>
    <div class="d-flex gap-2">
        @if ($message->isUnread())
            <form method="POST" action="{{ route('admin.contact-messages.read', $message) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-dark">Mark as read</button>
            </form>
        @endif
        <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <h2 class="h5 mb-3">Message</h2>
            <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card">
            <h2 class="h5 mb-3">Sender</h2>
            <dl class="mb-0">
                <dt class="text-muted small">Name</dt>
                <dd>{{ $message->name }}</dd>
                <dt class="text-muted small">Email</dt>
                <dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
                @if ($message->phone)
                    <dt class="text-muted small">Phone</dt>
                    <dd><a href="tel:{{ $message->phone }}">{{ $message->phone }}</a></dd>
                @endif
                <dt class="text-muted small">Subject</dt>
                <dd>{{ $message->subject }}</dd>
                <dt class="text-muted small">Status</dt>
                <dd>
                    <span class="badge {{ $message->isUnread() ? 'text-bg-warning' : 'text-bg-secondary' }}">
                        {{ $message->isUnread() ? 'Unread' : 'Read' }}
                    </span>
                    @if ($message->read_at)
                        <span class="small text-muted d-block mt-1">Read {{ $message->read_at->format('M j, Y g:i A') }}</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>
@endsection
