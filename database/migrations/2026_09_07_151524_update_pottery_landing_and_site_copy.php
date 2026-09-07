<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align live CMS/site copy with the Thimi pottery brand and SEO targets.
     */
    public function up(): void
    {
        if (Schema::hasTable('landing_pages')) {
            DB::table('landing_pages')->where('id', 1)->update([
                'hero_title' => 'Handmade Newar pottery from Thimi — for home, garden and ritual.',
                'hero_subtitle' => 'Terracotta planting pots, clay water vessels and everyday ware — shaped on the wheel in Madhyapur.',
                'meta_title' => 'JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal',
                'meta_description' => 'Shop handmade terracotta pots, gamala, and clay water filters from Thimi potters. Fired by hand in Madhyapur — shipped across Nepal.',
                'meta_keywords' => 'Thimi pottery, Nepal clay pots, Newar pottery, clay water filter Nepal, terracotta plant pots Kathmandu, Madhyapur pottery',
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('site_settings')) {
            DB::table('site_settings')->where('id', 1)->update([
                'site_name' => 'JheeKuma',
                'brand_suffix' => 'Clay Arts',
                'tagline' => 'Handmade Newar pottery from Thimi, Nepal',
                'promo_primary' => 'Free shipping on orders above Rs. 2,000',
                'promo_secondary' => 'Handmade in Thimi',
                'footer_description' => 'Handmade clay crafts from the potters of Thimi — vessels for water, plants, ritual and daily life.',
                'default_meta_title' => 'JheeKuma Clay Arts | Handmade Pottery from Thimi, Nepal',
                'default_meta_description' => 'Shop handmade terracotta pots, gamala, and clay water filters from Thimi potters. Fired by hand in Madhyapur — shipped across Nepal.',
                'updated_at' => now(),
            ]);
        }

        Cache::forget('landing.page');
        Cache::forget('site.settings');
        Cache::forget('site.settings.public');
    }

    /**
     * Reverse the pottery copy update (previous production-leaning placeholders).
     */
    public function down(): void
    {
        if (Schema::hasTable('landing_pages')) {
            DB::table('landing_pages')->where('id', 1)->update([
                'hero_title' => 'Where Clay Becomes Culture',
                'hero_subtitle' => 'Discover traditional clay pottery shaped by generations of Newar craftsmanship in Thimi, Nepal.',
                'meta_title' => 'Traditional Clay Pottery from Thimi, Nepal — Newar Handmade Pots',
                'meta_description' => 'Handmade Newar pottery from Thimi, Nepal — planting pots, water pots and clay water filters shaped by generations of potters. Shop traditional craft, shipped across Nepal.',
                'meta_keywords' => 'Thimi pottery, Nepal clay pots, Newar pottery, clay water filter Nepal, terracotta plant pots Kathmandu',
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('site_settings')) {
            DB::table('site_settings')->where('id', 1)->update([
                'promo_secondary' => '100% natural products',
                'footer_description' => 'Premium dried fruits and traditional pickles crafted with care. 100% natural ingredients, no artificial additives — straight from Nepal to your table.',
                'updated_at' => now(),
            ]);
        }

        Cache::forget('landing.page');
        Cache::forget('site.settings');
        Cache::forget('site.settings.public');
    }
};
