<?php

namespace App\Concerns;

trait ProvidesRelativeMediaUrls
{
    /**
     * Strip the app host so images work regardless of how the site is accessed
     * (127.0.0.1 vs localhost, custom port, etc.).
     */
    protected function relativeMediaUrl(string $url): string
    {
        if ($url === '') {
            return '';
        }

        $path = parse_url($url, PHP_URL_PATH);

        return is_string($path) && $path !== '' ? $path : $url;
    }
}
