<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SubscriptionWidget extends Widget
{
    protected static string  $view = 'filament.widgets.subscription';
    protected static ?int    $sort = 0;
   // protected int | string   $columnSpan = 'full';

    public function getViewData(): array
    {
        $tenant = Auth::user()->tenant;
        $sub    = $tenant->activeSubscription;
        $plan   = $sub?->plan;

        $appIds  = $tenant->apps()->pluck('id');
        
        $used    = \App\Models\TrustLog::whereIn('app_id', $appIds)
            ->whereMonth('created_at', now()->month)->count();


        $limit   = $plan?->max_requests_per_month ?? 100;
        $percent = $limit === -1 ? 0 : min(100, round($used / max($limit, 1) * 100));

        return compact('tenant', 'sub', 'plan', 'used', 'limit', 'percent');
    }
}