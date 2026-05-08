<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 */

class TrustLog extends Model
{

 // ✅ FIX : tous les champs écrits par TrustController::analyze()
    protected $fillable = [
        'app_id',
        'phone_number',
        'nokia_payload',
        'ai_provider',
        'ai_response',
        'token_count',
        'latency_ms',
        'cost_estimate',
    ];
    
    protected $casts = [
        'nokia_payload' => 'array',
        'ai_response' => 'array',
        'cost_estimate' => 'decimal:6',
    ];

    public function app() {
        return $this->belongsTo(App::class);
    }
}