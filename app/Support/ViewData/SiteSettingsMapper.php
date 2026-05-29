<?php

namespace App\Support\ViewData;

use App\Domain\Settings\Models\SiteSetting;

class SiteSettingsMapper
{
    /**
     * @return array<string, mixed>
     */
    public function toPublic(SiteSetting $settings): array
    {
        return [
            'siteName' => $settings->site_name,
            'brandSuffix' => $settings->brand_suffix,
            'tagline' => $settings->tagline,
            'theme' => [
                'primary' => $this->themeColor($settings->theme_primary, 'primary'),
                'primaryDark' => $this->themeColor($settings->theme_primary_dark, 'primary_dark'),
                'heroAccent' => $this->themeColor($settings->theme_hero_accent, 'hero_accent'),
            ],
            'adminTheme' => $this->adminTheme($settings),
            'adminColors' => $this->adminColors($settings),
            'promoPrimary' => $settings->promo_primary,
            'promoSecondary' => $settings->promo_secondary,
            'footerDescription' => $settings->footer_description,
            'copyright' => $settings->copyright_text
                ?: '&copy; ' . date('Y') . ' ' . $settings->site_name . ' ' . $settings->brand_suffix . '. All rights reserved.',
            'logoUrl' => $settings->getFirstMediaUrl('logo', 'header')
                ?: $settings->getFirstMediaUrl('logo')
                ?: null,
            'faviconUrl' => $settings->getFirstMediaUrl('favicon') ?: null,
            'defaultMetaTitle' => $settings->default_meta_title,
            'defaultMetaDescription' => $settings->default_meta_description,
            'contactInfo' => $this->contactInfo($settings),
            'socialLinks' => $this->socialLinks($settings),
            'privacyBody' => $settings->privacy_policy,
            'termsBody' => $settings->terms_conditions,
            'refundBody' => $settings->refund_policy,
            'privacyUpdated' => $settings->privacy_updated_at?->format('M j, Y'),
            'termsUpdated' => $settings->terms_updated_at?->format('M j, Y'),
            'refundUpdated' => $settings->refund_updated_at?->format('M j, Y'),
        ];
    }

    /**
     * @return array<int, array{icon: string, label: string, value: string, href: string|null}>
     */
    public function contactInfo(SiteSetting $settings): array
    {
        $items = [];

        if ($settings->contact_email) {
            $items[] = [
                'icon' => 'bi-envelope',
                'label' => 'Email',
                'value' => $settings->contact_email,
                'href' => 'mailto:' . $settings->contact_email,
            ];
        }

        if ($settings->contact_phone) {
            $phoneHref = 'tel:' . preg_replace('/\s+/', '', $settings->contact_phone);
            $items[] = [
                'icon' => 'bi-telephone',
                'label' => 'Phone',
                'value' => $settings->contact_phone,
                'href' => $phoneHref,
            ];
        }

        if ($settings->contact_address) {
            $items[] = [
                'icon' => 'bi-geo-alt',
                'label' => 'Address',
                'value' => $settings->contact_address,
                'href' => null,
            ];
        }

        if ($settings->hours_display_text) {
            $items[] = [
                'icon' => 'bi-clock',
                'label' => 'Hours',
                'value' => $settings->hours_display_text,
                'href' => null,
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array{label: string, href: string, icon: string}>
     */
    public function socialLinks(SiteSetting $settings): array
    {
        $links = [];

        $map = [
            ['field' => 'facebook_url', 'label' => 'Facebook', 'icon' => 'bi-facebook'],
            ['field' => 'instagram_url', 'label' => 'Instagram', 'icon' => 'bi-instagram'],
            ['field' => 'youtube_url', 'label' => 'YouTube', 'icon' => 'bi-youtube'],
            ['field' => 'tiktok_url', 'label' => 'TikTok', 'icon' => 'bi-tiktok'],
        ];

        foreach ($map as $social) {
            $url = $settings->{$social['field']};
            if ($url) {
                $links[] = [
                    'label' => $social['label'],
                    'href' => $url,
                    'icon' => $social['icon'],
                ];
            }
        }

        return $links;
    }

    private function themeColor(?string $value, string $key): string
    {
        if ($value && preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
            return strtoupper($value);
        }

        return strtoupper((string) config("store.theme.{$key}", '#b91c1c'));
    }

    private function adminTheme(SiteSetting $settings): string
    {
        $value = $settings->admin_theme;

        if (in_array($value, ['light', 'dark', 'system'], true)) {
            return $value;
        }

        $default = config('store.admin.theme', 'light');

        return in_array($default, ['light', 'dark', 'system'], true) ? $default : 'light';
    }

    /**
     * @return array{primary: string, secondary: string, neutral: string}
     */
    private function adminColors(SiteSetting $settings): array
    {
        return [
            'primary' => $this->adminColor($settings->admin_color_primary, 'primary'),
            'secondary' => $this->adminColor($settings->admin_color_secondary, 'secondary'),
            'neutral' => $this->adminColor($settings->admin_color_neutral, 'neutral'),
        ];
    }

    private function adminColor(?string $value, string $key): string
    {
        if ($value && preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
            return strtoupper($value);
        }

        return strtoupper((string) config("store.admin.colors.{$key}", '#3D2914'));
    }
}
