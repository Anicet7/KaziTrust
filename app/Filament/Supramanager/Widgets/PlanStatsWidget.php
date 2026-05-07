<?php

namespace App\Filament\Supramanager\Widgets;

use App\Models\Plan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlanStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // On récupère les plans actifs avec le compte de leurs souscriptions actives
        $plans = Plan::where('is_active', true)
            ->withCount(['subscriptions' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        $stats = [];

        // 1. Statistique pour chaque Plan (Répartition)
        foreach ($plans as $plan) {
            $stats[] = Stat::make("Abonnés : {$plan->name}", $plan->subscriptions_count)
                ->description($plan->price_monthly > 0 ? number_format($plan->price_monthly, 0, '.', ' ') . ' XOF / mois' : 'Gratuit')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($plan->subscriptions_count > 0 ? 'success' : 'gray');
        }

        // 2. Identification du Plan "Best-seller"
        $bestPlan = $plans->sortByDesc('subscriptions_count')->first();
        
        if ($bestPlan && $bestPlan->subscriptions_count > 0) {
            $stats[] = Stat::make('Plan Populaire', $bestPlan->name)
                ->description('Le plan avec le plus de clients actifs')
                ->descriptionIcon('heroicon-m-star')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-default',
                    'style' => 'background-color: rgba(59, 130, 246, 0.05)',
                ]);
        }

        return $stats;
    }
}