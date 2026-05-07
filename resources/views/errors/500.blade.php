{{-- resources/views/errors/500.blade.php --}}
@extends('errors.layout')

@section('code', '500')

@section('content')
<div x-data>
    {{-- Icône --}}
    <div class="kz-icon-wrap">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
    </div>

    {{-- Code --}}
    <div class="kz-code" data-code="500">500</div>

    {{-- Titre --}}
    <h1 class="kz-title">
        <span x-show="lang === 'fr'">Erreur serveur</span>
        <span x-show="lang === 'en'">Server error</span>
    </h1>

    {{-- Description --}}
    <p class="kz-desc">
        <span x-show="lang === 'fr'">Une erreur inattendue s'est produite côté serveur. Nos équipes ont été notifiées. Réessayez dans quelques instants.</span>
        <span x-show="lang === 'en'">An unexpected error occurred on our end. Our team has been notified. Please try again in a few moments.</span>
    </p>
</div>
@endsection