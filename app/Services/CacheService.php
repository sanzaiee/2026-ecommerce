<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }

    public function forgetProduct(?string $slug = null): void
    {
        if ($slug) {
            $this->forget("products.{$slug}");
        }

        $this->forgetByPrefix('products.list.');
    }

    public function forgetCategory(?string $slug = null): void
    {
        if ($slug) {
            $this->forget("categories.{$slug}");
        }

        $this->forget('categories.all');
        $this->forgetByPrefix('products.list.');
    }

    public function forgetTestimonials(): void
    {
        $this->forget('testimonials.home');
    }

    public function forgetBlogs(?string $slug = null): void
    {
        if ($slug) {
            $this->forget('blogs.'.$slug);
        }
    }

    public function forgetLanding(): void
    {
        $this->forget('landing.page');
    }

    public function forgetAbout(): void
    {
        $this->forget('about.page');
    }

    public function forgetSiteSettings(): void
    {
        $this->forget('site.settings');
        $this->forget('site.settings.public');
    }

    public function forgetAllCatalog(): void
    {
        $this->forgetLanding();
        $this->forgetAbout();
        $this->forgetCategory();
        $this->forgetProduct();
    }

    private function forgetByPrefix(string $prefix): void
    {
        // Database/file cache drivers do not support tagless prefix flush;
        // landing and product detail keys are explicitly cleared on writes.
        $this->forget('products.list.default');
    }
}
