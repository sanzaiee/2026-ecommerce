<?php

namespace App\Domain\CMS\Repositories;

use App\Domain\CMS\Models\AboutPage;

class AboutPageRepository implements AboutPageRepositoryInterface
{
    public function getSingleton(): AboutPage
    {
        $page = AboutPage::with('media')->find(1);

        if ($page) {
            return $page;
        }

        return AboutPage::create($this->defaultAttributes());
    }

    public function update(AboutPage $aboutPage, array $attributes): AboutPage
    {
        $aboutPage->update($attributes);

        return $aboutPage->fresh(['media']);
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultAttributes(): array
    {
        return [
            'hero_eyebrow' => 'Our story',
            'story_heading' => 'From Nepal to your table',
            'story_paragraph_1' => 'Our site brings premium dried fruits and traditional Nepali pickles to homes across the country.',
            'story_paragraph_2' => 'Whether you are filling a pantry, packing a lunchbox, or sending a gift, we want every product to feel as thoughtful as something you would share with family.',
            'gallery_heading' => 'Crafted with care',
            'gallery_lead' => 'A glimpse of what we make — simple ingredients, proud Nepali roots.',
            'process_heading' => 'How we work',
            'values' => [
                ['icon' => 'bi-flower1', 'title' => '100% Natural', 'text' => 'No artificial preservatives, colors, or flavors in our core range.'],
                ['icon' => 'bi-droplet-half', 'title' => 'No Added Sugar', 'text' => 'Sun-ripened fruit, slow-dried to keep natural sweetness.'],
                ['icon' => 'bi-geo-alt', 'title' => 'Rooted in Nepal', 'text' => 'Trusted growers and time-honored methods from farm to pack.'],
                ['icon' => 'bi-box-seam', 'title' => 'Packed with Care', 'text' => 'Resealable pouches and careful handling for lasting freshness.'],
            ],
            'process_steps' => [
                ['title' => 'Select', 'text' => 'Hand-picked fruit and spices from partners we know and trust.'],
                ['title' => 'Prepare', 'text' => 'Slow drying and traditional recipes — no shortcuts on taste.'],
                ['title' => 'Pack', 'text' => 'Sealed for freshness and delivered across Nepal with care.'],
            ],
            'cta_title' => 'Taste the difference',
            'cta_text' => 'Explore our dried fruits, pickles, and gift-ready packs — shipped across Nepal.',
        ];
    }
}
