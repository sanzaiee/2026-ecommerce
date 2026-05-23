<?php

namespace App\Domain\Order\DTOs;

use App\Enums\PaymentMethod;

readonly class PlaceOrderData
{
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public string $customerPhone,
        public string $shippingAddressLine1,
        public ?string $shippingAddressLine2,
        public string $shippingCity,
        public string $shippingDistrict,
        public ?string $shippingPostalCode,
        public ?string $notes,
        public PaymentMethod $paymentMethod,
    ) {}
}
