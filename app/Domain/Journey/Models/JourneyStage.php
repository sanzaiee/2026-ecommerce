<?php

namespace App\Domain\Journey\Models;

use App\Concerns\HasSingleMediaImage;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JourneyStage extends Model implements HasMedia
{
    use HasSingleMediaImage, InteractsWithMedia {
        HasSingleMediaImage::registerMediaCollections insteadof InteractsWithMedia;
        HasSingleMediaImage::registerMediaConversions insteadof InteractsWithMedia;
    }
    use LogsActivity;

    protected $fillable = [
        'product_id',
        'title',
        'text',
        'sort_order',
        'is_published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'title', 'sort_order', 'is_published'])
            ->logOnlyDirty();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function singleMediaCollection(): string
    {
        return 'journey_stages';
    }
}
