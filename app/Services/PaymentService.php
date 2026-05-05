<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Point d'entrée unique pour souscrire à un plan.
     * En mode prototype : validation automatique immédiate.
     * En production : remplacer processPayment() par l'appel FedaPay/Stripe.
     */
    public function subscribe(Tenant $tenant, Plan $plan, string $billingCycle = 'monthly'): Subscription
    {
        return DB::transaction(function () use ($tenant, $plan, $billingCycle) {

            // 1. Expirer toutes les souscriptions actives du tenant
            $tenant->subscriptions()
                ->whereIn('status', ['active', 'trial'])
                ->update(['status' => 'expired']);

            // 2. Calculer le prix selon le cycle
            $price = $billingCycle === 'yearly' ? $plan->price_yearly : $plan->price_monthly;
            $endsAt = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

            // 3. Créer la nouvelle souscription (en attente de paiement)
            $subscription = $tenant->subscriptions()->create([
                'plan_id'       => $plan->id,
                'status'        => 'active', // AUTO-VALIDÉ en prototype
                'starts_at'     => now(),
                'ends_at'       => $endsAt,
                'billing_cycle' => $billingCycle,
                'price_paid'    => $price,
                'currency'      => $plan->currency,
                'payment_provider'    => 'prototype',
                'payment_provider_id' => 'PROTO-' . strtoupper(uniqid()),
            ]);

            // 4. Créer le paiement (auto-validé)
            $this->processPayment($subscription, $price, $plan->currency);

            return $subscription;
        });
    }

    /**
     * En prototype : paiement immédiatement "completed".
     * En production : appeler FedaPay ou Stripe ici et retourner
     * un payment_url de redirection. Le statut passera à 'completed'
     * via le webhook du provider.
     */
    private function processPayment(Subscription $subscription, float $amount, string $currency): Payment
    {
        return Payment::create([
            'subscription_id'        => $subscription->id,
            'tenant_id'              => $subscription->tenant_id,
            'amount'                 => $amount,
            'currency'               => $currency,
            'status'                 => 'completed',   // ← Toujours completed en prototype
            'provider'               => 'prototype',
            'provider_transaction_id'=> 'PROTO-' . strtoupper(uniqid()),
            'provider_response'      => ['note' => 'Paiement simulé — mode prototype'],
            'description'            => 'Souscription plan ' . $subscription->plan->name,
            'paid_at'                => now(),
        ]);
    }

    /**
     * Annuler une souscription
     */
    public function cancel(Subscription $subscription): void
    {
        $subscription->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}