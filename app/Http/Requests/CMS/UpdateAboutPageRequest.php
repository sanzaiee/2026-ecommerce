<?php

namespace App\Http\Requests\CMS;

use App\Domain\CMS\DTOs\UpdateAboutPageData;
use App\Services\HtmlSanitizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UpdateAboutPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $sanitizer = app(HtmlSanitizer::class);

        foreach ([
            'hero_eyebrow', 'hero_lead', 'story_heading', 'gallery_heading', 'gallery_lead',
            'process_heading', 'cta_title', 'meta_title', 'meta_description',
        ] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => strip_tags((string) $this->input($field))]);
            }
        }

        foreach (['story_paragraph_1', 'story_paragraph_2', 'cta_text'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim((string) $this->input($field)) ?: null]);
            }
        }

        if ($this->has('editorial_body')) {
            $this->merge(['editorial_body' => $sanitizer->clean((string) $this->input('editorial_body'))]);
        }

        if ($this->has('values')) {
            $this->merge(['values' => $this->normalizeValues()]);
        }

        if ($this->has('process_steps')) {
            $this->merge(['process_steps' => $this->normalizeProcessSteps()]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'hero_eyebrow' => ['nullable', 'string', 'max:120'],
            'hero_lead' => ['nullable', 'string', 'max:500'],
            'story_heading' => ['nullable', 'string', 'max:255'],
            'story_paragraph_1' => ['nullable', 'string', 'max:5000'],
            'story_paragraph_2' => ['nullable', 'string', 'max:5000'],
            'gallery_heading' => ['nullable', 'string', 'max:255'],
            'gallery_lead' => ['nullable', 'string', 'max:500'],
            'process_heading' => ['nullable', 'string', 'max:255'],
            'editorial_body' => ['nullable', 'string', 'max:65535'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'hero_image' => ['nullable', 'image', 'max:5120'],
            'story_image' => ['nullable', 'image', 'max:5120'],
            'values' => ['nullable', 'array', 'size:4'],
            'values.*.icon' => ['required', 'string', 'max:80'],
            'values.*.title' => ['required', 'string', 'max:120'],
            'values.*.text' => ['required', 'string', 'max:500'],
            'process_steps' => ['nullable', 'array', 'size:3'],
            'process_steps.*.title' => ['required', 'string', 'max:120'],
            'process_steps.*.text' => ['required', 'string', 'max:500'],
        ];

        for ($i = 0; $i < 3; $i++) {
            $rules["gallery_images.{$i}"] = ['nullable', 'image', 'max:5120'];
            $rules["gallery_alts.{$i}"] = ['nullable', 'string', 'max:255'];
            $rules["gallery_captions.{$i}"] = ['nullable', 'string', 'max:255'];
        }

        for ($i = 0; $i < 2; $i++) {
            $rules["craft_images.{$i}"] = ['nullable', 'image', 'max:5120'];
            $rules["craft_alts.{$i}"] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    public function toDto(): UpdateAboutPageData
    {
        return new UpdateAboutPageData(
            heroEyebrow: $this->input('hero_eyebrow'),
            heroLead: $this->input('hero_lead'),
            storyHeading: $this->input('story_heading'),
            storyParagraph1: $this->input('story_paragraph_1'),
            storyParagraph2: $this->input('story_paragraph_2'),
            galleryHeading: $this->input('gallery_heading'),
            galleryLead: $this->input('gallery_lead'),
            processHeading: $this->input('process_heading'),
            values: $this->input('values'),
            processSteps: $this->input('process_steps'),
            editorialBody: $this->input('editorial_body'),
            ctaTitle: $this->input('cta_title'),
            ctaText: $this->input('cta_text'),
            metaTitle: $this->input('meta_title'),
            metaDescription: $this->input('meta_description'),
            heroImage: $this->file('hero_image'),
            storyImage: $this->file('story_image'),
            galleryImages: $this->galleryFiles(),
            galleryMeta: $this->galleryMeta(),
            craftImages: $this->craftFiles(),
            craftMeta: $this->craftMeta(),
        );
    }

    /**
     * @return array<int, array{icon: string, title: string, text: string}>
     */
    private function normalizeValues(): array
    {
        $values = [];
        foreach ((array) $this->input('values', []) as $row) {
            $icon = trim(strip_tags((string) ($row['icon'] ?? '')));
            $title = trim(strip_tags((string) ($row['title'] ?? '')));
            $text = trim(strip_tags((string) ($row['text'] ?? '')));
            if ($title === '' || $text === '') {
                continue;
            }
            $values[] = [
                'icon' => $icon !== '' ? $icon : 'bi-star',
                'title' => $title,
                'text' => $text,
            ];
        }

        return $values;
    }

    /**
     * @return array<int, array{title: string, text: string}>
     */
    private function normalizeProcessSteps(): array
    {
        $steps = [];
        foreach ((array) $this->input('process_steps', []) as $row) {
            $title = trim(strip_tags((string) ($row['title'] ?? '')));
            $text = trim(strip_tags((string) ($row['text'] ?? '')));
            if ($title === '' || $text === '') {
                continue;
            }
            $steps[] = ['title' => $title, 'text' => $text];
        }

        return $steps;
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function galleryFiles(): array
    {
        $files = [];
        for ($i = 0; $i < 3; $i++) {
            $file = $this->file("gallery_images.{$i}");
            if ($file instanceof UploadedFile) {
                $files[$i] = $file;
            }
        }

        return $files;
    }

    /**
     * @return array<int, array{alt: ?string, caption: ?string}>
     */
    private function galleryMeta(): array
    {
        $meta = [];
        for ($i = 0; $i < 3; $i++) {
            $meta[] = [
                'alt' => strip_tags((string) $this->input("gallery_alts.{$i}", '')) ?: null,
                'caption' => strip_tags((string) $this->input("gallery_captions.{$i}", '')) ?: null,
            ];
        }

        return $meta;
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function craftFiles(): array
    {
        $files = [];
        for ($i = 0; $i < 2; $i++) {
            $file = $this->file("craft_images.{$i}");
            if ($file instanceof UploadedFile) {
                $files[$i] = $file;
            }
        }

        return $files;
    }

    /**
     * @return array<int, array{alt: ?string}>
     */
    private function craftMeta(): array
    {
        $meta = [];
        for ($i = 0; $i < 2; $i++) {
            $meta[] = [
                'alt' => strip_tags((string) $this->input("craft_alts.{$i}", '')) ?: null,
            ];
        }

        return $meta;
    }
}
