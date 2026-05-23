<?php

namespace App\Services;

use Illuminate\Support\Str;

class SEOService
{
    public function slugify(string $value): string
    {
        return Str::slug($value);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalizeSeoFields(array $data, string $titleKey = 'title'): array
    {
        $title = $data[$titleKey] ?? $data['name'] ?? '';

        if (empty($data['meta_title']) && $title !== '') {
            $data['meta_title'] = $title;
        }

        if (empty($data['slug']) && $title !== '') {
            $data['slug'] = $this->slugify($title);
        }

        if (! empty($data['meta_keywords']) && is_string($data['meta_keywords'])) {
            $data['meta_keywords'] = implode(', ', array_filter(array_map(
                'trim',
                explode(',', $data['meta_keywords'])
            )));
        }

        return $data;
    }
}
