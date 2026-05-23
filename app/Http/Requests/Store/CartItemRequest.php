<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
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
            'id' => ['required', 'string', 'max:255'],
            'qty' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function productSlug(): string
    {
        return $this->string('id')->toString();
    }

    public function quantity(): int
    {
        return $this->integer('qty', 1);
    }
}
