<?php

namespace App\Filament\Supramanager\Widgets;

use App\Models\Tenant;
use App\Models\TrustLog;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class GlobalStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // On définit le début du mois une seule fois pour la cohérence des données
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Statistiques Tenants & Abonnements
        $totalTenants = Tenant::count();
        
        $activeSubs = Subscription::query()
            ->where('status', 'active')
            ->count();
            
        $trialSubs = Subscription::query()
            ->where('status', 'trial')
            ->count();

        // 2. Analyses et Coûts IA (TrustLogs)
        $logsThisMonth = TrustLog::query()
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        $totalCost = TrustLog::query()
            ->where('created_at', '>=', $startOfMonth)
            ->sum('cost_estimate');

        // 3. Revenus (Basé sur la date de début de l'abonnement ou de mise à jour)
        $revenueMonth = Subscription::query()
            ->where('status', 'active')
            ->where('updated_at', '>=', $startOfMonth)
            ->sum('price_paid');

        return [
            Stat::make('Tenants total', $totalTenants)
                ->description('Total des comptes créés')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make('Abonnements actifs', $activeSubs)
                ->description('Clients payants')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('En période d\'essai', $trialSubs)
                ->description('Potentiel de conversion')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Analyses ce mois', number_format($logsThisMonth, 0, ',', ' '))
                ->description('Volume d\'activité IA')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('info'),

            Stat::make('Revenus ce mois', number_format($revenueMonth, 0, '.', ' ') . ' XOF')
                ->description('Chiffre d\'affaires mensuel')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Coût IA ce mois', '$' . number_format($totalCost, 4))
                ->description('Estimation API')
                ->descriptionIcon('heroicon-m-variable')
                ->color('danger'),
        ];
    }
}