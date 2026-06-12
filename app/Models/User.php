<?php

namespace App\Models;

use App\Domain\Order\Models\Order;
use App\Domain\Review\Models\Review;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_district',
        'shipping_postal_code',
        'is_banned',
        'banned_at',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'total_spent',
        'avg_rating',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isBanned(): bool
    {
        return $this->is_banned === true;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getTotalSpentAttribute(): float
    {
        return (float) $this->orders()
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->avg('rating') ?: 0;
    }
}
