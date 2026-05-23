<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class WishlistSyncRequest extends FormRequest
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
        ];
    }

    /**
     * @return list<string>
     */
    public function slugs(): array
    {
        return collect($this->input('items', []))
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }
}
