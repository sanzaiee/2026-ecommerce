<?php

namespace App\Http\Requests\Store;

use App\Enums\PaymentMethod;
use App\Domain\Order\DTOs\PlaceOrderData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:32'],
            'shipping_address_line1' => ['required', 'string', 'max:255'],
            'shipping_address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_district' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['nullable', 'string', 'max:16'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(['cod', 'online'])],
        ];
    }

    public function toDto(): PlaceOrderData
    {
        return new PlaceOrderData(
            customerName: $this->string('customer_name')->toString(),
            customerEmail: $this->string('customer_email')->toString(),
            customerPhone: $this->string('customer_phone')->toString(),
            shippingAddressLine1: $this->string('shipping_address_line1')->toString(),
            shippingAddressLine2: $this->filled('shipping_address_line2')
                ? $this->string('shipping_address_line2')->toString()
                : null,
            shippingCity: $this->string('shipping_city')->toString(),
            shippingDistrict: $this->string('shipping_district')->toString(),
            shippingPostalCode: $this->filled('shipping_postal_code')
                ? $this->string('shipping_postal_code')->toString()
                : null,
            notes: $this->filled('notes') ? $this->string('notes')->toString() : null,
            paymentMethod: $this->string('payment_method')->toString() === 'cod'
                ? PaymentMethod::Cod
                : PaymentMethod::Online,
        );
    }
}
