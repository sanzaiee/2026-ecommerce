<?php

namespace App\Domain\Order\DTOs;

readonly class PaymentInitiationData
{
    /**
     * @param  array<string, string|float|int>  $fields
     */
    public function __construct(
        public string $formAction,
        public array $fields,
    ) {}
}
