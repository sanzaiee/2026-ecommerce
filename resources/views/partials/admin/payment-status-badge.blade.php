@php
    $class = match ($status) {
        \App\Enums\PaymentStatus::Paid => 'bg-success',
        \App\Enums\PaymentStatus::Failed => 'bg-danger',
        default => 'bg-warning text-dark',
    };
@endphp
<span class="badge {{ $class }}">{{ ucfirst($status->value) }}</span>
