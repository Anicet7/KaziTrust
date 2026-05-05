<?php

namespace App\Filament\Widgets;

use App\Models\TrustLog;
use App\Models\App;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecentLogsWidget extends BaseWidget
{
    protected static ?string $heading  = 'Dernières analyses';
    protected static ?int    $sort     = 3;
    // protected int | string   $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $appIds = App::where('tenant_id', Auth::user()->tenant_id)->pluck('id');

        return $table
            ->query(
                TrustLog::whereIn('app_id', $appIds)->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')->dateTime('d/m/Y H:i')->sortable(),

                Tables\Columns\TextColumn::make('app.name')
                    ->label('App')->badge()->color('gray'),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Numéro')->fontFamily('mono'),

                Tables\Columns\TextColumn::make('ai_response.decision')
                    ->label('Décision')
                    ->badge()
                    ->color(fn (?string $state) => match(strtolower($state ?? '')) {
                        'approve'       => 'success',
                        'reject'        => 'danger',
                        'manual_review' => 'warning',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => match(strtolower($state ?? '')) {
                        'approve'       => '✓ Approuvé',
                        'reject'        => '✗ Rejeté',
                        'manual_review' => '⚠ Révision',
                        default         => '—',
                    }),

                Tables\Columns\TextColumn::make('ai_response.score')
                    ->label('Score')->suffix('%'),

                Tables\Columns\TextColumn::make('latency_ms')
                    ->label('Latence')->suffix(' ms'),
            ])
            ->paginated(false);
    }
}