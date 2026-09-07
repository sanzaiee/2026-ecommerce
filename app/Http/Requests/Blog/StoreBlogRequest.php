<?php

namespace App\Http\Requests\Blog;

use App\Domain\Blog\DTOs\CreateBlogData;
use App\Domain\Blog\Models\Blog;
use App\Services\HtmlSanitizer;
use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Blog::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim(strip_tags((string) $this->input('title'))),
            'content' => app(HtmlSanitizer::class)->clean((string) $this->input('content')) ?? '',
            'excerpt' => trim(strip_tags((string) $this->input('excerpt', ''))),
            'meta_title' => trim(strip_tags((string) $this->input('meta_title', ''))),
            'meta_description' => trim(strip_tags((string) $this->input('meta_description', ''))),
            'meta_keywords' => trim(strip_tags((string) $this->input('meta_keywords', ''))),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:65535'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'blog_category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function toDto(): CreateBlogData
    {
        return new CreateBlogData(
            title: $this->string('title')->trim()->toString(),
            content: (string) $this->input('content'),
            excerpt: $this->filled('excerpt') ? $this->string('excerpt')->trim()->toString() : null,
            blogCategoryId: $this->filled('blog_category_id') ? (int) $this->input('blog_category_id') : null,
            metaTitle: $this->filled('meta_title') ? $this->string('meta_title')->trim()->toString() : null,
            metaDescription: $this->filled('meta_description') ? $this->string('meta_description')->trim()->toString() : null,
            metaKeywords: $this->filled('meta_keywords') ? $this->string('meta_keywords')->trim()->toString() : null,
            position: (int) $this->input('position', 0),
            isFeatured: $this->boolean('is_featured'),
            image: $this->file('image'),
        );
    }
}
