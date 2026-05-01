<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppResource\Pages;
use App\Filament\Resources\AppResource\RelationManagers;
use App\Models\App;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppResource extends Resource
{
    protected static ?string $model = App::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'Mes Services API';
    protected static ?string $modelLabel = 'Service API';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations Générales')
                    ->description('Définissez le nom de l\'application ou du service qui consommera KaziTrust.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'application')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ex: ERP Comptabilité, App Mobile...'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Service Actif')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Intelligence Artificielle (BYO-AI)')
                    ->description('Configurez votre propre clé API pour garder le contrôle de vos coûts et de vos données.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Forms\Components\Select::make('llm_provider')
                            ->label('Fournisseur d\'IA')
                            ->options([
                                'openai' => 'OpenAI (Recommandé)',
                                'gemini' => 'Google Gemini',
                                'claude' => 'Anthropic Claude',
                            ])
                            ->default('openai')
                            ->required(),

                        Forms\Components\TextInput::make('llm_api_key')
                            ->label('Clé API Sécurisée')
                            ->password() // Masque la saisie
                            ->revealable() // Permet de voir la clé en cliquant sur l'œil
                            ->helperText('Cette clé est chiffrée avec un algorithme AES-256 dans notre base de données.')
                            ->required(),

                        Forms\Components\KeyValue::make('ai_settings')
                            ->label('Paramètres avancés du modèle')
                            ->keyLabel('Propriété')
                            ->valueLabel('Valeur')
                            ->default(['model' => 'gpt-4o-mini', 'temperature' => '0.2'])
                            ->helperText('Spécifiez le modèle exact à utiliser (ex: model = gpt-4o).'),
                    ]),

                Forms\Components\Section::make('Configuration Webhook')
                    ->description('Recevez des alertes en temps réel sur vos serveurs lorsqu\'une fraude est détectée.')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\TextInput::make('webhook_url')
                            ->label('URL du Webhook')
                            ->url()
                            ->placeholder('https://votre-serveur.com/api/kazitrust-alerts'),
                            
                        Forms\Components\TextInput::make('webhook_secret')
                            ->label('Secret de signature')
                            ->password()
                            ->revealable()
                            ->helperText('Utilisé pour signer nos requêtes (HMAC) afin que vous puissiez vérifier qu\'elles proviennent bien de KaziTrust.'),
                    ])->collapsed(), // On réduit cette section par défaut pour alléger l'UI
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Application')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('uuid')
                    ->label('ID Client (UUID)')
                    ->copyable() // Permet au client de copier l'UUID en un clic !
                    ->copyMessage('UUID copié')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('llm_provider')
                    ->label('Moteur IA')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'openai' => 'success',
                        'gemini' => 'info',
                        'claude' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Actif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Nous ajouterons ici la relation (RelationManager) vers AppApiKeys plus tard !
            RelationManagers\ApiKeysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApps::route('/'),
            'create' => Pages\CreateApp::route('/create'),
            'edit' => Pages\EditApp::route('/{record}/edit'),
        ];
    }
}