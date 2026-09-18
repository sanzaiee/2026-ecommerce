<?php

namespace App\Http\Requests\Journey;

use App\Domain\Journey\DTOs\CreateJourneyStageData;
use Illuminate\Foundation\Http\FormRequest;

class StoreJourneyStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => strip_tags((string) $this->input('title')),
            'text' => strip_tags((string) $this->input('text')),
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'title' => ['required', 'string', 'max:120'],
            'text' => ['required', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function toDto(): CreateJourneyStageData
    {
        return new CreateJourneyStageData(
            productId: (int) $this->input('product_id'),
            title: $this->string('title')->toString(),
            text: $this->string('text')->toString(),
            sortOrder: (int) $this->input('sort_order', 0),
            isPublished: $this->boolean('is_published'),
            image: $this->file('image'),
        );
    }
}
