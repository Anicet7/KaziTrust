<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder whereIn($column, $values, $boolean = 'and', $not = false)
 */


class Payment extends Model
{
    protected $fillable = [
        'subscription_id', 'tenant_id',
        'amount', 'currency', 'status',
        'provider', 'provider_transaction_id', 'provider_response',
        'description', 'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'            => 'decimal:2',
            'provider_response' => 'array',
            'paid_at'           => 'datetime',
        ];
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}