<?php

namespace App\Support\Store;

use App\Domain\Order\Payment\PaymentGatewayManager;

class CheckoutPaymentOptions
{
    public function __construct(private PaymentGatewayManager $gateways) {}

    /**
     * @return list<array{id: string, label: string, description: string}>
     */
    public function available(float $orderTotal): array
    {
        $options = [];

        if ($this->isCodAvailable($orderTotal)) {
            $options[] = [
                'id' => 'cod',
                'label' => 'Cash on delivery',
                'description' => 'Pay with cash when your order is delivered.',
            ];
        }

        if ($this->gateways->isOnlineEnabled()) {
            $options[] = [
                'id' => 'online',
                'label' => 'Pay with '.$this->gateways->label(),
                'description' => 'You will be redirected to complete payment securely.',
            ];
        }

        return $options;
    }

    public function defaultMethod(float $orderTotal): string
    {
        $options = $this->available($orderTotal);

        foreach ($options as $option) {
            if ($option['id'] === 'cod') {
                return 'cod';
            }
        }

        return $options[0]['id'] ?? 'cod';
    }

    public function isCodAvailable(float $orderTotal): bool
    {
        if (! config('store.payments.cod.enabled', true)) {
            return false;
        }

        $max = (float) config('store.payments.cod.max_order_total', 15000);

        return $orderTotal <= $max;
    }

    public function hasAny(float $orderTotal): bool
    {
        return $this->available($orderTotal) !== [];
    }
}
