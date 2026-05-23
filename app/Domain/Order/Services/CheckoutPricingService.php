<?php

namespace App\Domain\Order\Services;

use App\Domain\Order\DTOs\CheckoutTotalsData;

class CheckoutPricingService
{
    public function calculate(float $subtotal, int $itemCount): CheckoutTotalsData
    {
        $freeThreshold = (float) config('store.shipping.free_threshold', 2000);
        $flatRate = (float) config('store.shipping.flat_rate', 150);

        $shippingAmount = $subtotal >= $freeThreshold ? 0.0 : $flatRate;

        return new CheckoutTotalsData(
            subtotal: round($subtotal, 2),
            shippingAmount: round($shippingAmount, 2),
            total: round($subtotal + $shippingAmount, 2),
            itemCount: $itemCount,
        );
    }
}
