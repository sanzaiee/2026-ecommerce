<?php

namespace App\Domain\Settings\DTOs;

use Illuminate\Http\UploadedFile;

readonly class UpdateSiteSettingsData
{
    public function __construct(
        public ?string $siteName = null,
        public ?string $brandSuffix = null,
        public ?string $tagline = null,
        public ?string $themePrimary = null,
        public ?string $themePrimaryDark = null,
        public ?string $themeHeroAccent = null,
        public ?string $promoPrimary = null,
        public ?string $promoSecondary = null,
        public ?string $footerDescription = null,
        public ?string $copyrightText = null,
        public ?string $contactEmail = null,
        public ?string $contactPhone = null,
        public ?string $contactAddress = null,
        public ?string $hoursDisplayText = null,
        public ?string $facebookUrl = null,
        public ?string $instagramUrl = null,
        public ?string $youtubeUrl = null,
        public ?string $tiktokUrl = null,
        public ?string $privacyPolicy = null,
        public ?string $termsConditions = null,
        public ?string $refundPolicy = null,
        public ?string $defaultMetaTitle = null,
        public ?string $defaultMetaDescription = null,
        public ?UploadedFile $logo = null,
        public ?UploadedFile $favicon = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = array_filter([
            'site_name' => $this->siteName,
            'brand_suffix' => $this->brandSuffix,
            'tagline' => $this->tagline,
            'theme_primary' => $this->themePrimary,
            'theme_primary_dark' => $this->themePrimaryDark,
            'theme_hero_accent' => $this->themeHeroAccent,
            'promo_primary' => $this->promoPrimary,
            'promo_secondary' => $this->promoSecondary,
            'footer_description' => $this->footerDescription,
            'copyright_text' => $this->copyrightText,
            'contact_email' => $this->contactEmail,
            'contact_phone' => $this->contactPhone,
            'contact_address' => $this->contactAddress,
            'hours_display_text' => $this->hoursDisplayText,
            'facebook_url' => $this->facebookUrl,
            'instagram_url' => $this->instagramUrl,
            'youtube_url' => $this->youtubeUrl,
            'tiktok_url' => $this->tiktokUrl,
            'privacy_policy' => $this->privacyPolicy,
            'terms_conditions' => $this->termsConditions,
            'refund_policy' => $this->refundPolicy,
            'default_meta_title' => $this->defaultMetaTitle,
            'default_meta_description' => $this->defaultMetaDescription,
        ], fn ($v) => $v !== null);

        return $data;
    }
}
