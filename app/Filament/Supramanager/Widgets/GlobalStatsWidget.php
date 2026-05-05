<?php

namespace App\Filament\Supramanager\Widgets;

use App\Models\Tenant;
use App\Models\TrustLog;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {

        /*
        $totalTenants   = Tenant::count();
        $activeSubs     = Subscription::where('status', 'active')->count();
        $trialSubs      = Subscription::where('status', 'trial')->count();
        $logsThisMonth  = TrustLog::whereMonth('created_at', now()->month)->count();
        $revenueMonth   = Subscription::where('status', 'active')
            ->whereMonth('updated_at', now()->month)->sum('price_paid');
        $totalCost      = TrustLog::whereMonth('created_at', now()->month)
            ->sum('cost_estimate');
        */    


        /** @var \Illuminate\Database\Eloquent\Builder $tenantQuery */
        $tenantQuery = Tenant::query();
        $totalTenants = $tenantQuery->count();

        /** @var \Illuminate\Database\Eloquent\Builder $subQuery */
        $subQuery = Subscription::query();
        $activeSubs = $subQuery->where('status', 'active')->count();
        $trialSubs  = $subQuery->where('status', 'trial')->count();

        /** @var \Illuminate\Database\Eloquent\Builder $logQuery */
        $logQuery = TrustLog::query();
        $logsThisMonth = $logQuery->whereMonth('created_at', now()->month)->count();
        
        $revenueMonth = Subscription::query()->where('status', 'active')
            ->whereMonth('updated_at', now()->month)->sum('price_paid');

        $totalCost = $logQuery->whereMonth('created_at', now()->month)
            ->sum('cost_estimate');


        return [
            Stat::make('Tenants total', $totalTenants)->color('primary'),
            Stat::make('Abonnements actifs', $activeSubs)->color('success'),
            Stat::make('En période d\'essai', $trialSubs)->color('warning'),
            Stat::make('Analyses ce mois', number_format($logsThisMonth))->color('info'),
            Stat::make('Revenus ce mois', number_format($revenueMonth, 0, '.', ' ') . ' XOF')->color('success'),
            Stat::make('Coût IA ce mois', '$' . number_format($totalCost, 4))->color('gray'),
        ];
    }
}