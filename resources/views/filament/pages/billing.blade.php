{{-- resources/views/filament/pages/billing.blade.php --}}
<x-filament-panels::page>

    {{-- Souscription actuelle --}}
    @if($this->currentSubscription)
        <x-filament::section>
            <x-slot name="heading">Votre abonnement actuel</x-slot>
            <div class="flex items-center gap-4">
                <x-filament::badge
                    :color="match($this->currentSubscription->status) {
                        'active' => 'success',
                        'trial'  => 'warning',
                        default  => 'gray'
                    }">
                    {{ ucfirst($this->currentSubscription->status) }}
                </x-filament::badge>
                <span class="font-semibold text-lg">
                    {{ $this->currentSubscription->plan->name }}
                </span>
                @if($this->currentSubscription->isOnTrial())
                    <span class="text-sm text-gray-500">
                        Trial jusqu'au {{ $this->currentSubscription->trial_ends_at->format('d/m/Y') }}
                    </span>
                @elseif($this->currentSubscription->ends_at)
                    <span class="text-sm text-gray-500">
                        Expire le {{ $this->currentSubscription->ends_at->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        </x-filament::section>
    @endif

    {{-- Grille des plans --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mt-6">
        @foreach($this->plans as $plan)
            @php
                $isCurrent = $this->currentSubscription?->plan_id === $plan->id
                          && $this->currentSubscription?->canUseApi();
            @endphp

            <x-filament::section :class="$isCurrent ? 'ring-2 ring-primary-500' : ''">
                <x-slot name="heading">{{ $plan->name }}</x-slot>

                <p class="text-gray-500 text-sm mb-4">{{ $plan->description }}</p>

                <div class="text-3xl font-bold mb-1">
                    {{ number_format($plan->price_monthly, 0, '.', ' ') }}
                    <span class="text-base font-normal text-gray-500">XOF/mois</span>
                </div>
                @if($plan->price_yearly > 0)
                    <p class="text-xs text-gray-400 mb-4">
                        ou {{ number_format($plan->price_yearly, 0, '.', ' ') }} XOF/an
                    </p>
                @endif

                <ul class="space-y-2 text-sm mb-6">
                    <li>
                        ✓ {{ $plan->max_apps === -1 ? 'Applications illimitées' : $plan->max_apps . ' application(s)' }}
                    </li>
                    <li>
                        ✓ {{ $plan->max_requests_per_month === -1 ? 'Requêtes illimitées' : number_format($plan->max_requests_per_month) . ' requêtes/mois' }}
                    </li>
                    <li>
                        ✓ {{ $plan->max_users === -1 ? 'Utilisateurs illimités' : $plan->max_users . ' utilisateur(s)' }}
                    </li>
                    @if($plan->hasFeature('webhook'))
                        <li>✓ Webhooks</li>
                    @endif
                    @if($plan->hasFeature('multi_llm'))
                        <li>✓ Multi-LLM (OpenAI, Gemini, Claude)</li>
                    @endif
                    @if($plan->hasFeature('priority_support'))
                        <li>✓ Support prioritaire</li>
                    @endif
                </ul>

                @if($isCurrent)
                    <x-filament::button disabled color="gray" class="w-full">
                        Plan actuel
                    </x-filament::button>
                @else
                    <x-filament::button
                        wire:click="choosePlan({{ $plan->id }}, 'monthly')"
                        class="w-full">
                        Choisir ce plan
                    </x-filament::button>
                @endif
            </x-filament::section>
        @endforeach
    </div>

    {{-- Note prototype --}}
    <x-filament::section class="mt-4 bg-amber-50 dark:bg-amber-900/20">
        <p class="text-sm text-amber-700 dark:text-amber-300">
            <strong>Mode prototype :</strong>
            les paiements sont simulés et validés automatiquement.
            Aucune carte bancaire requise.
        </p>
    </x-filament::section>

</x-filament-panels::page>