{{-- resources/views/filament/widgets/welcome-banner.blade.php --}}

<style>
/* ── Welcome Banner ── */
.kzwb {
    position: relative;
    border-radius: 16px;
    padding: 2rem 2.5rem;
    overflow: hidden;
    margin-bottom: 0.5rem;
    border: 1px solid #E2E8F0;
    background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 60%, #F0F9FF 100%);
}
.dark .kzwb {
    border-color: rgba(37,99,235,0.18);
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #162032 100%);
}

/* Orbe */
.kzwb-orb {
    position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px; border-radius: 50%;
    pointer-events: none;
    background: radial-gradient(circle, rgba(37,99,235,0.07) 0%, transparent 70%);
}
.dark .kzwb-orb {
    background: radial-gradient(circle, rgba(37,99,235,0.2) 0%, transparent 70%);
}

/* Ligne top */
.kzwb-topline {
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, #2563EB, rgba(37,99,235,0.1), transparent);
}

/* Layout interne */
.kzwb-inner {
    display: flex; align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
    position: relative; z-index: 1;
}

/* Greeting */
.kzwb-greeting {
    font-size: 0.75rem; font-weight: 700;
    letter-spacing: 0.1em; text-transform: uppercase;
    margin-bottom: 0.4rem;
    color: #2563EB;
}

/* Titre */
.kzwb-title {
    font-size: 1.5rem; font-weight: 800;
    letter-spacing: -0.04em; line-height: 1.2;
    margin-bottom: 0.35rem;
    color: #0F172A;
}
.dark .kzwb-title { color: #F1F5F9; }

.kzwb-title-accent { color: #2563EB; }

/* Sous-titre */
.kzwb-sub {
    font-size: 0.85rem; line-height: 1.6;
    color: #64748B;
}
.kzwb-sub-date { color: #94A3B8; }
.dark .kzwb-sub-date { color: #334155; }

/* Badges conteneur */
.kzwb-badges {
    display: flex; align-items: center;
    gap: 0.75rem; flex-wrap: wrap;
}

/* Badge générique */
.kzwb-badge {
    display: flex; align-items: center; gap: 0.5rem;
    border-radius: 10px; padding: 0.55rem 1rem;
    border: 1px solid;
}

/* Badge trial */
.kzwb-badge-trial {
    background: rgba(251,191,36,0.08);
    border-color: rgba(251,191,36,0.25);
}
.dark .kzwb-badge-trial {
    background: rgba(251,191,36,0.08);
    border-color: rgba(251,191,36,0.2);
}
.kzwb-badge-trial-label {
    font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.06em; text-transform: uppercase;
    color: #D97706;
}
.dark .kzwb-badge-trial-label { color: #FBBF24; }
.kzwb-badge-trial-sub {
    font-size: 0.78rem;
    color: #92400E;
}
.dark .kzwb-badge-trial-sub { color: #92400E; }

/* Badge plan actif */
.kzwb-badge-active {
    background: rgba(52,211,153,0.07);
    border-color: rgba(52,211,153,0.2);
}
.dark .kzwb-badge-active {
    background: rgba(52,211,153,0.08);
    border-color: rgba(52,211,153,0.2);
}
.kzwb-badge-active-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #10B981;
    box-shadow: 0 0 6px #10B981;
    display: inline-block;
    animation: kzPulse 2s ease-in-out infinite;
}
.dark .kzwb-badge-active-dot {
    background: #34D399;
    box-shadow: 0 0 8px #34D399;
}
.kzwb-badge-active-label {
    font-size: 0.82rem; font-weight: 700;
    text-transform: capitalize;
    color: #059669;
}
.dark .kzwb-badge-active-label { color: #34D399; }

/* Badge plateforme */
.kzwb-badge-platform {
    background: rgba(37,99,235,0.06);
    border-color: rgba(37,99,235,0.15);
}
.dark .kzwb-badge-platform {
    background: rgba(37,99,235,0.08);
    border-color: rgba(37,99,235,0.18);
}
.kzwb-badge-platform-label {
    font-size: 0.82rem; font-weight: 600;
    color: #2563EB;
}
.dark .kzwb-badge-platform-label { color: #60A5FA; }

@keyframes kzPulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:0.4; transform:scale(0.8); }
}
</style>

<div class="kzwb">
    <div class="kzwb-orb"></div>
    <div class="kzwb-topline"></div>

    <div class="kzwb-inner">

        {{-- Gauche --}}
        <div>
            <p class="kzwb-greeting">
                {{ now()->hour < 12 ? 'Bonjour' : (now()->hour < 18 ? 'Bon après-midi' : 'Bonsoir') }},
                {{ explode(' ', $userName)[0] }} 👋
            </p>

            <h2 class="kzwb-title">
                Espace <span class="kzwb-title-accent">{{ $companyName }}</span>
            </h2>

            <p class="kzwb-sub">
                Tableau de bord · KaziTrust B2B ·
                <span class="kzwb-sub-date">{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
            </p>
        </div>

        {{-- Badges --}}
        <div class="kzwb-badges">

            @if ($onTrial && $daysLeft > 0)
                <div class="kzwb-badge kzwb-badge-trial">
                    <span style="font-size:1rem;">⏳</span>
                    <div>
                        <p class="kzwb-badge-trial-label">Essai gratuit</p>
                        <p class="kzwb-badge-trial-sub">
                            {{ $daysLeft }} jour{{ $daysLeft > 1 ? 's' : '' }} restant{{ $daysLeft > 1 ? 's' : '' }}
                        </p>
                    </div>
                </div>
            @else
                <div class="kzwb-badge kzwb-badge-active">
                    <span class="kzwb-badge-active-dot"></span>
                    <p class="kzwb-badge-active-label">Plan {{ $plan }}</p>
                </div>
            @endif

            <div class="kzwb-badge kzwb-badge-platform">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;">
                    <path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z"
                          stroke="currentColor" stroke-width="1.5" fill="rgba(37,99,235,0.15)"
                          style="color:#2563EB;" class="dark:text-blue-400"/>
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5"
                          stroke-linecap="round" style="color:#2563EB;" class="dark:text-blue-400"/>
                </svg>
                <p class="kzwb-badge-platform-label">KaziTrust · Actif</p>
            </div>

        </div>
    </div>
</div>