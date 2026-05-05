<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder orderBy($column, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder public()
 */

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'price_monthly', 'price_yearly', 'currency',
        'max_apps', 'max_api_keys_per_app', 'max_requests_per_month', 'max_users',
        'features', 'is_active', 'is_public', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features'   => 'array',
            'is_active'  => 'boolean',
            'is_public'  => 'boolean',
            'price_monthly' => 'decimal:2',
            'price_yearly'  => 'decimal:2',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Vérifie si une feature est activée sur ce plan
     */
    public function hasFeature(string $feature): bool
    {
        return (bool) ($this->features[$feature] ?? false);
    }

    /**
     * Vérifie si une limite est illimitée (-1)
     */
    public function isUnlimited(string $limitField): bool
    {
        return $this->{$limitField} === -1;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)->orderBy('sort_order');
    }
}