<?php

namespace App\Filament\Widgets;

use App\Models\TrustLog;
use App\Models\App;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AnalysisChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Analyses des 30 derniers jours';
    protected static ?int    $sort    = 2;
   // protected int | string   $columnSpan = 'full';

    protected function getData(): array
    {
        $tenantId = Auth::user()->tenant_id;
        $appIds   = App::where('tenant_id', $tenantId)->pluck('id');

        

        $days    = collect(range(29, 0))->map(fn($d) => now()->subDays($d)->format('Y-m-d'));
        $labels  = $days->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'));

        $logs = TrustLog::whereIn('app_id', $appIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw("DATE(created_at) as date, 
                         JSON_UNQUOTE(JSON_EXTRACT(ai_response, '$.decision')) as decision,
                         COUNT(*) as total")
            ->groupBy('date', 'decision')
            ->get()
            ->groupBy('date');

        $approved = $days->map(fn($d) =>
            $logs->get($d)?->firstWhere('decision', 'approve')?->total ?? 0
        );
        $rejected = $days->map(fn($d) =>
            $logs->get($d)?->firstWhere('decision', 'reject')?->total ?? 0
        );
        $manual = $days->map(fn($d) =>
            $logs->get($d)?->firstWhere('decision', 'manual_review')?->total ?? 0
        );

        return [
            'datasets' => [
                [
                    'label'           => 'Approuvés',
                    'data'            => $approved->values()->toArray(),
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Rejetés',
                    'data'            => $rejected->values()->toArray(),
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
                [
                    'label'           => 'Révision',
                    'data'            => $manual->values()->toArray(),
                    'borderColor'     => '#f59e0b',
                    'backgroundColor' => 'rgba(245,158,11,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}