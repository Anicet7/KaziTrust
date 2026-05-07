{{-- resources/views/errors/403.blade.php --}}
@extends('errors.layout')

@section('code', '403')

@section('content')
<div x-data>
    {{-- Icône --}}
    <div class="kz-icon-wrap">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 11V7a4 4 0 0 0-8 0v4M5 11h14a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2zm7 4v2"/>
        </svg>
    </div>

    {{-- Code --}}
    <div class="kz-code" data-code="403">403</div>

    {{-- Titre --}}
    <h1 class="kz-title">
        <span x-show="lang === 'fr'">Accès refusé</span>
        <span x-show="lang === 'en'">Access denied</span>
    </h1>

    {{-- Description --}}
    <p class="kz-desc">
        <span x-show="lang === 'fr'">Vous n'avez pas les permissions nécessaires pour accéder à cette ressource. Contactez votre administrateur si besoin.</span>
        <span x-show="lang === 'en'">You don't have the required permissions to access this resource. Contact your administrator if needed.</span>
    </p>
</div>
@endsection