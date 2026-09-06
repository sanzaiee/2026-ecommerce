<?php

namespace App\Http\Requests\Blog;

use App\Domain\Blog\DTOs\UpdateBlogData;
use App\Domain\Blog\Models\Blog;
use App\Services\HtmlSanitizer;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if (! $user?->isAdmin()) {
            return false;
        }

        $blog = Blog::query()->find($this->route('blog'));

        return $blog === null || $user->can('update', $blog);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim(strip_tags((string) $this->input('title'))),
            'content' => app(HtmlSanitizer::class)->clean((string) $this->input('content')) ?? '',
            'excerpt' => trim(strip_tags((string) $this->input('excerpt', ''))),
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
            'position' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function toDto(): UpdateBlogData
    {
        return new UpdateBlogData(
            id: (int) $this->route('blog'),
            title: $this->string('title')->trim()->toString(),
            content: (string) $this->input('content'),
            excerpt: $this->filled('excerpt') ? $this->string('excerpt')->trim()->toString() : null,
            blogCategoryId: $this->filled('blog_category_id') ? (int) $this->input('blog_category_id') : null,
            position: (int) $this->input('position', 0),
            isFeatured: $this->boolean('is_featured'),
            image: $this->file('image'),
        );
    }
}
