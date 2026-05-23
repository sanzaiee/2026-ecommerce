<?php

namespace App\Http\Requests\Product;

use App\Domain\Product\DTOs\CreateProductData;
use App\Enums\StockStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => strip_tags((string) $this->input('title')),
            'description' => $this->input('description') ? strip_tags((string) $this->input('description')) : null,
            'short_description' => $this->input('short_description') ? strip_tags((string) $this->input('short_description')) : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'stock_status' => ['required', Rule::enum(StockStatus::class)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'bullets' => ['nullable', 'array'],
            'additional_info' => ['nullable', 'array'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],
        ];
    }

    public function toDto(): CreateProductData
    {
        return new CreateProductData(
            title: $this->string('title')->toString(),
            slug: $this->input('slug'),
            description: $this->input('description'),
            shortDescription: $this->input('short_description'),
            price: (float) $this->input('price'),
            oldPrice: $this->input('old_price') !== null ? (float) $this->input('old_price') : null,
            stockStatus: StockStatus::from($this->input('stock_status')),
            categoryId: $this->input('category_id'),
            brandId: $this->input('brand_id'),
            metaTitle: $this->input('meta_title'),
            metaDescription: $this->input('meta_description'),
            metaKeywords: $this->input('meta_keywords'),
            isFeatured: $this->boolean('is_featured'),
            sortOrder: (int) $this->input('sort_order', 0),
            bullets: $this->input('bullets'),
            additionalInfo: $this->input('additional_info'),
            images: $this->file('images', []),
        );
    }
}
