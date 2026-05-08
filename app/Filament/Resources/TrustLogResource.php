<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrustLogResource\Pages;
use App\Models\TrustLog;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class TrustLogResource extends Resource
{
    protected static ?string $model = TrustLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Logs d\'Analyse';
    protected static ?string $modelLabel = 'Log d\'analyse';
    protected static ?string $navigationGroup = 'API & Intégrations';

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('app', function (Builder $q) {
                $q->where('tenant_id', Auth::user()->tenant_id);
            });
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('app.name')
                    ->label('App')
                    ->badge(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Numéro')
                    ->copyable()
                    ->fontFamily('mono'),

                // ✅ Sécurisé : Gère le cas où l'IA renvoie [] (logs 1 à 9)
                Tables\Columns\TextColumn::make('ai_response_decision')
                    ->label('Décision')
                    ->badge()
                    ->state(function (TrustLog $record) {
                        if (empty($record->ai_response)) return 'no_data';
                        return data_get($record->ai_response, 'decision', 'unknown');
                    })
                    ->color(fn (string $state) => match (strtolower($state)) {
                        'approve'       => 'success',
                        'reject'        => 'danger',
                        'manual_review' => 'warning',
                        'no_data'       => 'gray',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match (strtolower($state)) {
                        'approve'       => '✓ Approuvé',
                        'reject'        => '✗ Rejeté',
                        'manual_review' => '⚠ Révision',
                        'no_data'       => 'Non analysé',
                        default         => 'Inconnu',
                    }),

                Tables\Columns\TextColumn::make('ai_response.score')
                    ->label('Score')
                    ->numeric()
                    ->suffix('%')
                    ->state(fn (TrustLog $record) => empty($record->ai_response) ? null : data_get($record->ai_response, 'score'))
                    ->color(fn (?int $state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger'))
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('latency_ms')
                    ->label('Latence')
                    ->suffix(' ms')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('app')
                    ->relationship('app', 'name', fn (Builder $query) => $query->where('tenant_id', Auth::user()->tenant_id)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            // SECTION 1 : VUE D'ENSEMBLE (DÉCISION)
            Infolists\Components\Section::make('Résultat de l\'analyse')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('ai_decision')
                        ->label('Décision Finale')
                        ->badge()
                        ->state(fn ($record) => empty($record->ai_response) ? 'no_data' : data_get($record->ai_response, 'decision', 'unknown'))
                        ->color(fn (string $state) => match(strtolower($state)) {
                            'approve' => 'success', 'reject' => 'danger', 'manual_review' => 'warning', default => 'gray',
                        })
                        ->formatStateUsing(fn (string $state) => match (strtolower($state)) {
                            'approve' => 'Approuvé', 'reject' => 'Rejeté', 'manual_review' => 'Révision Manuelle', 'no_data' => 'Aucune donnée IA', default => 'Inconnu',
                        }),

                    Infolists\Components\TextEntry::make('score')
                        ->label('Score de Confiance')
                        ->state(fn ($record) => empty($record->ai_response) ? 'N/A' : data_get($record->ai_response, 'score', '0') . '%')
                        ->weight('bold'),

                    Infolists\Components\TextEntry::make('ai_provider')
                        ->label('Moteur IA')
                        ->badge()
                        ->color('info'),
                ]),

            // SECTION 2 : SIGNAUX NOKIA (Extraction intelligente du payload)
            /*
            Infolists\Components\Section::make('Signaux Réseau (Nokia CAMARA)')
                ->description('Indicateurs extraits du réseau opérateur')
                ->columns(3)
                ->schema([
                    // SIM SWAP
                    Infolists\Components\TextEntry::make('nokia_sim_swap')
                        ->label('SIM Swap Détecté')
                        ->badge()
                        ->state(function ($record) {
                            $err = data_get($record->nokia_payload, 'sim_swap.error');
                            if ($err) return 'Erreur API';
                            return data_get($record->nokia_payload, 'sim_swap.swapped') === true ? 'Oui' : 'Non';
                        })
                        ->color(fn ($state) => $state === 'Oui' ? 'danger' : ($state === 'Non' ? 'success' : 'gray')),

                    // ROAMING
                    Infolists\Components\TextEntry::make('nokia_roaming')
                        ->label('Itinérance (Roaming)')
                        ->badge()
                        ->state(function ($record) {
                            $err = data_get($record->nokia_payload, 'roaming.error');
                            if ($err) return 'Erreur API';
                            
                            $isRoaming = data_get($record->nokia_payload, 'roaming.is_roaming');
                            if ($isRoaming) {
                                // Gère le fait que country_name est un tableau, ex: ["HU"]
                                $country = data_get($record->nokia_payload, 'roaming.country_name.0', 'Inconnu');
                                return "Oui ($country)";
                            }
                            return 'Non';
                        })
                        ->color(fn ($state) => str_starts_with($state, 'Oui') ? 'warning' : ($state === 'Non' ? 'success' : 'gray')),

                    // NETWORK STATUS
                    Infolists\Components\TextEntry::make('nokia_network')
                        ->label('Statut de l\'appareil')
                        ->badge()
                        ->state(function ($record) {
                            $err = data_get($record->nokia_payload, 'network_status.error');
                            if ($err) return 'Erreur API';
                            return data_get($record->nokia_payload, 'network_status.status', 'Inconnu');
                        })
                        ->color(fn ($state) => str_contains(strtoupper($state), 'CONNECTED') ? 'success' : 'gray'),
                ]),
                */
                
                 
                 
                 // --- SECTION 2 : DIAGNOSTIC RÉSEAU (L'ARBRE PRO) ---
            Section::make('Expert Network Diagnostics (Nokia CAMARA)')
    ->description('Analyse granulaire des métadonnées opérateur et détection d\'anomalies')
    ->icon('heroicon-o-cpu-chip')
    ->collapsible()
    ->schema([
        Grid::make(3)
            ->schema([
                
                // --- COLONNE 1 : SÉCURITÉ SIM ---
                Group::make([
                    TextEntry::make('sim_header')
                        ->label('🛡️ SÉCURITÉ DE LA LIGNE')
                        ->default('Analyse SIM Swap')
                        ->weight('bold')
                        ->color('gray'),
                    
                    IconEntry::make('nokia_payload.sim_swap.swapped')
                        ->label('Changement de SIM')
                        ->boolean()
                        ->trueIcon('heroicon-o-exclamation-triangle')
                        ->falseIcon('heroicon-o-check-badge')
                        ->trueColor('danger')
                        ->falseColor('success'),

                    TextEntry::make('sim_details')
                        ->label('Détails Temporels')
                        ->state(function ($record) {
                            $data = data_get($record->nokia_payload, 'sim_swap');
                            if (!empty($data['error'])) return '⚠️ Service Indisponible';
                            if (!empty($data['raw_date'])) {
                                return "Dernier swap: " . \Carbon\Carbon::parse($data['raw_date'])->diffForHumans();
                            }
                            return 'Aucun changement récent';
                        })
                        ->size('xs')
                        ->color('gray'),
                ])->extraAttributes(['class' => 'border-l-2 border-primary-500 pl-4']),

                // --- COLONNE 2 : LOCALISATION & ROAMING ---
                Group::make([
                    TextEntry::make('geo_header')
                        ->label('🌍 GÉO-LOCALISATION')
                        ->default('Roaming & Connectivité')
                        ->weight('bold')
                        ->color('gray'),

                    TextEntry::make('roaming_status')
                        ->label('Statut Itinérance')
                        ->badge()
                        ->state(fn ($record) => data_get($record->nokia_payload, 'roaming.is_roaming') ? 'EN ROAMING' : 'RÉSEAU NATIONAL')
                        ->color(fn ($state) => $state === 'EN ROAMING' ? 'warning' : 'success'),

                    TextEntry::make('country_info')
                        ->label('Localisation Opérateur')
                        ->state(function ($record) {
                            $countries = data_get($record->nokia_payload, 'roaming.country_name');
                            $code = data_get($record->nokia_payload, 'roaming.country_code');
                            if (is_array($countries)) return "Pays: " . implode(', ', $countries) . " (MCC: $code)";
                            return "Région: Bénin (Local)";
                        })
                        ->size('xs')
                        ->fontFamily('mono'),
                ])->extraAttributes(['class' => 'border-l-2 border-primary-500 pl-4']),

                // --- COLONNE 3 : ÉTAT TECHNIQUE ---
                Group::make([
                    TextEntry::make('tech_header')
                        ->label('📡 ÉTAT DU TERMINAL')
                        ->default('Status Réseau Actif')
                        ->weight('bold')
                        ->color('gray'),

                    TextEntry::make('nokia_payload.network_status.status')
                        ->label('Mode de Connexion')
                        ->badge()
                        ->formatStateUsing(fn ($state) => str_replace('_', ' ', $state))
                        ->color(fn ($state) => match($state) {
                            'CONNECTED_DATA' => 'success',
                            'CONNECTED_SMS' => 'info',
                            default => 'gray'
                        }),

                    TextEntry::make('api_version')
                        ->label('Version Protocole')
                        ->state(fn($record) => "API: " . data_get($record->nokia_payload, 'network_status.api_version', 'v0'))
                        ->size('xs')
                        ->fontFamily('mono'),
                ])->extraAttributes(['class' => 'border-l-2 border-primary-500 pl-4']),
            ]),

                    // PIED DE SECTION : L'INTERPRÉTATION MÉTIER (L'ARBRE DE DÉCISION)
                  // Remplacez ViewEntry par TextEntry
            Infolists\Components\TextEntry::make('risk_indicator')
                ->label('Interprétation Métier')
                ->columnSpanFull()
                ->state(function($record) {
                    $swapped = data_get($record->nokia_payload, 'sim_swap.swapped');
                    $roaming = data_get($record->nokia_payload, 'roaming.is_roaming');
                    
                    if ($swapped && $roaming) return "🔴 RISQUE CRITIQUE : SIM swappée et à l'étranger.";
                    if ($swapped) return "🟠 RISQUE MODÉRÉ : SIM swappée récemment.";
                    return "🟢 AUCUNE ANOMALIE RÉSEAU DÉTECTÉE";
                })
                // On utilise extraAttributes pour le style puisque c'est du texte simple
                ->extraAttributes(['class' => 'mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-xl italic text-center text-sm border border-gray-100 dark:border-gray-800'])
                ]),
                    
    

            // SECTION 3 : RAISONNEMENT DE L'IA (Uniquement si l'IA a répondu)
            Infolists\Components\Section::make('Analyse détaillée de l\'IA')
                ->visible(fn ($record) => !empty($record->ai_response))
                ->schema([
                    Infolists\Components\TextEntry::make('ai_reasoning')
                        ->label('Explication')
                        ->state(fn ($record) => data_get($record->ai_response, 'reasoning', 'Aucune explication.'))
                        ->prose(),
                    
                    Infolists\Components\TextEntry::make('ai_recommendation')
                        ->label('Recommandation')
                        ->state(fn ($record) => data_get($record->ai_response, 'recommendation', 'Aucune.'))
                        ->weight('bold')
                        ->color('warning'),
                ]),

            // SECTION 4 : DONNÉES TECHNIQUES ET BRUTES (JSON)
            Infolists\Components\Section::make('Données Techniques & Débogage')
                ->collapsed()
                ->schema([
                    Infolists\Components\Grid::make(3)->schema([
                        Infolists\Components\TextEntry::make('latency_ms')->label('Latence')->suffix(' ms'),
                        Infolists\Components\TextEntry::make('token_count')->label('Tokens'),
                        Infolists\Components\TextEntry::make('cost_estimate')->label('Coût')
                            ->formatStateUsing(fn ($state) => '$' . number_format((float) $state, 6)),
                    ]),

                    Infolists\Components\Grid::make(2)->schema([
                        // Payload Nokia Brut
                        Infolists\Components\TextEntry::make('nokia_payload_raw')
                            ->label('Payload Nokia (Brut)')
                            ->state(fn ($record) => json_encode($record->nokia_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                            ->fontFamily('mono')
                            ->extraAttributes(['class' => 'text-xs whitespace-pre-wrap overflow-x-auto bg-gray-50 dark:bg-gray-900 p-4 rounded-lg']),

                        // Réponse IA Brute
                        Infolists\Components\TextEntry::make('ai_response_raw')
                            ->label('Réponse IA (Brute)')
                            ->state(fn ($record) => empty($record->ai_response) ? '[]' : json_encode($record->ai_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->fontFamily('mono')
                            ->extraAttributes(['class' => 'text-xs whitespace-pre-wrap overflow-x-auto bg-gray-50 dark:bg-gray-900 p-4 rounded-lg']),
                    ])
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrustLogs::route('/'),
            'view'  => Pages\ViewTrustLog::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'developer', 'viewer']);
    }
}