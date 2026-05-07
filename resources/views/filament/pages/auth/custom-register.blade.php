{{--
    resources/views/filament/pages/auth/custom-register.blade.php
--}}

@php $locale = $currentLocale ?? session('kz_locale', 'fr'); @endphp

<div
    class="kz-reg"
    x-data="{
        lang: '{{ $locale }}',
        t: {
            fr: {
                headline:   'Créez votre espace',
                sub:        '14 jours gratuits — aucune carte bancaire requise.',
                company:    'Entreprise',
                already:    'Déjà un compte ?',
                signin:     'Se connecter',
                back:       'Retour au site',
                badge:      'Inscription gratuite',
                tagline1:   'Démarrez en quelques',
                tagline2:   'minutes, pas en semaines.',
                desc:       'Configurez votre espace KaziTrust, intégrez vos apps et analysez la confiance en temps réel.',
                step1:      'Créez votre compte',
                step2:      'Connectez vos apps',
                step3:      'Analysez & décidez',
            },
            en: {
                headline:   'Create your workspace',
                sub:        '14-day free trial — no credit card required.',
                company:    'Company',
                already:    'Already have an account?',
                signin:     'Sign in',
                back:       'Back to website',
                badge:      'Free sign up',
                tagline1:   'Get started in minutes,',
                tagline2:   'not weeks.',
                desc:       'Set up your KaziTrust workspace, connect your apps and analyse trust in real time.',
                step1:      'Create your account',
                step2:      'Connect your apps',
                step3:      'Analyse & decide',
            }
        }
    }"
>

