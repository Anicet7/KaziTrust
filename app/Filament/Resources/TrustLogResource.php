<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrustLogResource\Pages;
use App\Models\App;
use App\Models\TrustLog;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TrustLogResource extends Resource
{
    protected static ?string $model          = TrustLog::class;
    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Logs d\'Analyse';
    protected static ?string $modelLabel      = 'Log';
    protected static ?string $navigationGroup = 'API & Intégrations';

    // ✅ FIX #1 : lecture seule — pas de création manuelle
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    // ✅ FIX #2 : isolation tenant via la relation app→tenant_id
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('app', function (Builder $q) {
                $q->where('tenant_id', Auth::user()->tenant_id);
            });
    }

    // form() vide — resource en lecture seule, Filament l'exige quand même
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date / Heure')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('app.name')
                    ->label('Application')
                    ->badge()->color('gray'),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Numéro analysé')
                    ->searchable()
                    ->fontFamily('mono'),

                // ✅ Décision IA avec couleur
                Tables\Columns\TextColumn::make('ai_response.decision')
                    ->label('Décision')
                    ->badge()
                    ->color(fn (?string $state) => match (strtolower($state ?? '')) {
                        'approve'       => 'success',
                        'reject'        => 'danger',
                        'manual_review' => 'warning',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => match (strtolower($state ?? '')) {
                        'approve'       => '✓ Approuvé',
                        'reject'        => '✗ Rejeté',
                        'manual_review' => '⚠ Révision',
                        default         => $state ?? '—',
                    }),

                Tables\Columns\TextColumn::make('ai_response.score')
                    ->label('Score')
                    ->numeric()
                    ->suffix('%')
                    ->color(fn (?string $state) => match(true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default      => 'danger',
                    }),

                Tables\Columns\TextColumn::make('ai_provider')
                    ->label('Moteur IA')
                    ->badge()->color('info'),

                // ✅ FIX #3 : colonnes métriques ajoutées
                Tables\Columns\TextColumn::make('latency_ms')
                    ->label('Latence')
                    ->formatStateUsing(fn ($state) => $state . ' ms')
                    ->color(fn ($state) => $state > 3000 ? 'danger' : ($state > 1500 ? 'warning' : 'success'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('token_count')
                    ->label('Tokens')
                    ->numeric()->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('cost_estimate')
                    ->label('Coût estimé')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 4))
                    ->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filtre par application du tenant
                Tables\Filters\SelectFilter::make('app')
                    ->label('Application')
                    ->relationship('app', 'name', fn (Builder $query) =>
                        $query->where('tenant_id', Auth::user()->tenant_id)
                    ),

                // Filtre par décision
                Tables\Filters\SelectFilter::make('decision')
                    ->label('Décision')
                    ->options([
                        'approve'       => 'Approuvé',
                        'reject'        => 'Rejeté',
                        'manual_review' => 'Révision manuelle',
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        $data['value']
                            ? $query->whereJsonContains('ai_response->decision', $data['value'])
                            : $query
                    ),

                // Filtre par période
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Du'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Au'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'],  fn ($q) => $q->whereDate('created_at', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('created_at', '<=', $data['until']))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Détails'),
            ])
            ->emptyStateHeading('Aucun log pour le moment')
            ->emptyStateDescription("Les analyses apparaîtront ici après vos premiers appels API.")
            ->emptyStateIcon('heroicon-o-shield-check');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Résultat de l\'analyse')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('ai_response.decision')
                        ->label('Décision')
                        ->badge()
                        ->color(fn (?string $state) => match(strtolower($state ?? '')) {
                            'approve'       => 'success',
                            'reject'        => 'danger',
                            'manual_review' => 'warning',
                            default         => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('ai_response.score')
                        ->label('Score de confiance')->suffix('%'),
                    Infolists\Components\TextEntry::make('ai_provider')
                        ->label('Moteur IA utilisé'),
                ]),

            Infolists\Components\Section::make('Raisonnement de l\'IA')
                ->schema([
                    Infolists\Components\TextEntry::make('ai_response.reasoning')
                        ->label('Analyse détaillée')
                        ->columnSpanFull()
                        ->prose(), // rendu markdown si l'IA retourne du markdown
                ]),

            Infolists\Components\Section::make('Métriques techniques')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('latency_ms')
                        ->label('Latence totale')->suffix(' ms'),
                    Infolists\Components\TextEntry::make('token_count')
                        ->label('Tokens consommés'),
                    Infolists\Components\TextEntry::make('cost_estimate')
                        ->label('Coût estimé')
                        ->formatStateUsing(fn ($state) => '$' . number_format($state, 6)),
                ]),

            Infolists\Components\Section::make('Données brutes réseau (Nokia CAMARA)')
                ->collapsed()
                ->schema([
                    Infolists\Components\KeyValueEntry::make('nokia_payload')
                        ->label('Payload reçu'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            // ✅ FIX #1 : seulement index + view, pas create/edit
            'index' => Pages\ListTrustLogs::route('/'),
            'view'  => Pages\ViewTrustLog::route('/{record}'),
        ];
    }

    // ✅ Tous les rôles voient les logs (mais déjà en lecture seule)
    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'developer', 'viewer']);
    }


}