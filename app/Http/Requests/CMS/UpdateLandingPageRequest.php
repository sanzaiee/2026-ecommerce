<?php

namespace App\Http\Requests\CMS;

use App\Domain\CMS\DTOs\UpdateLandingPageData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['hero_title', 'hero_subtitle', 'meta_title', 'meta_description', 'meta_keywords'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => strip_tags((string) $this->input($field))]);
            }
        }

        foreach (['featured_category_ids', 'everyday_product_ids', 'top_selling_product_ids'] as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => array_values(array_filter(
                        (array) $this->input($field),
                        fn ($id) => (int) $id > 0,
                    )),
                ]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'hero_image' => ['nullable', 'image', 'max:5120'],
            'featured_category_ids' => ['nullable', 'array'],
            'featured_category_ids.*' => ['integer', 'exists:categories,id'],
            'everyday_product_ids' => ['nullable', 'array'],
            'everyday_product_ids.*' => ['integer', 'exists:products,id'],
            'top_selling_product_ids' => ['nullable', 'array'],
            'top_selling_product_ids.*' => ['integer', 'exists:products,id'],
        ];
    }

    public function toDto(): UpdateLandingPageData
    {
        return new UpdateLandingPageData(
            heroTitle: $this->input('hero_title'),
            heroSubtitle: $this->input('hero_subtitle'),
            metaTitle: $this->input('meta_title'),
            metaDescription: $this->input('meta_description'),
            metaKeywords: $this->input('meta_keywords'),
            heroImage: $this->file('hero_image'),
            featuredCategoryIds: $this->input('featured_category_ids'),
            everydayProductIds: $this->input('everyday_product_ids'),
            topSellingProductIds: $this->input('top_selling_product_ids'),
        );
    }
}
