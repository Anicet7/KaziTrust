<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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