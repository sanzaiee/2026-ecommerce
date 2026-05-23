<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class CartSyncRequest extends FormRequest
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
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    /**
     * @return list<array{id: string, qty: int}>
     */
    public function lines(): array
    {
        return collect($this->input('items', []))
            ->map(fn (array $item) => [
                'id' => (string) $item['id'],
                'qty' => (int) $item['qty'],
            ])
            ->all();
    }
}
