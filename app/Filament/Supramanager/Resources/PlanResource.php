<?php

namespace App\Filament\Supramanager\Resources;

use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'Plans';
    protected static ?string $navigationGroup = 'Abonnements';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations générales')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom du plan')
                        ->required(),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (identifiant unique)')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\Toggle::make('is_public')->default(true),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()->default(0),
                ]),

            Forms\Components\Section::make('Tarification')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('price_monthly')
                        ->label('Prix mensuel')
                        ->numeric()->prefix('XOF')->default(0),
                    Forms\Components\TextInput::make('price_yearly')
                        ->label('Prix annuel')
                        ->numeric()->prefix('XOF')->default(0),
                    Forms\Components\TextInput::make('currency')
                        ->default('XOF'),
                ]),

            Forms\Components\Section::make('Limites d\'usage')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('max_apps')
                        ->label('Nb apps max (-1 = illimité)')
                        ->numeric()->default(1),
                    Forms\Components\TextInput::make('max_api_keys_per_app')
                        ->label('Clés API par app (-1 = illimité)')
                        ->numeric()->default(2),
                    Forms\Components\TextInput::make('max_requests_per_month')
                        ->label('Requêtes/mois (-1 = illimité)')
                        ->numeric()->default(500),
                    Forms\Components\TextInput::make('max_users')
                        ->label('Utilisateurs max (-1 = illimité)')
                        ->numeric()->default(1),
                ]),

            Forms\Components\Section::make('Fonctionnalités')
                ->schema([
                    Forms\Components\KeyValue::make('features')
                        ->label('Features (clé: feature, valeur: true/false)')
                        ->keyLabel('Feature')
                        ->valueLabel('Activée ?'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Plan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->label('Prix/mois')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ' ') . ' XOF'),
                Tables\Columns\TextColumn::make('max_requests_per_month')
                    ->label('Requêtes/mois')
                    ->formatStateUsing(fn ($state) => $state === -1 ? '∞' : number_format($state)),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Souscriptions')
                    ->counts('subscriptions'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Actif'),
                Tables\Columns\IconColumn::make('is_public')->boolean()->label('Public'),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Supramanager\Resources\PlanResource\Pages\ListPlans::route('/'),
            'create' => \App\Filament\Supramanager\Resources\PlanResource\Pages\CreatePlan::route('/create'),
            'edit'   => \App\Filament\Supramanager\Resources\PlanResource\Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}