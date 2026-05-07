<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\App;

class CustomLogin extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.custom-login';

    /**
     * Le paramètre public pour tracker la locale côté Livewire
     */
    public string $currentLocale = 'fr';

    public function mount(): void
    {
        // Appliquer la locale depuis la session AVANT le mount parent
        // (pour que les labels du formulaire soient dans la bonne langue)
        $savedLocale = session('kz_locale', config('app.locale', 'fr'));
        if (in_array($savedLocale, ['fr', 'en'])) {
            App::setLocale($savedLocale);
            $this->currentLocale = $savedLocale;
        }

        parent::mount();
    }

    /**
     * Change la locale de l'application et recharge la page.
     * Appelé via wire:click depuis le blade.
     */
    public function setLocale(string $locale): void
    {
        if (!in_array($locale, ['fr', 'en'])) {
            return;
        }

        session(['kz_locale' => $locale]);
        App::setLocale($locale);
        $this->currentLocale = $locale;

        // Recharger la page pour appliquer les traductions Filament (labels, boutons, etc.)
       //--- depart $this->redirect(request()->fullUrl());
       /// Mieux  $this->redirect(request()->header('referer') ?? url()->current(), navigate: false);
       ///
        $this->dispatch('refresh-page');
       //-- $this->js('window.location.reload()');
    }



    public function render(): \Illuminate\Contracts\View\View
    {
        // On force la classe 'dark' sur le HTML avant même que Livewire ne finisse le rendu
        $this->js("
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        ");

        return parent::render();
    }


}