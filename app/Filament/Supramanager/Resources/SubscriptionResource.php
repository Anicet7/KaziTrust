<?php

namespace App\Filament\Supramanager\Resources;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PaymentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Souscriptions';
    protected static ?string $navigationGroup = 'Abonnements';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('tenant_id')
                ->relationship('tenant', 'name')->required(),
            Forms\Components\Select::make('plan_id')
                ->relationship('plan', 'name')->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'trial'     => 'Trial',
                    'active'    => 'Actif',
                    'past_due'  => 'Impayé',
                    'cancelled' => 'Annulé',
                    'expired'   => 'Expiré',
                    'paused'    => 'Pausé',
                ])->required(),
            Forms\Components\DateTimePicker::make('starts_at')->label('Début'),
            Forms\Components\DateTimePicker::make('ends_at')->label('Fin'),
            Forms\Components\DateTimePicker::make('trial_ends_at')->label('Fin de trial'),
            Forms\Components\Textarea::make('notes')->label('Notes admin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Entreprise')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('plan.name')
                    ->label('Plan')->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match($state) {
                        'active'    => 'success',
                        'trial'     => 'warning',
                        'past_due'  => 'danger',
                        'cancelled',
                        'expired'   => 'gray',
                        'paused'    => 'info',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Cycle')
                    ->formatStateUsing(fn ($state) => $state === 'yearly' ? 'Annuel' : 'Mensuel'),
                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Montant')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ' ') . ' XOF'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Début')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Expire le')->date('d/m/Y')
                    ->color(fn ($record) => $record?->ends_at?->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('payment_provider')
                    ->label('Provider')->badge()->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'trial'     => 'Trial',
                        'active'    => 'Actif',
                        'past_due'  => 'Impayé',
                        'cancelled' => 'Annulé',
                        'expired'   => 'Expiré',
                    ]),
                Tables\Filters\SelectFilter::make('plan')
                    ->relationship('plan', 'name'),
            ])
            ->actions([
                // Action : changer de plan (avec simulation de paiement)
                Tables\Actions\Action::make('upgrade')
                    ->label('Changer plan')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('plan_id')
                            ->label('Nouveau plan')
                           /// ->options(Plan::active()->orderBy('sort_order')->pluck('name', 'id'))
                            ->options(
                                    \App\Models\Plan::query() // Force l'usage de ton modèle Eloquent
                                        ->active()
                                        ->orderBy('sort_order', 'asc') // Ajoute 'asc' pour satisfaire l'autre classe au cas où
                                        ->pluck('name', 'id')
                                )
                            ->required(),
                        Forms\Components\Select::make('billing_cycle')
                            ->options(['monthly' => 'Mensuel', 'yearly' => 'Annuel'])
                            ->default('monthly')
                            ->required(),
                    ])
                    ->action(function (Subscription $record, array $data) {
                        $plan = Plan::findOrFail($data['plan_id']);
                        app(PaymentService::class)->subscribe(
                            $record->tenant,
                            $plan,
                            $data['billing_cycle']
                        );
                        Notification::make()
                            ->success()
                            ->title('Plan mis à jour — paiement simulé validé')
                            ->send();
                    }),

                // Action : annuler
                Tables\Actions\Action::make('cancel')
                    ->label('Annuler')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Subscription $record) {
                        app(PaymentService::class)->cancel($record);
                        Notification::make()
                            ->warning()
                            ->title('Souscription annulée')
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Supramanager\Resources\SubscriptionResource\Pages\ListSubscriptions::route('/'),
        ];
    }
}