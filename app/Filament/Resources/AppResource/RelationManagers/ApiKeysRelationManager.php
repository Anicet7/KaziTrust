<?php

namespace App\Filament\Resources\AppResource\RelationManagers;

use App\Models\AppApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
 

use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Clipboard;

class ApiKeysRelationManager extends RelationManager
{
    protected static string $relationship = 'apiKeys';
    protected static ?string $title       = 'Clés API';
    protected static ?string $icon        = 'heroicon-o-key';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nom de la clé')
                ->required()
                ->placeholder('Ex: Production, Staging, Dev...')
                ->maxLength(100),

            // La clé est auto-générée via boot() dans le modèle
            // On l'affiche en lecture seule à la création
            Forms\Components\TextInput::make('key')
                ->label('Clé générée')
                ->disabled()
                ->dehydrated(false)
                ->placeholder('Générée automatiquement à la création')
                ->visibleOn('edit')
                ->suffixAction(
                    Action::make('copy')
                        ->icon('heroicon-m-clipboard-document')
                        ->action(function ($state, $livewire) {
                            // On utilise le helper JavaScript de Filament via Livewire
                            $livewire->js("window.navigator.clipboard.writeText('{$state}');");
                            $livewire->dispatch('notify', [
                                'status' => 'success',
                                'message' => 'Copié dans le presse-papier',
                            ]);
                        })
                ),
               // ->copyable(),

            Forms\Components\Toggle::make('is_active')
                ->label('Clé active')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')->searchable()->weight('bold'),

                Tables\Columns\TextColumn::make('key')
                    ->label('Clé API')
                    ->formatStateUsing(fn ($state) =>
                        // Afficher seulement début et fin : kz_AbCd...xYz
                        substr($state, 0, 8) . '...' . substr($state, -4)
                    )
                    ->copyable()
                    ->copyMessage('Clé copiée !')
                    ///->copyStateUsing(fn ($state) => $state) // copie la valeur complète
                    ->fontFamily('mono')
                    ->icon('heroicon-o-key'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')->boolean(),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Dernière utilisation')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Jamais utilisée')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')->dateTime('d/m/Y'),
            ])
            ->headerActions([
                // ✅ Contrôle limite plan
                Tables\Actions\CreateAction::make()
                    ->label('Générer une clé API')
                    ->icon('heroicon-o-plus')
                    ->visible(fn () => in_array(Auth::user()->role, ['admin', 'developer']))
                    ->before(function (Tables\Actions\CreateAction $action) {

                        $app     = $this->getOwnerRecord();
                        $plan    = $app->tenant->currentPlan();
                        $maxKeys = $plan?->max_api_keys_per_app ?? 2;

                        if ($maxKeys !== -1 && $app->apiKeys()->count() >= $maxKeys) {

                            Notification::make()
                                ->warning()
                                ->title('Limite atteinte')
                                ->body("Votre plan autorise {$maxKeys} clé(s) par application.")
                                ->send();
                          // $this->halt();
                            $action->halt();

                            
                            
                        }
                    })
                    ->successNotificationTitle('Clé API générée avec succès !'),
            ])
            ->actions([
                // Révoquer = désactiver sans supprimer (audit)
                Tables\Actions\Action::make('toggle')
                    ->visible(fn () => in_array(Auth::user()->role, ['admin', 'developer']))
                    ->label(fn (AppApiKey $record) => $record->is_active ? 'Révoquer' : 'Réactiver')
                    ->icon(fn (AppApiKey $record) => $record->is_active
                        ? 'heroicon-o-x-circle'
                        : 'heroicon-o-check-circle')
                    ->color(fn (AppApiKey $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (AppApiKey $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->success()
                            ->title($record->is_active ? 'Clé réactivée' : 'Clé révoquée')
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()->label('Supprimer')
                ->visible(fn () => in_array(Auth::user()->role, ['admin', 'developer'])),
            ])
            ->emptyStateHeading('Aucune clé API')
            ->emptyStateDescription('Générez votre première clé pour commencer à appeler l\'API KaziTrust.')
            ->emptyStateIcon('heroicon-o-key');
    }



    public static function canViewForRecord($ownerRecord, $pageClass): bool
    {
        return in_array(Auth::user()->role, ['admin', 'developer']);
    }


}