<?php

namespace App\Domain\Product\Models;

use App\Concerns\HasProductMediaGallery;
use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Review\Models\Review;
use App\Enums\StockStatus;
use Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasProductMediaGallery, InteractsWithMedia {
        HasProductMediaGallery::registerMediaCollections insteadof InteractsWithMedia;
        HasProductMediaGallery::registerMediaConversions insteadof InteractsWithMedia;
    }
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'bullets',
        'additional_info',
        'price',
        'old_price',
        'stock_status',
        'stock_quantity',
        'category_id',
        'brand_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'rating_avg',
        'review_count',
        'is_featured',
        'sort_order',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Product $product) {
            $product->stock_status = $product->stock_quantity > 0 
                ? StockStatus::InStock 
                : StockStatus::OutOfStock;
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'stock_status' => StockStatus::class,
            'bullets' => 'array',
            'additional_info' => 'array',
            'rating_avg' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['title', 'slug', 'price', 'stock_status', 'stock_quantity'])->logOnlyDirty();
    }

    public function featuredImage(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMedia($this->productMediaCollection())->first(),
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved');
    }

    public function isOnSale(): bool
    {
        return $this->stock_status === StockStatus::Sale
            || ($this->old_price !== null && $this->old_price > $this->price);
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function hasStockFor(int $quantity): bool
    {
        return $this->stock_quantity >= $quantity;
    }
}
