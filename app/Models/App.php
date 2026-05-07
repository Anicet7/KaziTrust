<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\BelongsToTenant; 

/**
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 */

class App extends Model 
{

    use BelongsToTenant;
    
    
    protected $fillable = [
        'tenant_id', 'name', 'uuid', 'is_active', 
        'webhook_url', 'webhook_secret', 
        'llm_provider', 'llm_api_key', 'ai_settings'
    ];

    protected static function boot()
    {
        parent::boot();
        // Génération automatique du UUID à la création
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // PROTECTION CRITIQUE : Encryption des clés API en base de données
    protected function casts(): array
    {
        return [
            'llm_api_key' => 'encrypted', // Chiffré via APP_KEY de Laravel
            'ai_settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function tenant() {
        return $this->belongsTo(Tenant::class);
    }

    public function apiKeys() {
        return $this->hasMany(AppApiKey::class);
    }


    public function trustLogs() { return $this->hasMany(TrustLog::class); }


 }