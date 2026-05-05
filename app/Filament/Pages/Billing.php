<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use App\Services\PaymentService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Billing extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Abonnement & Facturation';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int    $navigationSort  = 99;
    protected static string  $view            = 'filament.pages.billing';

    // Données passées à la vue
    public function getPlansProperty()
    {
       // return Plan::active()->public()->orderBy('sort_order')->get();
        return \App\Models\Plan::query()
            ->active()
            ->public()
            ->orderBy('sort_order', 'asc') // On force le 2ème argument 'asc'
            ->get();
    }

    public function getCurrentSubscriptionProperty()
    {
        return Auth::user()->tenant->activeSubscription;
    }

    /**
     * Appelé quand le tenant clique "Choisir ce plan"
     */
    public function choosePlan(int $planId, string $cycle = 'monthly'): void
    {
        $plan   = Plan::findOrFail($planId);
        $tenant = Auth::user()->tenant;

        // Vérifier qu'il ne souscrit pas au même plan actif
        $current = $tenant->activeSubscription;
        if ($current && $current->plan_id === $planId && $current->isActive()) {
            Notification::make()
                ->warning()
                ->title('Vous êtes déjà sur ce plan.')
                ->send();
            return;
        }

        app(PaymentService::class)->subscribe($tenant, $plan, $cycle);

        Notification::make()
            ->success()
            ->title('🎉 Souscription activée !')
            ->body('Votre plan ' . $plan->name . ' est maintenant actif.')
            ->send();

        // Refresh la page
        $this->redirect(static::getUrl());
    }

    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }

}