{{-- resources/views/errors/404.blade.php --}}
@extends('errors.layout')

@section('code', '404')

@section('content')
<div x-data>
    {{-- Icône --}}
    <div class="kz-icon-wrap">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
    </div>

    {{-- Code --}}
    <div class="kz-code" data-code="404">404</div>

    {{-- Titre --}}
    <h1 class="kz-title">
        <span x-show="lang === 'fr'">Page introuvable</span>
        <span x-show="lang === 'en'">Page not found</span>
    </h1>

    {{-- Description --}}
    <p class="kz-desc">
        <span x-show="lang === 'fr'">La page que vous cherchez n'existe pas ou a été déplacée. Vérifiez l'URL ou revenez à un endroit sûr.</span>
        <span x-show="lang === 'en'">The page you're looking for doesn't exist or has been moved. Check the URL or head back somewhere safe.</span>
    </p>
</div>
@endsection