{{-- resources/views/filament/widgets/subscription.blade.php --}}
<x-filament::widget>
    <x-filament::section>
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-sm text-gray-500">Plan actuel</p>
                <p class="text-xl font-semibold">{{ $plan?->name ?? 'Aucun plan' }}</p>
                @if($sub?->isOnTrial())
                    <p class="text-sm text-amber-600">
                        Trial — expire le {{ $sub->trial_ends_at->format('d/m/Y') }}
                    </p>
                @elseif($sub?->ends_at)
                    <p class="text-sm text-gray-500">
                        Renouvellement le {{ $sub->ends_at->format('d/m/Y') }}
                    </p>
                @endif
            </div>

            <div class="text-right">
                <p class="text-sm text-gray-500">Quota ce mois</p>
                <p class="text-xl font-semibold">
                    {{ $used }} / {{ $limit === -1 ? '∞' : $limit }}
                </p>
                @if($limit !== -1)

                <!-- 
                    <div class="w-48 bg-gray-200 rounded-full h-2 mt-1">
                        <div class="h-2 rounded-full {{ $percent > 90 ? 'bg-red-500' : ($percent > 70 ? 'bg-amber-400' : 'bg-green-500') }}"
                             style="width: {{ $percent }}%"></div>
                    </div>
                -->

                    <div
                        @style([
                            "width: {$percent}%"
                        ])
                        class="h-2 rounded-full {{ $percent > 90 ? 'bg-red-500' : ($percent > 70 ? 'bg-amber-400' : 'bg-green-500') }}">
                     </div>


                @endif


                

            </div>

            <x-filament::button
                href="{{ route('filament.management.pages.billing') }}"
                tag="a"
                color="primary"
                size="sm">
                Gérer l'abonnement
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament::widget>