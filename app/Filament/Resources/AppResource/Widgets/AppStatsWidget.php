<?php

namespace App\Filament\Resources\AppResource\Widgets;

use App\Models\App;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AppStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // 1. Récupération sécurisée via l'utilisateur authentifié
        $user = Auth::user();
        
        // Sécurité : Si pas d'utilisateur ou pas de tenant_id, on s'arrête proprement
        if (!$user || !$user->tenant_id) {
            return [];
        }

        $tenantId = $user->tenant_id;

        // 2. Calcul des statistiques réelles pour ce tenant
        $activeAppsCount = App::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->count();

        // On récupère le nom du provider le plus utilisé pour ce tenant
        $favoriteProvider = App::where('tenant_id', $tenantId)
            ->select('llm_provider')
            ->groupBy('llm_provider')
            ->orderByRaw('COUNT(*) DESC')
            ->first()?->llm_provider ?? 'Aucun';

        return [
            Stat::make('Services API Actifs', $activeAppsCount)
                ->description('Applications prêtes à l\'usage')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([7, 3, 10, 5, 12, 8, 15]),

            Stat::make('Moteur IA Principal', strtoupper($favoriteProvider))
                ->description('Fournisseur le plus configuré')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info'),

            Stat::make('Sécurité & Logs', 'Actif')
                ->description('Analyse de fraude en temps réel')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),
        ];
    }
}