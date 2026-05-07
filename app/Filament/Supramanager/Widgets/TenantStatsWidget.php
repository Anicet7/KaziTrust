<?php

namespace App\Filament\Supramanager\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class TenantStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Statistique globale
            Stat::make('Total Entreprises (Tenants)', Tenant::count())
                ->description('PME et IMF inscrites')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            // Statistique filtrée sur la relation d'abonnement
            Stat::make('Comptes en Période d\'Essai', 
                Tenant::whereHas('activeSubscription', function (Builder $query) {
                    $query->where('status', 'trial');
                })->count()
            )
                ->description('À convertir ce mois-ci')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            // Ajout d'une statistique sur les comptes actifs pour compléter le dashboard
            Stat::make('Abonnements Actifs', 
                Tenant::whereHas('activeSubscription', function (Builder $query) {
                    $query->where('status', 'active');
                })->count()
            )
                ->description('Clients payants')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}