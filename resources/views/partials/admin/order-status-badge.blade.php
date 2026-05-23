@php
    $class = match ($status) {
        \App\Enums\OrderStatus::Confirmed => 'bg-success',
        \App\Enums\OrderStatus::Cancelled => 'bg-danger',
        default => 'bg-warning text-dark',
    };
@endphp
<span class="badge {{ $class }}">{{ ucfirst($status->value) }}</span>
