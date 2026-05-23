<?php

namespace App\Domain\Category\Models;

use App\Concerns\HasSingleMediaImage;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasSingleMediaImage, InteractsWithMedia {
        HasSingleMediaImage::registerMediaCollections insteadof InteractsWithMedia;
        HasSingleMediaImage::registerMediaConversions insteadof InteractsWithMedia;
    }
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sort_order',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'slug'])->logOnlyDirty();
    }

    protected function singleMediaCollection(): string
    {
        return 'categories';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
