<?php

namespace App\Domain\Blog\Models;

use App\Concerns\HasSingleMediaImage;
use Database\Factories\BlogFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Blog extends Model implements HasMedia
{
    /** @use HasFactory<BlogFactory> */
    use HasFactory;

    use HasSingleMediaImage, InteractsWithMedia {
        HasSingleMediaImage::registerMediaCollections insteadof InteractsWithMedia;
        HasSingleMediaImage::registerMediaConversions insteadof InteractsWithMedia;
    }
    use LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'blog_category_id',
        'content',
        'excerpt',
        'position',
        'is_featured',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'position' => 0,
        'is_featured' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'blog_category_id', 'position', 'is_featured'])
            ->logOnlyDirty();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function summary(int $limit = 160): string
    {
        if ($this->excerpt !== null && trim($this->excerpt) !== '') {
            return trim($this->excerpt);
        }

        return Str::limit(trim(strip_tags((string) $this->content)), $limit);
    }

    protected static function newFactory(): BlogFactory
    {
        return BlogFactory::new();
    }

    protected function singleMediaCollection(): string
    {
        return 'blogs';
    }

    #[Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_featured')
            ->orderByDesc('position')
            ->orderByDesc('created_at');
    }
}
