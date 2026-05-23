<?php

namespace App\Domain\Order\DTOs;

readonly class CheckoutTotalsData
{
    public function __construct(
        public float $subtotal,
        public float $shippingAmount,
        public float $total,
        public int $itemCount,
    ) {}
}
