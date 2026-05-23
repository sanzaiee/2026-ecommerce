<?php

namespace App\Domain\Order\Contracts;

use App\Domain\Order\DTOs\PaymentInitiationData;
use App\Domain\Order\Models\Order;
use Illuminate\Http\Request;

interface PaymentGateway
{
    public function name(): string;

    public function initiate(Order $order): PaymentInitiationData;

    public function verifyReturn(Request $request, Order $order): bool;
}
