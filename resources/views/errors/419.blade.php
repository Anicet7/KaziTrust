{{-- resources/views/errors/419.blade.php --}}
@extends('errors.layout')

@section('code', '419')

@section('content')
<div x-data>
    <div class="kz-icon-wrap">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
        </svg>
    </div>

    <div class="kz-code" data-code="419">419</div>

    <h1 class="kz-title">
        <span x-show="lang === 'fr'">Session expirée</span>
        <span x-show="lang === 'en'">Session expired</span>
    </h1>

    <p class="kz-desc">
        <span x-show="lang === 'fr'">Votre session a expiré par mesure de sécurité. Veuillez actualiser la page et vous reconnecter.</span>
        <span x-show="lang === 'en'">Your session has expired for security reasons. Please refresh the page and sign in again.</span>
    </p>

    <div style="margin-bottom: 0.5rem;">
        <a href="javascript:location.reload()" class="kz-btn kz-btn-primary" style="display:inline-flex;gap:0.45rem;align-items:center;padding:0.65rem 1.2rem;border-radius:10px;font-size:0.875rem;font-weight:700;text-decoration:none;background:#2563EB;color:#fff;transition:all 0.2s;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" d="M4 4v5h5M20 20v-5h-5M4 9a9 9 0 0 1 15-3.3M20 15a9 9 0 0 1-15 3.3"/>
            </svg>
            <span x-text="lang === 'fr' ? 'Actualiser' : 'Refresh'"></span>
        </a>
    </div>
</div>
@endsection