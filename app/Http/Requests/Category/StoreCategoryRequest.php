<?php

namespace App\Http\Requests\Category;

use App\Domain\Category\DTOs\CreateCategoryData;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags((string) $this->input('name')),
            'description' => $this->input('description') ? strip_tags((string) $this->input('description')) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function toDto(): CreateCategoryData
    {
        return new CreateCategoryData(
            name: $this->string('name')->toString(),
            slug: $this->input('slug'),
            description: $this->input('description'),
            metaTitle: $this->input('meta_title'),
            metaDescription: $this->input('meta_description'),
            metaKeywords: $this->input('meta_keywords'),
            sortOrder: (int) $this->input('sort_order', 0),
            image: $this->file('image'),
        );
    }
}
