<?php

namespace App\Filament\Widgets;

use App\Models\TrustLog;
use App\Models\App;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = Auth::user()->tenant_id;

        $appIds = App::where('tenant_id', $tenantId)->pluck('id');

        $totalLogs = TrustLog::whereIn('app_id', $appIds)->count();

        $thisMonth = TrustLog::whereIn('app_id', $appIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);

        $approved = (clone $thisMonth)
            ->whereJsonContains('ai_response->decision', 'approve')->count();

        $rejected = (clone $thisMonth)
            ->whereJsonContains('ai_response->decision', 'reject')->count();

        $manual = (clone $thisMonth)
            ->whereJsonContains('ai_response->decision', 'manual_review')->count();

        $totalMonth = (clone $thisMonth)->count();

        $avgLatency = (clone $thisMonth)->avg('latency_ms');
        $totalCost  = (clone $thisMonth)->sum('cost_estimate');

        $plan    = Auth::user()->tenant->currentPlan();
        $maxReq  = $plan?->max_requests_per_month ?? 100;
        $quota   = $maxReq === -1 ? '∞' : $totalMonth . '/' . $maxReq;

        return [
            Stat::make('Analyses ce mois', $totalMonth)
                ->description('Quota : ' . $quota)
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),

            Stat::make('Approuvés', $approved)
                ->description($totalMonth > 0
                    ? round($approved / $totalMonth * 100) . '% du total'
                    : '0%')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Rejetés', $rejected)
                ->description($totalMonth > 0
                    ? round($rejected / $totalMonth * 100) . '% du total'
                    : '0%')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Révision manuelle', $manual)
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('warning'),

            Stat::make('Latence moyenne', round($avgLatency ?? 0) . ' ms')
                ->descriptionIcon('heroicon-o-clock')
                ->color($avgLatency > 3000 ? 'danger' : 'success'),

            Stat::make('Coût estimé', '$' . number_format($totalCost, 4))
                ->description('Ce mois (USD)')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('gray'),
        ];
    }
}