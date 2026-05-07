<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 */

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id', 'plan_id', 'status',
        'trial_ends_at', 'starts_at', 'ends_at', 'cancelled_at',
        'payment_provider', 'payment_provider_id',
        'price_paid', 'currency', 'billing_cycle', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'cancelled_at'  => 'datetime',
            'price_paid'    => 'decimal:2',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /* -----------------------------------------------------------------
     |  Helpers de statut
     | ----------------------------------------------------------------- */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->status === 'trial' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return in_array($this->status, ['expired', 'cancelled']) ||
               ($this->ends_at && $this->ends_at->isPast() && $this->status !== 'active');
    }

    /**
     * Peut utiliser l'API ? (actif OU essai valide)
     */
    public function canUseApi(): bool
    {
        return $this->isActive() || $this->isOnTrial();
    }
}