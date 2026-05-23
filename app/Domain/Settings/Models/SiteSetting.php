<?php

namespace App\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'site_name',
        'brand_suffix',
        'tagline',
        'theme_primary',
        'theme_primary_dark',
        'theme_hero_accent',
        'promo_primary',
        'promo_secondary',
        'footer_description',
        'copyright_text',
        'contact_email',
        'contact_phone',
        'contact_address',
        'hours_display_text',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'privacy_policy',
        'terms_conditions',
        'refund_policy',
        'about_content',
        'privacy_updated_at',
        'terms_updated_at',
        'refund_updated_at',
        'about_updated_at',
        'default_meta_title',
        'default_meta_description',
    ];

    protected function casts(): array
    {
        return [
            'privacy_updated_at' => 'datetime',
            'terms_updated_at' => 'datetime',
            'refund_updated_at' => 'datetime',
            'about_updated_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(120)
            ->height(120)
            ->performOnCollections('logo');
    }
}
