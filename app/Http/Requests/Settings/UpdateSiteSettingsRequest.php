<?php

namespace App\Http\Requests\Settings;

use App\Domain\Settings\DTOs\UpdateSiteSettingsData;
use App\Services\HtmlSanitizer;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $sanitizer = app(HtmlSanitizer::class);

        foreach ([
            'site_name', 'brand_suffix', 'tagline', 'promo_primary', 'promo_secondary', 'copyright_text',
            'contact_email', 'contact_phone', 'hours_display_text',
            'default_meta_title', 'default_meta_description',
        ] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => strip_tags((string) $this->input($field))]);
            }
        }

        foreach (['promo_primary', 'promo_secondary'] as $field) {
            if ($this->has($field)) {
                $value = trim(strip_tags((string) $this->input($field)));
                $this->merge([$field => $value !== '' ? $value : null]);
            }
        }

        foreach (['theme_primary', 'theme_primary_dark', 'theme_hero_accent'] as $field) {
            if ($this->has($field)) {
                $value = trim(strip_tags((string) $this->input($field)));
                if ($value === '') {
                    $this->merge([$field => null]);

                    continue;
                }
                if (! str_starts_with($value, '#')) {
                    $value = '#'.$value;
                }
                $this->merge([$field => strtoupper($value)]);
            }
        }

        foreach (['footer_description', 'contact_address'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => trim((string) $this->input($field)) ?: null]);
            }
        }

        foreach (['facebook_url', 'instagram_url', 'youtube_url', 'tiktok_url'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $this->merge([$field => $value !== '' ? $value : null]);
            }
        }

        foreach (['privacy_policy', 'terms_conditions', 'refund_policy'] as $field) {
            if ($this->has($field)) {
                $this->merge([$field => $sanitizer->clean((string) $this->input($field))]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:100'],
            'brand_suffix' => ['nullable', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'theme_primary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_primary_dark' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_hero_accent' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'promo_primary' => ['nullable', 'string', 'max:255'],
            'promo_secondary' => ['nullable', 'string', 'max:255'],
            'footer_description' => ['nullable', 'string', 'max:2000'],
            'copyright_text' => ['nullable', 'string', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'hours_display_text' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
            'tiktok_url' => ['nullable', 'url', 'max:500'],
            'privacy_policy' => ['nullable', 'string', 'max:65535'],
            'terms_conditions' => ['nullable', 'string', 'max:65535'],
            'refund_policy' => ['nullable', 'string', 'max:65535'],
            'default_meta_title' => ['nullable', 'string', 'max:255'],
            'default_meta_description' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:3048'],
            'favicon' => ['nullable', 'image', 'max:512', 'dimensions:max_width=512,max_height=512'],
        ];
    }

    public function toDto(): UpdateSiteSettingsData
    {
        return new UpdateSiteSettingsData(
            siteName: $this->input('site_name'),
            brandSuffix: $this->input('brand_suffix'),
            tagline: $this->input('tagline'),
            themePrimary: $this->input('theme_primary'),
            themePrimaryDark: $this->input('theme_primary_dark'),
            themeHeroAccent: $this->input('theme_hero_accent'),
            promoPrimary: $this->input('promo_primary'),
            promoSecondary: $this->input('promo_secondary'),
            footerDescription: $this->input('footer_description'),
            copyrightText: $this->input('copyright_text'),
            contactEmail: $this->input('contact_email'),
            contactPhone: $this->input('contact_phone'),
            contactAddress: $this->input('contact_address'),
            hoursDisplayText: $this->input('hours_display_text'),
            facebookUrl: $this->input('facebook_url'),
            instagramUrl: $this->input('instagram_url'),
            youtubeUrl: $this->input('youtube_url'),
            tiktokUrl: $this->input('tiktok_url'),
            privacyPolicy: $this->input('privacy_policy'),
            termsConditions: $this->input('terms_conditions'),
            refundPolicy: $this->input('refund_policy'),
            defaultMetaTitle: $this->input('default_meta_title'),
            defaultMetaDescription: $this->input('default_meta_description'),
            logo: $this->file('logo'),
            favicon: $this->file('favicon'),
        );
    }
}
