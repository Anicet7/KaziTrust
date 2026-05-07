<?php

namespace App\Filament\Resources\TrustLogResource\Widgets;

use App\Models\TrustLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrustLogStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Sécurité : Si pas de tenant, on ne retourne rien
        if (!$user || !$user->tenant_id) {
            return [];
        }

        // 1. On définit la requête de base limitée au tenant de l'utilisateur
        $query = TrustLog::whereHas('app', function ($q) use ($user) {
            $q->where('tenant_id', $user->tenant_id);
        });

        // 2. Calcul des Fraudes Bloquées (Décision = 'reject')
        // On clone la requête pour ne pas polluer les autres calculs
        $fraudsCount = (clone $query)
            ->whereJsonContains('ai_response->decision', 'reject')
            ->count();

        // 3. Calcul du Taux de Confiance Moyen (Moyenne du champ score dans le JSON)
        $averageScore = (clone $query)
            ->avg(DB::raw("CAST(JSON_EXTRACT(ai_response, '$.score') AS UNSIGNED)")) ?? 0;

        // 4. Calcul de la Latence Moyenne (Performance API)
        $avgLatency = (clone $query)->avg('latency_ms') ?? 0;

        return [
            Stat::make('Fraudes Bloquées', number_format($fraudsCount))
                ->description('Tentatives suspectes rejetées')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('danger')
                ->chart([15, 10, 25, 18, 30, 45, $fraudsCount > 0 ? 20 : 0]),

            Stat::make('Taux de Confiance', round($averageScore, 1) . '%')
                ->description('Score IA moyen sur vos flux')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($averageScore >= 80 ? 'success' : ($averageScore >= 50 ? 'warning' : 'danger')),

            Stat::make('Réactivité API', round($avgLatency) . ' ms')
                ->description('Temps de réponse moyen')
                ->descriptionIcon('heroicon-m-bolt')
                ->color($avgLatency < 1500 ? 'success' : 'warning'),
        ];
    }
}