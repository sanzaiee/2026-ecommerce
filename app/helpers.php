<?php

use App\Domain\Settings\Repositories\SiteSettingRepository;

if (! function_exists('get_placeholder_image')) {
    /**
     * Default image URL when a product or media asset has no upload.
     */
    function get_placeholder_image(): string
    {
        $url = config('app.placeholder_image', '/img/placeholder.jpg');

        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return asset($url);
        }

        return $url;
    }
}

if (! function_exists('get_site_name')) {
    /**
     * Get the site name from the configuration.
     */
    function get_site_name(): string
    {
        $site = app(SiteSettingRepository::class)->getSingleton();

        return $site?->site_name ?? config('app.name');
    }
}
