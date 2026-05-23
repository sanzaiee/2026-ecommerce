<?php

namespace App\Support\ViewData;

use App\Domain\CMS\Models\AboutPage;

class AboutPageMapper
{
    /**
     * @return array<string, mixed>
     */
    public function toAboutView(AboutPage $page, array $site): array
    {
        $siteName = $site['siteName'] ?? 'Mandira';
        $brandSuffix = $site['brandSuffix'] ?? 'Foods';
        $brandName = trim($siteName . ' ' . $brandSuffix);

        return [
            'pageTitle' => 'About Us',
            'brandName' => $brandName,
            'heroEyebrow' => $page->hero_eyebrow ?: 'Our story',
            'tagline' => $page->hero_lead ?: ($site['tagline'] ?? 'Premium dried fruits and traditional pickles — crafted in Nepal.'),
            'lastUpdated' => $page->updated_at?->format('M j, Y'),
            'values' => $this->values($page),
            'storyHeading' => $page->story_heading ?: 'From Nepal to your table',
            'storyParagraph1' => $page->story_paragraph_1,
            'storyParagraph2' => $page->story_paragraph_2,
            'galleryHeading' => $page->gallery_heading ?: 'Crafted with care',
            'galleryLead' => $page->gallery_lead ?: 'A glimpse of what we make — simple ingredients, proud Nepali roots.',
            'processHeading' => $page->process_heading ?: 'How we work',
            'processSteps' => $this->processSteps($page),
            'aboutBody' => $page->editorial_body,
            'ctaTitle' => $page->cta_title ?: 'Taste the difference',
            'ctaText' => $page->cta_text ?: 'Explore our dried fruits, pickles, and gift-ready packs — shipped across Nepal.',
            'images' => [
                'hero' => $page->getFirstMediaUrl('hero', 'large')
                    ?: 'https://images.unsplash.com/photo-1608797178974-15b35a8edeaa?w=1600&q=80',
                'story' => $page->getFirstMediaUrl('story', 'large')
                    ?: 'https://images.unsplash.com/photo-1599599810769-0a29d5affa8e?w=900&q=80',
                'gallery' => $this->galleryImages($page),
                'craft' => $this->craftImages($page),
            ],
        ];
    }

    /**
     * @return array<int, array{icon: string, title: string, text: string}>
     */
    private function values(AboutPage $page): array
    {
        $values = $page->values;

        if (is_array($values) && $values !== []) {
            return $values;
        }

        return [
            ['icon' => 'bi-flower1', 'title' => '100% Natural', 'text' => 'No artificial preservatives, colors, or flavors in our core range.'],
            ['icon' => 'bi-droplet-half', 'title' => 'No Added Sugar', 'text' => 'Sun-ripened fruit, slow-dried to keep natural sweetness.'],
            ['icon' => 'bi-geo-alt', 'title' => 'Rooted in Nepal', 'text' => 'Trusted growers and time-honored methods from farm to pack.'],
            ['icon' => 'bi-box-seam', 'title' => 'Packed with Care', 'text' => 'Resealable pouches and careful handling for lasting freshness.'],
        ];
    }

    /**
     * @return array<int, array{title: string, text: string}>
     */
    private function processSteps(AboutPage $page): array
    {
        $steps = $page->process_steps;

        if (is_array($steps) && $steps !== []) {
            return $steps;
        }

        return [
            ['title' => 'Select', 'text' => 'Hand-picked fruit and spices from partners we know and trust.'],
            ['title' => 'Prepare', 'text' => 'Slow drying and traditional recipes — no shortcuts on taste.'],
            ['title' => 'Pack', 'text' => 'Sealed for freshness and delivered across Nepal with care.'],
        ];
    }

    /**
     * @return array<int, array{src: string, alt: string, caption: string}>
     */
    private function galleryImages(AboutPage $page): array
    {
        $defaults = [
            [
                'src' => 'https://images.unsplash.com/photo-1606313564200-e75d5e304d0e?w=700&q=80',
                'alt' => 'Assorted dried fruits in bowls',
                'caption' => 'Sun-dried fruits',
            ],
            [
                'src' => 'https://images.unsplash.com/photo-1563560360-5154e7376b5f?w=700&q=80',
                'alt' => 'Traditional pickles and preserves',
                'caption' => 'Traditional pickles',
            ],
            [
                'src' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=700&q=80',
                'alt' => 'Fresh produce at a local market',
                'caption' => 'Sourced with care',
            ],
        ];

        $items = [];
        for ($slot = 0; $slot < 3; $slot++) {
            $media = $page->mediaForSlot('gallery', $slot);
            $fallback = $defaults[$slot];

            if (! $media) {
                $items[] = $fallback;

                continue;
            }

            $items[] = [
                'src' => $page->mediaUrl($media),
                'alt' => (string) ($media->getCustomProperty('alt') ?: $fallback['alt']),
                'caption' => (string) ($media->getCustomProperty('caption') ?: $fallback['caption']),
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array{src: string, alt: string}>
     */
    private function craftImages(AboutPage $page): array
    {
        $defaults = [
            [
                'src' => 'https://images.unsplash.com/photo-1498837167922-ddd27525cd40?w=800&q=80',
                'alt' => 'Fresh ingredients prepared for drying',
            ],
            [
                'src' => 'https://images.unsplash.com/photo-1490474508879-786aee03d498?w=800&q=80',
                'alt' => 'Natural ingredients on a wooden board',
            ],
        ];

        $items = [];
        for ($slot = 0; $slot < 2; $slot++) {
            $media = $page->mediaForSlot('craft', $slot);
            $fallback = $defaults[$slot];

            if (! $media) {
                $items[] = $fallback;

                continue;
            }

            $items[] = [
                'src' => $page->mediaUrl($media),
                'alt' => (string) ($media->getCustomProperty('alt') ?: $fallback['alt']),
            ];
        }

        return $items;
    }
}
