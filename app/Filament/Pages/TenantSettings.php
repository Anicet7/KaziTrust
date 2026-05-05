<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

use Illuminate\Support\Facades\Auth;

class TenantSettings extends Page implements HasForms
{
    use InteractsWithForms;

   // protected static ?string $navigationIcon = 'heroicon-o-document-text';
   // protected static string $view = 'filament.pages.tenant-settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Paramètres Espace';
    protected static ?string $title = 'Configuration de l\'Entreprise';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 100; // Mettre en bas du menu

    protected static string $view = 'filament.pages.tenant-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Remplir le formulaire avec les données du Tenant connecté
        $this->form->fill(
            Auth::user()->tenant->toArray()
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Réglages')
                    ->tabs([
                        Tabs\Tab::make('Général')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nom de l\'entreprise')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email de facturation')
                                    ->email()
                                    ->required(),
                            ]),
                            
                        Tabs\Tab::make('Facturation & Abonnement')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                TextInput::make('subscription_plan')
                                    ->label('Plan actuel')
                                    ->disabled()
                                    ->helperText('Pour changer de forfait, veuillez contacter le support.'),
                                TextInput::make('trial_ends_at')
                                    ->label('Fin de la période d\'essai')
                                    ->disabled(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            Auth::user()->tenant->update($data);

            Notification::make()
                ->success()
                ->title('Paramètres sauvegardés')
                ->body('Les informations de votre entreprise ont été mises à jour.')
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }


    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }

}