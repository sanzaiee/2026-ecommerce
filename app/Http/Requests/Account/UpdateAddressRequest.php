<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isCustomer() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only([
            'phone',
            'shipping_address_line1',
            'shipping_address_line2',
            'shipping_city',
            'shipping_district',
            'shipping_postal_code',
        ]))->map(fn ($value) => is_string($value) ? strip_tags($value) : $value)->all());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:32'],
            'shipping_address_line1' => ['required', 'string', 'max:255'],
            'shipping_address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_district' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['nullable', 'string', 'max:16'],
        ];
    }
}
