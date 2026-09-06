<?php

namespace App\Domain\Blog\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'sort_order' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderByDesc('sort_order')->orderBy('name');
    }
}
