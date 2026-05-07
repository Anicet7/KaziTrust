<?php

namespace App\Filament\Widgets;

use App\Models\App;
use App\Models\AppApiKey;
use App\Models\TrustLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $tenantId = Auth::user()->tenant_id;

        // Analyses du mois en cours vs mois précédent
        $analysesThisMonth = TrustLog::whereHas('app', fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $analysesLastMonth = TrustLog::whereHas('app', fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $analysesTrend = $analysesLastMonth > 0
            ? round((($analysesThisMonth - $analysesLastMonth) / $analysesLastMonth) * 100)
            : 0;

        // Total analyses
        $totalAnalyses = TrustLog::whereHas('app', fn ($q) => $q->where('tenant_id', $tenantId))->count();

        // Apps actives
        $activeApps = App::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $totalApps  = App::where('tenant_id', $tenantId)->count();

        // Clés API actives
        $activeKeys = AppApiKey::whereHas('app', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('is_active', true)
            ->count();

        // Coût estimé du mois
        $costThisMonth = TrustLog::whereHas('app', fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('cost_estimate');

        return [
            Stat::make('Analyses ce mois', number_format($analysesThisMonth))
                ->description($analysesTrend >= 0
                    ? "+{$analysesTrend}% vs mois dernier"
                    : "{$analysesTrend}% vs mois dernier")
                ->descriptionIcon($analysesTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($analysesTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyChart($tenantId)),

            Stat::make('Total analyses', number_format($totalAnalyses))
                ->description('Depuis la création du compte')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Applications actives', "{$activeApps} / {$totalApps}")
                ->description("{$activeKeys} clé(s) API active(s)")
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('info'),

            Stat::make('Coût estimé (mois)', '$' . number_format($costThisMonth, 4))
                ->description('USD · mis à jour en temps réel')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }

    /**
     * Graphique sparkline des 7 derniers jours
     */
    private function getMonthlyChart(int $tenantId): array
    {
        return TrustLog::whereHas('app', fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->get()
            ->groupBy(fn ($log) => $log->created_at->format('Y-m-d'))
            ->map(fn ($group) => $group->count())
            ->values()
            ->toArray();
    }
}