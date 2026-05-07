{{-- resources/views/errors/429.blade.php --}}
@extends('errors.layout')

@section('code', '429')

@section('content')
<div x-data>
    <div class="kz-icon-wrap">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
        </svg>
    </div>

    <div class="kz-code" data-code="429">429</div>

    <h1 class="kz-title">
        <span x-show="lang === 'fr'">Trop de requêtes</span>
        <span x-show="lang === 'en'">Too many requests</span>
    </h1>

    <p class="kz-desc">
        <span x-show="lang === 'fr'">Vous avez effectué trop de tentatives en peu de temps. Veuillez patienter quelques instants avant de réessayer.</span>
        <span x-show="lang === 'en'">You've made too many attempts in a short time. Please wait a moment before trying again.</span>
    </p>
</div>
@endsection