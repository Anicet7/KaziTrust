<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppResource\Pages;
use App\Filament\Resources\AppResource\RelationManagers;
use App\Models\App;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AppResource extends Resource
{
    protected static ?string $model = App::class;
    protected static ?string $navigationIcon  = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'Mes Services API';
    protected static ?string $modelLabel      = 'Service API';
    protected static ?string $navigationGroup = 'API & Intégrations';

    // ✅ FIX #1 : isolation tenant explicite (double sécurité en plus du trait)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', Auth::user()->tenant_id);
    }

    // ✅ FIX #2 : contrôle limite plan avant création
    public static function canCreate(): bool
    {

     if (!in_array(Auth::user()->role, ['admin', 'developer'])) {
                return false;
            }

        $tenant  = Auth::user()->tenant;
        $plan    = $tenant->currentPlan();
        $maxApps = $plan?->max_apps ?? 1;

        if ($maxApps === -1) return true; // illimité

        /// $current = App::where('tenant_id', $tenant->id)->count();
        $current = \App\Models\App::query()->where('tenant_id', $tenant->id)->count();
        

        if ($current >= $maxApps) {
            Notification::make()
                ->warning()
                ->title('Limite atteinte')
                ->body("Votre plan {$plan->name} autorise {$maxApps} application(s). Passez à un plan supérieur.")
                ->send();
            return false;
        }

        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Informations générales')
                ->description("Définissez le nom du service qui consommera KaziTrust.")
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label("Nom de l'application")
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Ex: ERP Comptabilité, App Mobile...'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Service actif')
                        ->default(true),
                ]),

            Forms\Components\Section::make('Intelligence artificielle (BYO-AI)')
                ->description("Configurez votre propre clé API. Elle est chiffrée AES-256 en base de données.")
                ->icon('heroicon-o-sparkles')
                ->schema([
                    Forms\Components\Select::make('llm_provider')
                        ->label("Fournisseur d'IA")
                        ->options([
                            'openai' => 'OpenAI (Recommandé)',
                            'gemini' => 'Google Gemini',
                            'claude' => 'Anthropic Claude',
                        ])
                        ->default('openai')
                        ->required()
                        ->live(), // réactif pour adapter les modèles suggérés

                    Forms\Components\TextInput::make('llm_api_key')
                        ->label('Clé API sécurisée')
                        ->password()
                        ->revealable()
                        // ✅ FIX #3 : required seulement à la création
                        ->required(fn (string $context) => $context === 'create')
                        ->placeholder(fn (string $context) => $context === 'edit'
                            ? 'Laisser vide pour conserver la clé actuelle'
                            : 'sk-...')
                        // Ne pas écraser la valeur existante si champ vide à l'édition
                        ->dehydrated(fn ($state) => filled($state))
                        ->helperText('Chiffrée AES-256. Jamais affichée en clair après sauvegarde.'),

                    Forms\Components\KeyValue::make('ai_settings')
                        ->label('Paramètres avancés du modèle')
                        ->keyLabel('Propriété')
                        ->valueLabel('Valeur')
                        ->default(['model' => 'gpt-4o-mini', 'temperature' => '0.2'])
                        ->helperText('Ex: model = gpt-4o, temperature = 0.2'),
                ]),

            Forms\Components\Section::make('Configuration Webhook')
                ->description('Recevez des alertes temps réel quand une fraude est détectée.')
                ->icon('heroicon-o-globe-alt')
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('webhook_url')
                        ->label('URL du Webhook')
                        ->url()
                        ->placeholder('https://votre-serveur.com/api/kazitrust-alerts'),

                    Forms\Components\TextInput::make('webhook_secret')
                        ->label('Secret de signature (HMAC)')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state) => filled($state))
                        ->placeholder(fn (string $context) => $context === 'edit'
                            ? 'Laisser vide pour conserver le secret actuel'
                            : 'Généré automatiquement si vide')
                        ->helperText('Permet de vérifier que les requêtes viennent bien de KaziTrust.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Application')
                    ->searchable()->sortable()->weight('bold'),

                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->copyable()->copyMessage('UUID copié !')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('llm_provider')
                    ->label('Moteur IA')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'openai' => 'success',
                        'gemini' => 'info',
                        'claude' => 'warning',
                        default  => 'gray',
                    }),

                // Nombre de clés API actives
                Tables\Columns\TextColumn::make('apiKeys_count')
                    ->label('Clés API')
                    ->counts('apiKeys')
                    ->badge()->color('gray'),

                // Nombre de logs du mois
                Tables\Columns\TextColumn::make('trustLogs_count')
                    ->label('Appels ce mois')
                    ->counts('trustLogs')
                    ->badge()->color('info'),

                Tables\Columns\ToggleColumn::make('is_active')->label('Actif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')->dateTime('d/m/Y')
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Statut'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading("Aucun service API")
            ->emptyStateDescription("Créez votre première application pour obtenir un UUID et commencer à intégrer KaziTrust.")
            ->emptyStateIcon('heroicon-o-squares-plus');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApiKeysRelationManager::class, // ✅ créé ci-dessous
        ];
    }


            // ✅ Seuls admin et developer voient cette ressource
        public static function canViewAny(): bool
        {
            return in_array(Auth::user()->role, ['admin', 'developer']);
        }

       

        // ✅ Édition et suppression : admin et developer uniquement
        public static function canEdit($record): bool
        {
            return in_array(Auth::user()->role, ['admin', 'developer']);
        }

        public static function canDelete($record): bool
        {
            return in_array(Auth::user()->role, ['admin', 'developer']);
        }


    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApps::route('/'),
            'create' => Pages\CreateApp::route('/create'),
            'edit'   => Pages\EditApp::route('/{record}/edit'),
        ];
    }
}