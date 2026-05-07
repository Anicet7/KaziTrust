{{-- resources/views/errors/503.blade.php --}}
@extends('errors.layout')

@section('code', '503')

@section('content')
<div x-data>
    {{-- Icône --}}
    <div class="kz-icon-wrap">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>
        </svg>
    </div>

    {{-- Code --}}
    <div class="kz-code" data-code="503">503</div>

    {{-- Titre --}}
    <h1 class="kz-title">
        <span x-show="lang === 'fr'">Maintenance en cours</span>
        <span x-show="lang === 'en'">Under maintenance</span>
    </h1>

    {{-- Description --}}
    <p class="kz-desc">
        <span x-show="lang === 'fr'">KaziTrust est temporairement indisponible pour une maintenance planifiée. Nous serons de retour très bientôt.</span>
        <span x-show="lang === 'en'">KaziTrust is temporarily unavailable for scheduled maintenance. We'll be back up very soon.</span>
    </p>

    {{-- Barre de progression animée --}}
    <div style="
        height: 3px;
        background: rgba(37,99,235,0.12);
        border-radius: 99px;
        overflow: hidden;
        margin: 0 auto 0.5rem;
        max-width: 280px;
    ">
        <div style="
            height: 100%;
            background: linear-gradient(90deg, #2563EB, #60A5FA);
            border-radius: 99px;
            animation: maint 2.5s ease-in-out infinite;
        "></div>
    </div>
    <style>
        @keyframes maint {
            0%   { width: 0%; margin-left: 0; }
            50%  { width: 60%; margin-left: 20%; }
            100% { width: 0%; margin-left: 100%; }
        }
    </style>
</div>
@endsection