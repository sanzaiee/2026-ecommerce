<?php

namespace App\Domain\Order\Payment;

use App\Domain\Order\Contracts\PaymentGateway;
use App\Domain\Order\DTOs\PaymentInitiationData;
use App\Domain\Order\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EsewaPaymentGateway implements PaymentGateway
{
    public function name(): string
    {
        return 'esewa';
    }

    public function initiate(Order $order): PaymentInitiationData
    {
        $transactionUuid = $order->payment_reference ?? $order->order_number;

        $amount = (float) $order->total;
        $taxAmount = 0;
        $serviceCharge = 0;
        $deliveryCharge = 0;
        $totalAmount = $amount + $taxAmount + $serviceCharge + $deliveryCharge;

        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $signature = $this->generateSignature([
            'total_amount' => $this->formatAmount($totalAmount),
            'transaction_uuid' => $transactionUuid,
            'product_code' => $this->merchantCode(),
        ], $signedFieldNames);

        return new PaymentInitiationData(
            formAction: $this->formActionUrl(),
            fields: [
                'amount' => $this->formatAmount($amount),
                'tax_amount' => $this->formatAmount($taxAmount),
                'total_amount' => $this->formatAmount($totalAmount),
                'transaction_uuid' => $transactionUuid,
                'product_code' => $this->merchantCode(),
                'product_service_charge' => $this->formatAmount($serviceCharge),
                'product_delivery_charge' => $this->formatAmount($deliveryCharge),
                'success_url' => route('store.checkout.payment.success', $order->order_number),
                'failure_url' => route('store.checkout.payment.failure', $order->order_number),
                'signed_field_names' => $signedFieldNames,
                'signature' => $signature,
            ],
        );
    }

    public function verifyReturn(Request $request, Order $order): bool
    {
        $encoded = $request->query('data');

        if (! is_string($encoded) || $encoded === '') {
            return false;
        }

        $decoded = base64_decode($encoded, true);

        if ($decoded === false) {
            return false;
        }

        /** @var array<string, mixed>|null $payload */
        $payload = json_decode($decoded, true);

        if (! is_array($payload)) {
            return false;
        }

        $status = strtolower((string) ($payload['status'] ?? ''));
        $transactionUuid = (string) ($payload['transaction_uuid'] ?? '');
        $totalAmount = (string) ($payload['total_amount'] ?? '');

        if ($status !== 'complete') {
            return false;
        }

        if ($transactionUuid !== ($order->payment_reference ?? $order->order_number)) {
            return false;
        }

        if ($totalAmount !== $this->formatAmount((float) $order->total)) {
            Log::warning('eSewa amount mismatch', [
                'order' => $order->order_number,
                'expected' => $this->formatAmount((float) $order->total),
                'received' => $totalAmount,
            ]);

            return false;
        }

        return true;
    }

  /**
   * @param  array<string, string>  $fields
   */
    private function generateSignature(array $fields, string $signedFieldNames): string
    {
        $parts = [];

        foreach (explode(',', $signedFieldNames) as $key) {
            $key = trim($key);
            $parts[] = $key.'='.($fields[$key] ?? '');
        }

        $message = implode(',', $parts);

        return base64_encode(hash_hmac('sha256', $message, $this->secret(), true));
    }

    private function formActionUrl(): string
    {
        if ($this->isTestMode()) {
            return 'https://rc-epay.esewa.com.np/api/epay/main/v2/form';
        }

        return 'https://epay.esewa.com.np/api/epay/main/v2/form';
    }

    private function merchantCode(): string
    {
        return (string) config('store.payments.esewa.merchant_code', 'EPAYTEST');
    }

    private function secret(): string
    {
        return (string) config('store.payments.esewa.secret', '');
    }

    private function isTestMode(): bool
    {
        return (bool) config('store.payments.esewa.test_mode', true);
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
