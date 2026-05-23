<?php

namespace App\Http\Requests\Testimonial;

use App\Domain\Testimonial\DTOs\UpdateTestimonialData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags((string) $this->input('name')),
            'text' => strip_tags((string) $this->input('text')),
            'is_published' => $this->boolean('is_published'),
            'product_id' => $this->filled('product_id') ? $this->integer('product_id') : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'text' => ['required', 'string', 'max:2000'],
            'product_id' => ['nullable', 'integer', Rule::exists('products', 'id')],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function toDto(): UpdateTestimonialData
    {
        return new UpdateTestimonialData(
            id: (int) $this->route('testimonial'),
            name: $this->string('name')->toString(),
            rating: $this->integer('rating'),
            text: $this->string('text')->toString(),
            productId: $this->input('product_id'),
            isPublished: $this->boolean('is_published'),
            sortOrder: (int) $this->input('sort_order', 0),
        );
    }
}
