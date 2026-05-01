<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * @method static void addGlobalScope(string $identifier, \Closure $scope)
 * @method static void creating(\Closure $callback)
 */
trait BelongsToTenant
{
    public static function bootBelongsToTenant()
    {
        // Use the Auth facade for better IDE support and clarity
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->tenant_id) {
                static::addGlobalScope('tenant_id', function (Builder $builder) use ($user) {
                    $builder->where('tenant_id', $user->tenant_id);
                });

                static::creating(function ($model) use ($user) {
                    $model->tenant_id = $user->tenant_id;
                });
            }
        }
    }
}