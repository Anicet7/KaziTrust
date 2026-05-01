<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'email', 
        'is_active', 
        'subscription_plan', 
        'trial_ends_at'
    ];

    /**
     * Génération automatique du slug à partir du nom
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name) . '-' . Str::random(6);
            }
        });
    }

    /**
     * Casts pour formater correctement les dates et les booléens
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    /* -----------------------------------------------------------------
     |  Relations
     | ----------------------------------------------------------------- */

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function apps()
    {
        return $this->hasMany(App::class);
    }

    /* -----------------------------------------------------------------
     |  Méthodes Utilitaires (Business Logic)
     | ----------------------------------------------------------------- */

    /**
     * Vérifie si le tenant est en période d'essai valide
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Vérifie si le tenant a le droit d'utiliser l'API (Actif + Abonnement ou Essai valide)
     */
    public function hasActiveSubscription(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        return $this->subscription_plan !== 'trial' || $this->onTrial();
    }
}