<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 */

class TrustLog extends Model
{
    protected $casts = [
        'nokia_payload' => 'array',
        'ai_response' => 'array',
        'cost_estimate' => 'decimal:6',
    ];

    public function app() {
        return $this->belongsTo(App::class);
    }
}