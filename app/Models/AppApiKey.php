<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;


class AppApiKey extends Model
{
    protected $fillable = ['app_id', 'name', 'key', 'is_active', 'last_used_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->key = 'kz_' . Str::random(32);
        });
    }

    public function app() {
        return $this->belongsTo(App::class);
    }
}