<?php

namespace App\Domain\Settings\Repositories;

use App\Domain\Settings\Models\SiteSetting;

class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    public function getSingleton(): SiteSetting
    {
        $settings = SiteSetting::with('media')->find(1);

        if ($settings) {
            return $settings;
        }

        return SiteSetting::create([
            'site_name' => 'JheeKuma',
            'brand_suffix' => 'Clay Arts',
            'tagline' => 'Handmade Newar pottery from Thimi, Nepal',
            'theme_primary' => config('store.theme.primary'),
            'theme_primary_dark' => config('store.theme.primary_dark'),
            'theme_hero_accent' => config('store.theme.hero_accent'),
            'admin_theme' => config('store.admin.theme'),
            'admin_color_primary' => config('store.admin.colors.primary'),
            'admin_color_secondary' => config('store.admin.colors.secondary'),
            'admin_color_neutral' => config('store.admin.colors.neutral'),
            'promo_primary' => 'Free shipping on orders above Rs. 2,000',
            'promo_secondary' => 'Handmade in Thimi',
            'footer_description' => 'Handmade clay crafts from the potters of Thimi — vessels for water, plants, ritual and daily life.',
            'default_meta_title' => 'JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal',
            'default_meta_description' => 'Shop handmade terracotta pots, gamala, and clay water filters from Thimi potters. Fired by hand in Madhyapur — shipped across Nepal.',
            'contact_email' => 'hello@jheekuma.com',
            'contact_phone' => '+977 1-XXXXXXX',
            'contact_address' => 'Thimi, Madhyapur, Nepal',
            'hours_display_text' => 'Sun–Fri, 10:00 AM – 6:00 PM',
        ]);
    }

    public function update(SiteSetting $settings, array $attributes): SiteSetting
    {
        $settings->update($attributes);

        return $settings->fresh('media');
    }
}
