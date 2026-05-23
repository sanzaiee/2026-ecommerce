<?php

namespace App\Domain\Order\DTOs;

use App\Enums\DeliveryStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

readonly class OrderFilterData
{
    public function __construct(
        public ?string $search = null,
        public ?OrderStatus $status = null,
        public ?DeliveryStatus $deliveryStatus = null,
        public ?PaymentStatus $paymentStatus = null,
        public int $perPage = 20,
        public int $page = 1,
    ) {}
}
