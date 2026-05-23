<?php

namespace App\Domain\Settings\Repositories;

use App\Domain\Settings\Models\SiteSetting;

interface SiteSettingRepositoryInterface
{
    public function getSingleton(): SiteSetting;

    public function update(SiteSetting $settings, array $attributes): SiteSetting;
}
