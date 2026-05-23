@php
    $class = match ($status) {
        \App\Enums\DeliveryStatus::Delivered => 'bg-success',
        \App\Enums\DeliveryStatus::Shipped => 'bg-primary',
        \App\Enums\DeliveryStatus::Processing => 'bg-info text-dark',
        \App\Enums\DeliveryStatus::Cancelled => 'bg-danger',
        default => 'bg-secondary',
    };
@endphp
<span class="badge {{ $class }}">{{ ucfirst($status->value) }}</span>
