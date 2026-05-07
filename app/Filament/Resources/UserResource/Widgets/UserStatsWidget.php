<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Sécurité : Si pas de tenant_id, on ne retourne rien
        if (!$user || !$user->tenant_id) {
            return [];
        }

        $tenantId = $user->tenant_id;

        // 1. Nombre total de membres dans l'équipe
        $totalMembers = User::where('tenant_id', $tenantId)->count();

        // 2. Nombre de développeurs (ceux qui gèrent les API)
        $devsCount = User::where('tenant_id', $tenantId)
            ->where('role', 'developer')
            ->count();

        // 3. Nouveaux membres ce mois-ci (pour la tendance)
        $newThisMonth = User::where('tenant_id', $tenantId)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            Stat::make('Membres de l\'équipe', $totalMembers)
                ->description('Collaborateurs actifs')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([3, 5, 4, 6, 8, 7, $totalMembers]),

            Stat::make('Développeurs', $devsCount)
                ->description('Accès aux clés API')
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('info'),

            Stat::make('Nouveaux ce mois', $newThisMonth)
                ->description('Inscriptions récentes')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($newThisMonth > 0 ? 'success' : 'gray'),
        ];
    }
}