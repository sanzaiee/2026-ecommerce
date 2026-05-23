<?php

namespace App\Http\Requests\Brand;

use App\Domain\Brand\DTOs\CreateBrandData;
use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255', 'unique:brands,slug'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function toDto(): CreateBrandData
    {
        return new CreateBrandData(
            name: $this->string('name')->toString(),
            slug: $this->input('slug'),
            description: $this->input('description'),
            metaTitle: $this->input('meta_title'),
            metaDescription: $this->input('meta_description'),
            metaKeywords: $this->input('meta_keywords'),
            image: $this->file('image'),
        );
    }
}
