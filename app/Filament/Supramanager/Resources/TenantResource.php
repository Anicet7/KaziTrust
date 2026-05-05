<?php

namespace App\Filament\Supramanager\Resources;

use App\Models\Plan;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Tenants';
    protected static ?string $navigationGroup = 'Abonnements';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Entreprise')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('email')->email()->required(),
                    Forms\Components\Toggle::make('is_active')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Entreprise')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activeSubscription.plan.name')
                    ->label('Plan actuel')
                    ->badge()
                    ->color(fn (string $state = null) => match($state) {
                        'Trial'      => 'warning',
                        'Starter'    => 'info',
                        'Pro'        => 'success',
                        'Enterprise' => 'purple',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('activeSubscription.status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state = null) => match($state) {
                        'active'    => 'success',
                        'trial'     => 'warning',
                        'past_due'  => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('activeSubscription.trial_ends_at')
                    ->label('Fin essai')
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('apps_count')
                    ->label('Apps')->counts('apps'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')->dateTime('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subscription_status')
                    ->label('Statut')
                    ->relationship('activeSubscription', 'status')
                    ->options([
                        'trial'     => 'Trial',
                        'active'    => 'Actif',
                        'past_due'  => 'Impayé',
                        'cancelled' => 'Annulé',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Action rapide : changer de plan manuellement
                Tables\Actions\Action::make('change_plan')
                    ->label('Changer de plan')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('plan_id')
                            ->label('Nouveau plan')
                            ->options(Plan::active()->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active'    => 'Actif',
                                'trial'     => 'Trial',
                                'cancelled' => 'Annulé',
                            ])->required(),
                        Forms\Components\Textarea::make('notes')->label('Notes admin'),
                    ])
                    ->action(function (Tenant $record, array $data) {
                        // Expirer l'ancienne souscription
                        $record->subscriptions()
                            ->whereIn('status', ['active', 'trial'])
                            ->update(['status' => 'expired']);

                        // Créer la nouvelle
                        $record->subscriptions()->create([
                            'plan_id'    => $data['plan_id'],
                            'status'     => $data['status'],
                            'starts_at'  => now(),
                            'ends_at'    => now()->addMonth(),
                            'notes'      => $data['notes'] ?? null,
                        ]);
                    }),
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
            // À ajouter plus tard : SubscriptionsRelationManager, PaymentsRelationManager
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Supramanager\Resources\TenantResource\Pages\ListTenants::route('/'),
            'create' => \App\Filament\Supramanager\Resources\TenantResource\Pages\CreateTenant::route('/create'),
            'edit'   => \App\Filament\Supramanager\Resources\TenantResource\Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}