{{-- ═══════════════════════════════════════════════════════
     STYLES  (à l'intérieur du root element unique)
═══════════════════════════════════════════════════════ --}}
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap');

html, body {
    background: #0F172A !important;
    min-height: 100vh;
    margin: 0 !important; padding: 0 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

/* Neutralise wrappers Filament */
.fi-simple-page, .fi-simple-layout, .fi-simple-main-ctn { display: contents !important; background: transparent !important; padding: 0 !important; }
.fi-simple-main { background: transparent !important; padding: 0 !important; border: none !important; box-shadow: none !important; width: 100% !important; max-width: 100% !important; }

/* ── Layout ────────────────────────────────────────────── */
.kz-reg {
    display: flex;
    min-height: 100vh;
    background: #0F172A;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* ── Panneau gauche ────────────────────────────────────── */
.kz-reg-visual {
    display: none;
    flex: 1;
    position: relative;
    overflow: hidden;
    background: #080E1A;

    /* AJOUTER CES 3 LIGNES */
     /* align-items: center;      /* Centre verticalement */
    justify-content: center;   /* Centre horizontalement */
    padding: 3rem;
    min-height: 100vh;

}
@media (min-width: 1024px) { .kz-reg-visual { display: flex; flex-direction: column; } }

.kz-orb { position: absolute; border-radius: 50%; filter: blur(90px); pointer-events: none; }
.kz-orb-a { width: 500px; height: 500px; background: radial-gradient(circle, #2563EB 0%, transparent 70%); top: -20%; left: -10%; opacity: 0.2; animation: drift 11s ease-in-out infinite; }
.kz-orb-b { width: 320px; height: 320px; background: radial-gradient(circle, #1D4ED8 0%, transparent 70%); bottom: 10%; right: -5%; opacity: 0.18; animation: drift 15s ease-in-out infinite reverse; }
.kz-orb-c { width: 200px; height: 200px; background: radial-gradient(circle, #3B82F6 0%, transparent 70%); top: 50%; left: 35%; opacity: 0.15; animation: drift 12s ease-in-out infinite 4s; }
@keyframes drift { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(30px,-20px) scale(1.05)} 66%{transform:translate(-20px,28px) scale(0.95)} }

.kz-dots {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, rgba(37,99,235,0.22) 1px, transparent 1px);
    background-size: 36px 36px;
    opacity: 0.55;
}

/* Étapes */
.kz-steps-wrap {
   /* position: absolute;
    bottom: 3rem; left: 3rem; right: 3rem;
    z-index: 4;
    */

    position: relative; /* CHANGÉ de absolute à relative */
    z-index: 4;
    width: 100%;       /* Assure que le contenu prend la largeur nécessaire */
    max-width: 450px;  /* Optionnel : pour éviter que le texte soit trop large */
    /* Retirer bottom: 3rem; left: 3rem; right: 3rem; */

}
.kz-visual-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(37,99,235,0.12); border: 1px solid rgba(37,99,235,0.28);
    border-radius: 100px; padding: 0.3rem 0.85rem; margin-bottom: 1.25rem;
}
.kz-vbadge-dot { width: 7px; height: 7px; background: #2563EB; border-radius: 50%; box-shadow: 0 0 8px #2563EB; animation: blink 2s ease-in-out infinite; }
@keyframes blink { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(0.85)} }
.kz-vbadge-txt { font-size: 0.72rem; font-weight: 700; color: #60A5FA; letter-spacing: 0.1em; text-transform: uppercase; }

.kz-tagline { font-size: clamp(1.5rem, 2.4vw, 2.1rem); font-weight: 800; color: #F1F5F9; line-height: 1.15; letter-spacing: -0.04em; margin-bottom: 0.85rem; }
.kz-tagline em { font-style: normal; background: linear-gradient(135deg, #2563EB, #60A5FA); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.kz-vdesc { color: #475569; font-size: 0.875rem; line-height: 1.65; max-width: 340px; margin-bottom: 2rem; }

/* Steps list */
.kz-steps { display: flex; flex-direction: column; gap: 0.85rem; }
.kz-step {
    display: flex; align-items: center; gap: 0.85rem;
    background: rgba(30,41,59,0.5); border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px; padding: 0.75rem 1rem;
    backdrop-filter: blur(8px);
}
.kz-step-num {
    width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
    background: linear-gradient(135deg, #2563EB, #1D4ED8);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; font-weight: 800; color: white;
    box-shadow: 0 0 12px rgba(37,99,235,0.4);
}
.kz-step-label { font-size: 0.82rem; font-weight: 600; color: #94A3B8; }

/* ── Panneau formulaire ────────────────────────────────── */
.kz-reg-form-side {
    flex: 0 0 100%;
    display: flex; flex-direction: column;
    justify-content: center; align-items: center;
    padding: 2rem 1.5rem 5rem;
    position: relative;
    background: #0F172A;
}
@media (min-width: 1024px) {
    .kz-reg-form-side { flex: 0 0 490px; border-left: 1px solid rgba(255,255,255,0.05); }
}

/* Langue */
.kz-lang {
    position: absolute; top: 1.5rem; right: 1.5rem;
    display: flex; align-items: center; gap: 0.25rem;
    background: rgba(30,41,59,0.7); border: 1px solid rgba(255,255,255,0.07);
    border-radius: 10px; padding: 0.25rem;
    backdrop-filter: blur(8px);
}
.kz-lang-a {
    display: block; padding: 0.3rem 0.7rem;
    border-radius: 7px; font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.1em; text-decoration: none;
    color: #475569; transition: all 0.2s;
}
.kz-lang-a:hover { color: #94A3B8; }
.kz-lang-a.is-active { background: rgba(37,99,235,0.2); color: #60A5FA; border: 1px solid rgba(37,99,235,0.35); }

/* Boîte */
.kz-reg-box { width: 100%; max-width: 400px; }

/* Brand */
.kz-brand { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2rem; }
.kz-brand-icon {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
    border-radius: 11px; display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 25px rgba(37,99,235,0.4), 0 4px 12px rgba(0,0,0,0.3); flex-shrink: 0;
}
.kz-brand-name { font-family: 'JetBrains Mono', monospace; font-size: 1.15rem; font-weight: 700; color: #E2E8F0; letter-spacing: -0.02em; }
.kz-brand-name span { color: #2563EB; }

/* Trial badge */
.kz-trial {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2);
    border-radius: 8px; padding: 0.4rem 0.85rem; margin-bottom: 1.5rem;
}
.kz-trial-icon { font-size: 0.9rem; }
.kz-trial-txt { font-size: 0.75rem; font-weight: 600; color: #34D399; letter-spacing: 0.02em; }

.kz-reg-title { font-size: 1.65rem; font-weight: 800; color: #F1F5F9; letter-spacing: -0.04em; line-height: 1.15; margin: 0 0 0.4rem; }
.kz-reg-sub { font-size: 0.85rem; color: #64748B; margin: 0 0 1.75rem; }

.kz-divider { height: 1px; background: linear-gradient(90deg, transparent, rgba(37,99,235,0.22), transparent); margin-bottom: 1.5rem; }

/* ── Inputs Filament ────────────────────────────────────── */
.fi-input-wrp, .fi-input-wrapper { background: transparent !important; }
.fi-input-wrp input, .fi-input {
    background: #1E293B !important;
    border: 1px solid rgba(148,163,184,0.12) !important;
    color: #E2E8F0 !important;
    border-radius: 10px !important;
    padding: 0.68rem 1rem !important;
    font-size: 0.875rem !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    width: 100% !important; box-sizing: border-box !important;
}
.fi-input-wrp input:focus, .fi-input:focus {
    border-color: rgba(37,99,235,0.6) !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12), 0 0 15px rgba(37,99,235,0.08) !important;
    outline: none !important;
}
.fi-input-wrp input::placeholder { color: #334155 !important; }

/* Labels */
.fi-fo-field-wrp > label, .fi-label, label[for] {
    color: #94A3B8 !important; font-size: 0.75rem !important;
    font-weight: 600 !important; letter-spacing: 0.06em !important;
    text-transform: uppercase !important; font-family: 'Plus Jakarta Sans', sans-serif !important;
}

/* Espacement champs */
.fi-form .fi-fo-component-ctn .fi-fo-field-wrp + .fi-fo-field-wrp,
.fi-form > .fi-fo-component-ctn > * + * { margin-top: 1rem !important; }

/* Erreurs */
.fi-fo-field-wrp-validation-error, p[id$="-error"] { color: #F87171 !important; font-size: 0.75rem !important; }

/* ── Bouton ─────────────────────────────────────────────── */
.fi-form-actions, .fi-ac-actions { margin-top: 1.5rem !important; }
.fi-btn, .fi-form-actions button, button[type="submit"] {
    background: #2563EB !important;
    border: 1px solid #2563EB !important;
    color: #FFFFFF !important;
    border-radius: 10px !important;
    padding: 0.75rem 1.5rem !important;
    font-size: 0.9rem !important; font-weight: 700 !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    width: 100% !important; cursor: pointer !important;
    transition: all 0.25s ease !important;
    box-shadow: 0 0 20px rgba(37,99,235,0.3), 0 4px 15px rgba(37,99,235,0.2), 0 1px 3px rgba(0,0,0,0.4) !important;
    position: relative !important; overflow: hidden !important;
}
.fi-btn::before, button[type="submit"]::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, transparent 60%);
    pointer-events: none;
}
.fi-btn:hover, button[type="submit"]:hover {
    background: #1D4ED8 !important; border-color: #1D4ED8 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 0 40px rgba(37,99,235,0.5), 0 8px 25px rgba(37,99,235,0.3), 0 2px 8px rgba(0,0,0,0.4) !important;
}
.fi-btn:active, button[type="submit"]:active {
    transform: translateY(0) !important;
    box-shadow: 0 0 20px rgba(37,99,235,0.3), 0 2px 8px rgba(0,0,0,0.4) !important;
}

/* Liens secondaires */
.fi-link, .fi-btn-color-gray, a.text-sm { color: #475569 !important; font-size: 0.78rem !important; transition: color 0.2s !important; }
.fi-link:hover { color: #94A3B8 !important; }

/* Login link */
.kz-login-link { text-align: center; margin-top: 1.5rem; font-size: 0.82rem; color: #475569; }
.kz-login-link a { color: #2563EB; font-weight: 600; text-decoration: none; margin-left: 0.25rem; transition: color 0.2s; }
.kz-login-link a:hover { color: #60A5FA; text-decoration: underline; }

/* Back link */
.kz-back {
    position: absolute; bottom: 1.75rem; left: 50%; transform: translateX(-50%);
    color: #334155; font-size: 0.78rem; font-weight: 500;
    text-decoration: none; display: flex; align-items: center; gap: 0.4rem;
    white-space: nowrap; transition: color 0.2s; letter-spacing: 0.01em;
}
.kz-back:hover { color: #64748B; }
.kz-back svg { transition: transform 0.2s; }
.kz-back:hover svg { transform: translateX(-3px); }


/* ── Mode sombre ── */
.dark html, .dark body          { background: #0F172A !important; }
.dark .kz-reg                   { background: #0F172A; }
.dark .kz-reg-form-side         { background: #0F172A; }
.dark .kz-reg-form-side         { border-left-color: rgba(255,255,255,0.05) !important; }
.dark .kz-lang                  { background: rgba(30,41,59,0.7); border-color: rgba(255,255,255,0.07); }
.dark .kz-brand-name            { color: #E2E8F0; }
.dark .kz-reg-title             { color: #F1F5F9; }
.dark .fi-input-wrp input,
.dark .fi-input                 { background: #1E293B !important; border-color: rgba(148,163,184,0.12) !important; color: #E2E8F0 !important; }
.dark .fi-input-wrp input::placeholder { color: #334155 !important; }
.dark .fi-fo-field-wrp > label,

.dark .kz-back                  { color: #334155; }


</style>

{{-- ═══════════════════════════════════════════════════════
     PANNEAU GAUCHE  — Visuel abstrait
═══════════════════════════════════════════════════════ --}}
<div class="kz-reg-visual">
    <div class="kz-orb kz-orb-a"></div>
    <div class="kz-orb kz-orb-b"></div>
    <div class="kz-orb kz-orb-c"></div>
    <div class="kz-dots"></div>

    {{-- SVG réseau ──────────────────────────────────── --}}
    <svg style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;"
         viewBox="0 0 640 900" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
        <defs>
            <filter id="rglow"><feGaussianBlur stdDeviation="3" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
        </defs>

        {{-- Cercles entrelacés --}}
        <g fill="none" stroke="#2563EB" stroke-width="0.8" opacity="0.18">
            <circle cx="200" cy="200" r="120"><animateTransform attributeName="transform" type="rotate" from="0 200 200" to="360 200 200" dur="30s" repeatCount="indefinite"/></circle>
            <circle cx="200" cy="200" r="180" stroke-dasharray="12 8"><animateTransform attributeName="transform" type="rotate" from="360 200 200" to="0 200 200" dur="45s" repeatCount="indefinite"/></circle>
            <circle cx="440" cy="650" r="100"><animateTransform attributeName="transform" type="rotate" from="0 440 650" to="360 440 650" dur="25s" repeatCount="indefinite"/></circle>
            <circle cx="440" cy="650" r="155" stroke-dasharray="8 12"><animateTransform attributeName="transform" type="rotate" from="360 440 650" to="0 440 650" dur="38s" repeatCount="indefinite"/></circle>
        </g>

        {{-- Hexagones --}}
        <g fill="none" stroke="#3B82F6" stroke-width="1.1">
            <polygon points="130,80 175,106 175,158 130,184 85,158 85,106" opacity="0.45">
                <animateTransform attributeName="transform" type="translate" values="0,0;10,-14;0,0" dur="9s" repeatCount="indefinite"/>
            </polygon>
            <polygon points="460,120 505,148 505,200 460,228 415,200 415,148" opacity="0.35">
                <animateTransform attributeName="transform" type="translate" values="0,0;-12,8;0,0" dur="12s" repeatCount="indefinite"/>
            </polygon>
            <polygon points="310,380 355,408 355,460 310,488 265,460 265,408" opacity="0.5">
                <animateTransform attributeName="transform" type="translate" values="0,0;8,10;0,0" dur="10s" repeatCount="indefinite"/>
            </polygon>
            <polygon points="100,560 145,588 145,640 100,668 55,640 55,588" opacity="0.3">
                <animateTransform attributeName="transform" type="translate" values="0,0;-8,-12;0,0" dur="14s" repeatCount="indefinite"/>
            </polygon>
            <polygon points="520,520 565,548 565,600 520,628 475,600 475,548" opacity="0.4">
                <animateTransform attributeName="transform" type="translate" values="0,0;14,-6;0,0" dur="8s" repeatCount="indefinite"/>
            </polygon>
        </g>

        {{-- Lignes de connexion --}}
        <g stroke="#3B82F6" stroke-width="0.5" opacity="0.14">
            <line x1="130" y1="132" x2="310" y2="434"/><line x1="460" y1="174" x2="310" y2="434"/>
            <line x1="130" y1="132" x2="460" y2="174"/><line x1="310" y1="434" x2="100" y2="614"/>
            <line x1="310" y1="434" x2="520" y2="574"/><line x1="100" y1="614" x2="520" y2="574"/>
        </g>

        {{-- Noeuds --}}
        <g filter="url(#rglow)" fill="#2563EB">
            <circle cx="130" cy="132" r="4"><animate attributeName="r" values="4;7;4" dur="3.5s" repeatCount="indefinite"/><animate attributeName="opacity" values="0.9;0.4;0.9" dur="3.5s" repeatCount="indefinite"/></circle>
            <circle cx="460" cy="174" r="3"><animate attributeName="r" values="3;5.5;3" dur="5s" repeatCount="indefinite" begin="1s"/></circle>
            <circle cx="310" cy="434" r="5"><animate attributeName="r" values="5;9;5" dur="4s" repeatCount="indefinite"/><animate attributeName="opacity" values="1;0.3;1" dur="4s" repeatCount="indefinite"/></circle>
            <circle cx="100" cy="614" r="3.5"><animate attributeName="r" values="3.5;6;3.5" dur="6s" repeatCount="indefinite" begin="2s"/></circle>
            <circle cx="520" cy="574" r="3"><animate attributeName="r" values="3;5;3" dur="7s" repeatCount="indefinite"/></circle>
        </g>
    </svg>

    {{-- Contenu bas --}}
    <div class="kz-steps-wrap">
        <div class="kz-visual-badge">
            <div class="kz-vbadge-dot"></div>
            <span class="kz-vbadge-txt" x-text="t[lang].badge"></span>
        </div>
        <div class="kz-tagline">
            <span x-text="t[lang].tagline1"></span><br>
            <em x-text="t[lang].tagline2"></em>
        </div>
        <p class="kz-vdesc" x-text="t[lang].desc"></p>
        <div class="kz-steps">
            <div class="kz-step"><div class="kz-step-num">01</div><span class="kz-step-label" x-text="t[lang].step1"></span></div>
            <div class="kz-step"><div class="kz-step-num">02</div><span class="kz-step-label" x-text="t[lang].step2"></span></div>
            <div class="kz-step"><div class="kz-step-num">03</div><span class="kz-step-label" x-text="t[lang].step3"></span></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     PANNEAU DROIT  — Formulaire
═══════════════════════════════════════════════════════ --}}
<div class="kz-reg-form-side">

    {{-- Sélecteur de langue --}}
    <div class="kz-lang">
        <a href="{{ filament()->getRegistrationUrl() }}?lang=fr"
           class="kz-lang-a {{ $locale === 'fr' ? 'is-active' : '' }}"
           @click="lang = 'fr'">FR</a>
        <a href="{{ filament()->getRegistrationUrl() }}?lang=en"
           class="kz-lang-a {{ $locale === 'en' ? 'is-active' : '' }}"
           @click="lang = 'en'">EN</a>
    </div>

    <div class="kz-reg-box">

        {{-- Brand --}}
        <div class="kz-brand">
            <div class="kz-brand-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z"
                          stroke="white" stroke-width="1.5" fill="rgba(255,255,255,0.12)"/>
                    <path d="M9 12L11 14L15 10" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="kz-brand-name">Kazi<span>Trust</span></div>
        </div>

        {{-- Trial badge --}}
        <div class="kz-trial">
            <span class="kz-trial-icon">🎁</span>
            <span class="kz-trial-txt" x-text="lang === 'fr' ? '14 jours d\'essai gratuit · Aucune carte requise' : '14-day free trial · No card required'"></span>
        </div>

        {{-- Titre --}}
        <h1 class="kz-reg-title" x-text="t[lang].headline"></h1>
        <p class="kz-reg-sub" x-text="t[lang].sub"></p>

        <div class="kz-divider"></div>

        {{-- Formulaire Filament --}}
        <form wire:submit.prevent="register">
            {{ $this->form }}
            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </form>

        {{-- Lien connexion --}}
        @if (filament()->hasLogin())
            <div class="kz-login-link">
                <span x-text="t[lang].already"></span>
                <a href="{{ filament()->getLoginUrl() }}?lang={{ $locale }}" x-text="t[lang].signin"></a>
            </div>
        @endif

    </div>

    {{-- Lien retour --}}
    <a href="/" class="kz-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        <span x-text="t[lang].back"></span>
    </a>
</div>

</div>{{-- fin .kz-reg (root unique) --}}