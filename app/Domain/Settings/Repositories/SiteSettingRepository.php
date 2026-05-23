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
            'site_name' => 'Our Site Name',
            'brand_suffix' => 'Foods',
            'tagline' => 'Premium dried fruits and traditional pickles',
            'theme_primary' => config('store.theme.primary'),
            'theme_primary_dark' => config('store.theme.primary_dark'),
            'theme_hero_accent' => config('store.theme.hero_accent'),
            'promo_primary' => 'Free shipping on orders above Rs. 2,000',
            'promo_secondary' => '100% natural products',
            'footer_description' => 'Premium dried fruits and traditional pickles crafted with care. 100% natural ingredients, no artificial additives — straight from Nepal to your table.',
            'contact_email' => 'support@mandirafoods.com',
            'contact_phone' => '+977 1-XXXXXXX',
            'contact_address' => 'Baluwatar, Kathmandu, Nepal',
            'hours_display_text' => 'Sun–Fri, 10:00 AM – 6:00 PM',
        ]);
    }

    public function update(SiteSetting $settings, array $attributes): SiteSetting
    {
        $settings->update($attributes);

        return $settings->fresh('media');
    }
}
