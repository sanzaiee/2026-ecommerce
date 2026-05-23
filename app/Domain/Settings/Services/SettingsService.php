<?php

namespace App\Domain\Settings\Services;

use App\Domain\Settings\DTOs\UpdateSiteSettingsData;
use App\Domain\Settings\Models\SiteSetting;
use App\Domain\Settings\Repositories\SiteSettingRepositoryInterface;
use App\Services\CacheService;
use App\Services\FileUploadService;
use App\Support\ViewData\SiteSettingsMapper;

class SettingsService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private SiteSettingRepositoryInterface $repository,
        private FileUploadService $uploads,
        private CacheService $cache,
        private SiteSettingsMapper $mapper,
    ) {}

    public function getSingleton(): SiteSetting
    {
        return $this->cache->remember('site.settings', self::CACHE_TTL, fn () => $this->repository->getSingleton());
    }

    /**
     * @return array<string, mixed>
     */
    public function getPublic(): array
    {
        return $this->cache->remember(
            'site.settings.public',
            self::CACHE_TTL,
            fn () => $this->mapper->toPublic($this->repository->getSingleton()),
        );
    }

    public function update(UpdateSiteSettingsData $data): SiteSetting
    {
        $settings = $this->repository->getSingleton();
        $attributes = $data->toArray();

        if ($data->privacyPolicy !== null && $data->privacyPolicy !== $settings->privacy_policy) {
            $attributes['privacy_updated_at'] = now();
        }

        if ($data->termsConditions !== null && $data->termsConditions !== $settings->terms_conditions) {
            $attributes['terms_updated_at'] = now();
        }

        if ($data->refundPolicy !== null && $data->refundPolicy !== $settings->refund_policy) {
            $attributes['refund_updated_at'] = now();
        }

        if ($attributes !== []) {
            $settings = $this->repository->update($settings, $attributes);
        }

        $this->uploads->addSingle($settings, 'logo', $data->logo);
        $this->uploads->addSingle($settings, 'favicon', $data->favicon);

        $this->cache->forgetSiteSettings();

        return $this->repository->getSingleton();
    }
}
