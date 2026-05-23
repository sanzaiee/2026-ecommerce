<?php

namespace App\Domain\CMS\Models;

use App\Domain\Category\Models\Category;
use App\Domain\Product\Models\Product;
use App\Enums\LandingProductSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LandingPage extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cms')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('large')->width(1200)->height(800);
    }

    public function featuredCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'landing_page_category')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function featuredProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'landing_page_product')
            ->withPivot(['section', 'sort_order'])
            ->orderByPivot('sort_order');
    }

    public function productsForSection(LandingProductSection $section): BelongsToMany
    {
        return $this->featuredProducts()->wherePivot('section', $section->value);
    }
}
