<?php

namespace App\Domain\Order\Payment;

use App\Domain\Order\Contracts\PaymentGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function activeGatewayName(): ?string
    {
        $name = config('store.payments.gateway');

        return is_string($name) && $name !== '' ? $name : null;
    }

    public function isOnlineEnabled(): bool
    {
        return $this->resolve() !== null;
    }

    public function resolve(): ?PaymentGateway
    {
        $name = $this->activeGatewayName();

        if (! $name) {
            return null;
        }

        return match ($name) {
            'esewa' => app(EsewaPaymentGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway [{$name}]."),
        };
    }

    public function label(): string
    {
        return match ($this->activeGatewayName()) {
            'esewa' => 'eSewa',
            'khalti' => 'Khalti',
            default => 'Online payment',
        };
    }
}
