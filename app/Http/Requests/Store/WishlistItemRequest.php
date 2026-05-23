<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class WishlistItemRequest extends FormRequest
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
        ];
    }

    public function productSlug(): string
    {
        return $this->string('id')->toString();
    }
}
