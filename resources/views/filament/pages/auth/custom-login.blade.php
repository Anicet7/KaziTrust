{{--
    resources/views/filament/pages/auth/custom-login.blade.php
    Vue de connexion personnalisée KaziTrust
--}}

@php
    $locale = $currentLocale ?? session('kz_locale', config('app.locale', 'fr'));
@endphp

{{-- ============================================================
     STRUCTURE HTML
     ============================================================ --}}
<div
    class="kz-login"
    x-data="{
        lang: '{{ $locale }}',
        labels: {
            fr: {
                welcome:   'Bon retour',
                subtitle:  'Connectez-vous à votre espace KaziTrust',
                back:      'Retour au site',
                newHere:   'Nouveau sur KaziTrust ?',
                register:  'Créer un compte',
                tagline1:  'La confiance financière,',
                tagline2:  'réinventée par l\'IA.',
                desc:      'Analyse de risque en temps réel, propulsée par l\'IA — pour des décisions B2B plus sécurisées.',
                live:      'Plateforme active',
            },
            en: {
                welcome:   'Welcome back',
                subtitle:  'Sign in to your KaziTrust workspace',
                back:      'Back to website',
                newHere:   'New to KaziTrust?',
                register:  'Create an account',
                tagline1:  'Financial trust,',
                tagline2:  'reimagined with AI.',
                desc:      'Real-time risk analysis powered by AI — for safer, smarter B2B decisions.',
                live:      'Platform live',
            }
        }
    }"
>




    {{-- ============================================================
        STYLES GLOBAUX
        ============================================================ --}}
    <style>
        /* ── Fonts ────────────────────────────────────────────────── */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap');

        /* ── Reset / Override Filament body ───────────────────────── */
        html, body {
            background: #0F172A !important;
            min-height: 100vh;
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* Neutralise le wrapper simple-page de Filament */
        .fi-simple-page,
        .fi-simple-layout,
        .fi-simple-main-ctn {
            display: contents !important;
            background: transparent !important;
            padding: 0 !important;
            min-height: unset !important;
        }
        .fi-simple-main {
            background: transparent !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* ── Layout principal ──────────────────────────────────────── */
        .kz-login {
            display: flex;
            min-height: 100vh;
            background: #0F172A;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── Panneau visuel gauche ─────────────────────────────────── */
        .kz-visual {
            display: none;
            flex: 1;
            position: relative;
            overflow: hidden;
            background: #080E1A;
        }
        @media (min-width: 1024px) {
            .kz-visual { display: flex; flex-direction: column; }
        }

        /* Orbes ambients animés */
        .kz-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
        }
        .kz-orb-1 {
            width: 480px; height: 480px;
            background: radial-gradient(circle, #2563EB 0%, transparent 70%);
            top: -15%; left: -15%;
            opacity: 0.25;
            animation: orbDrift 10s ease-in-out infinite;
        }
        .kz-orb-2 {
            width: 360px; height: 360px;
            background: radial-gradient(circle, #1D4ED8 0%, transparent 70%);
            bottom: 5%; right: -8%;
            opacity: 0.2;
            animation: orbDrift 14s ease-in-out infinite reverse;
        }
        .kz-orb-3 {
            width: 220px; height: 220px;
            background: radial-gradient(circle, #3B82F6 0%, transparent 70%);
            top: 45%; left: 38%;
            opacity: 0.18;
            animation: orbDrift 11s ease-in-out infinite 3s;
        }
        @keyframes orbDrift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%  { transform: translate(40px, -25px) scale(1.06); }
            66%  { transform: translate(-25px, 35px) scale(0.94); }
        }

        /* Grille pointillée */
        .kz-grid {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle, rgba(37,99,235,0.25) 1px, transparent 1px);
            background-size: 36px 36px;
            opacity: 0.6;
        }

        /* Contenu bas du panneau visuel */
        .kz-visual-content {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            
            /* Conservez vos autres propriétés si nécessaire */
            left: 3rem; 
            right: 3rem;
            z-index: 4;
        }
        
        .kz-visual-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(37,99,235,0.15);
            border: 1px solid rgba(37,99,235,0.3);
            border-radius: 100px;
            padding: 0.3rem 0.85rem;
            margin-bottom: 1.25rem;
        }
        .kz-visual-badge-dot {
            width: 7px; height: 7px;
            background: #2563EB;
            border-radius: 50%;
            box-shadow: 0 0 8px #2563EB;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.85); }
        }
        .kz-visual-badge-text {
            font-size: 0.72rem;
            font-weight: 600;
            color: #60A5FA;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .kz-visual-tagline {
            font-size: clamp(1.6rem, 2.5vw, 2.2rem);
            font-weight: 800;
            color: #F1F5F9;
            line-height: 1.15;
            letter-spacing: -0.04em;
            margin-bottom: 1rem;
        }
        .kz-visual-tagline em {
            font-style: normal;
            background: linear-gradient(135deg, #2563EB, #60A5FA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .kz-visual-desc {
            color: #475569;
            font-size: 0.875rem;
            line-height: 1.65;
            max-width: 340px;
        }

        /* ── Panneau formulaire droit ──────────────────────────────── */
        .kz-form-side {
            flex: 0 0 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1.5rem 5rem;
            position: relative;
            background: #0F172A;
        }
        @media (min-width: 1024px) {
            .kz-form-side {
                flex: 0 0 460px;
                border-left: 1px solid rgba(255,255,255,0.05);
            }
        }

        /* Sélecteur de langue */
        .kz-lang-switch {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            background: rgba(30,41,59,0.7);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 10px;
            padding: 0.25rem;
            backdrop-filter: blur(8px);
        }
        .kz-lang-btn {
            padding: 0.3rem 0.7rem;
            border-radius: 7px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #475569;
            transition: all 0.2s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
            line-height: 1.4;
        }
        .kz-lang-btn:hover {
            color: #94A3B8;
        }
        .kz-lang-btn.is-active {
            background: rgba(37,99,235,0.2);
            color: #60A5FA;
            border: 1px solid rgba(37,99,235,0.35);
        }

        /* Boîte du formulaire */
        .kz-form-box {
            width: 100%;
            max-width: 380px;
        }

        /* Brand */
        .kz-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
        }
        .kz-brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 25px rgba(37,99,235,0.4), 0 4px 12px rgba(0,0,0,0.3);
            flex-shrink: 0;
        }
        .kz-brand-name {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.15rem;
            font-weight: 700;
            color: #E2E8F0;
            letter-spacing: -0.02em;
        }
        .kz-brand-name span {
            color: #2563EB;
        }

        /* Titre et sous-titre */
        .kz-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #F1F5F9;
            letter-spacing: -0.04em;
            line-height: 1.15;
            margin: 0 0 0.5rem;
        }
        .kz-subtitle {
            font-size: 0.875rem;
            color: #64748B;
            margin: 0 0 2rem;
            line-height: 1.5;
        }

        /* Séparateur */
        .kz-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(37,99,235,0.25), transparent);
            margin-bottom: 1.75rem;
        }

        /* ── Override des inputs Filament ───────────────────────── */
        .fi-input-wrp,
        .fi-input-wrapper {
            background: transparent !important;
        }
        .fi-input-wrp input,
        .fi-input {
            background: #1E293B !important;
            border: 1px solid rgba(148,163,184,0.12) !important;
            color: #E2E8F0 !important;
            border-radius: 10px !important;
            padding: 0.72rem 1rem !important;
            font-size: 0.875rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .fi-input-wrp input:focus,
        .fi-input:focus {
            border-color: rgba(37,99,235,0.6) !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12), 0 0 15px rgba(37,99,235,0.08) !important;
            outline: none !important;
        }
        .fi-input-wrp input::placeholder {
            color: #334155 !important;
        }

        /* Labels */
        .fi-fo-field-wrp > label,
        .fi-label,
        label[for] {
            color: #94A3B8 !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            letter-spacing: 0.06em !important;
            text-transform: uppercase !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* Erreurs de validation */
        .fi-fo-field-wrp-validation-error,
        p[id$="-error"] {
            color: #F87171 !important;
            font-size: 0.75rem !important;
        }

        /* Espacement entre champs */
        .fi-form .fi-fo-component-ctn .fi-fo-field-wrp + .fi-fo-field-wrp,
        .fi-form > .fi-fo-component-ctn > * + * {
            margin-top: 1.1rem !important;
        }

        /* ── Bouton de soumission ─────────────────────────────────── */
        .fi-form-actions,
        .fi-ac-actions {
            margin-top: 1.5rem !important;
        }

        .fi-btn,
        .fi-form-actions button,
        button[type="submit"] {
            background: #2563EB !important;
            border: 1px solid #2563EB !important;
            color: #FFFFFF !important;
            border-radius: 10px !important;
            padding: 0.75rem 1.5rem !important;
            font-size: 0.9rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.01em !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            width: 100% !important;
            cursor: pointer !important;
            transition: all 0.25s ease !important;
            box-shadow:
                0 0 20px rgba(37,99,235,0.3),
                0 4px 15px rgba(37,99,235,0.2),
                0 1px 3px rgba(0,0,0,0.4) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        .fi-btn::before,
        button[type="submit"]::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.08) 0%, transparent 60%);
            pointer-events: none;
        }
        .fi-btn:hover,
        .fi-form-actions button:hover,
        button[type="submit"]:hover {
            background: #1D4ED8 !important;
            border-color: #1D4ED8 !important;
            transform: translateY(-1px) !important;
            box-shadow:
                0 0 40px rgba(37,99,235,0.5),
                0 8px 25px rgba(37,99,235,0.3),
                0 2px 8px rgba(0,0,0,0.4) !important;
        }
        .fi-btn:active,
        button[type="submit"]:active {
            transform: translateY(0) !important;
            box-shadow:
                0 0 20px rgba(37,99,235,0.3),
                0 2px 8px rgba(0,0,0,0.4) !important;
        }

        /* "Mot de passe oublié" et autres liens secondaires */
        .fi-link,
        .fi-btn-color-gray,
        a.text-sm {
            color: #475569 !important;
            font-size: 0.78rem !important;
            transition: color 0.2s !important;
        }
        .fi-link:hover { color: #94A3B8 !important; }

        /* ── Lien d'inscription ───────────────────────────────────── */
        .kz-register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.82rem;
            color: #475569;
        }
        .kz-register-link a {
            color: #2563EB;
            font-weight: 600;
            text-decoration: none;
            margin-left: 0.25rem;
            transition: color 0.2s;
        }
        .kz-register-link a:hover {
            color: #60A5FA;
            text-decoration: underline;
        }

        /* ── Lien retour ──────────────────────────────────────────── */
        .kz-back-link {
            position: absolute;
            bottom: 1.75rem;
            left: 50%;
            transform: translateX(-50%);
            color: #334155;
            font-size: 0.78rem;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            transition: color 0.2s ease;
            letter-spacing: 0.01em;
        }
        .kz-back-link:hover { color: #64748B; }
        .kz-back-link svg { transition: transform 0.2s; }
        .kz-back-link:hover svg { transform: translateX(-3px); }



                /* ── Mode sombre ── */
        .dark html, .dark body          { background: #0F172A !important; }
        .dark .kz-login                 { background: #0F172A; }
        .dark .kz-form-side             { background: #0F172A; }
        .dark .kz-form-side             { border-left-color: rgba(255,255,255,0.05) !important; }
        .dark .kz-lang-switch           { background: rgba(30,41,59,0.7); border-color: rgba(255,255,255,0.07); }
        .dark .kz-brand-name            { color: #E2E8F0; }
        .dark .kz-title                 { color: #F1F5F9; }
        .dark .fi-input-wrp input,
        .dark .fi-input                 { background: #1E293B !important; border-color: rgba(148,163,184,0.12) !important; color: #E2E8F0 !important; }
        .dark .fi-input-wrp input::placeholder { color: #334155 !important; }
        .dark .fi-fo-field-wrp > label,
        .dark .fi-label, .dark label[for] { color: #94A3B8 !important; }
        .dark .kz-back-link             { color: #334155; }


    </style>



    {{-- ======================================================
         PANNEAU GAUCHE : ART VISUEL ABSTRAIT
         ====================================================== --}}
    <div class="kz-visual">
        {{-- Orbes de lumière --}}
        <div class="kz-orb kz-orb-1"></div>
        <div class="kz-orb kz-orb-2"></div>
        <div class="kz-orb kz-orb-3"></div>

        {{-- Grille de points --}}
        <div class="kz-grid"></div>

        {{-- Art SVG : réseau de noeuds hexagonaux --}}
        <svg
            style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;"
            viewBox="0 0 640 900"
            xmlns="http://www.w3.org/2000/svg"
            preserveAspectRatio="xMidYMid slice"
        >
            <defs>
                <filter id="glow">
                    <feGaussianBlur stdDeviation="3" result="blur"/>
                    <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                </filter>
            </defs>

            {{-- Hexagones animés --}}
            <g fill="none" stroke="#2563EB" stroke-width="1.2">
                <polygon points="110,60 160,88 160,145 110,173 60,145 60,88" opacity="0.5">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; 8,-12; 0,0" dur="7s" repeatCount="indefinite"/>
                </polygon>
                <polygon points="280,130 330,158 330,215 280,243 230,215 230,158" opacity="0.35">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; -10,8; 0,0" dur="10s" repeatCount="indefinite"/>
                </polygon>
                <polygon points="490,80 540,108 540,165 490,193 440,165 440,108" opacity="0.45">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; 12,-6; 0,0" dur="8s" repeatCount="indefinite"/>
                </polygon>
                <polygon points="160,340 210,368 210,425 160,453 110,425 110,368" opacity="0.3">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; -6,12; 0,0" dur="12s" repeatCount="indefinite"/>
                </polygon>
                <polygon points="420,390 470,418 470,475 420,503 370,475 370,418" opacity="0.5">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; 10,-10; 0,0" dur="9s" repeatCount="indefinite"/>
                </polygon>
                <polygon points="550,560 600,588 600,645 550,673 500,645 500,588" opacity="0.3">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; -12,6; 0,0" dur="11s" repeatCount="indefinite"/>
                </polygon>
                <polygon points="90,610 140,638 140,695 90,723 40,695 40,638" opacity="0.22">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; 6,12; 0,0" dur="14s" repeatCount="indefinite"/>
                </polygon>
                <polygon points="290,560 340,588 340,645 290,673 240,645 240,588" opacity="0.38">
                    <animateTransform attributeName="transform" type="translate"
                        values="0,0; -8,-8; 0,0" dur="9.5s" repeatCount="indefinite"/>
                </polygon>
            </g>

            {{-- Lignes de connexion --}}
            <g stroke="#3B82F6" stroke-width="0.6" opacity="0.18">
                <line x1="110" y1="116" x2="280" y2="186"/>
                <line x1="280" y1="186" x2="490" y2="136"/>
                <line x1="110" y1="116" x2="160" y2="396"/>
                <line x1="490" y1="136" x2="420" y2="446"/>
                <line x1="160" y1="396" x2="420" y2="446"/>
                <line x1="420" y1="446" x2="550" y2="616"/>
                <line x1="160" y1="396" x2="90" y2="666"/>
                <line x1="290" y1="616" x2="550" y2="616"/>
                <line x1="280" y1="186" x2="160" y2="396"/>
                <line x1="290" y1="616" x2="160" y2="396"/>
            </g>

            {{-- Noeuds pulsants --}}
            <g filter="url(#glow)" fill="#2563EB">
                <circle cx="110" cy="116" r="3.5" opacity="0.9">
                    <animate attributeName="r" values="3.5;6;3.5" dur="3s" repeatCount="indefinite"/>
                    <animate attributeName="opacity" values="0.9;0.5;0.9" dur="3s" repeatCount="indefinite"/>
                </circle>
                <circle cx="280" cy="186" r="2.5" opacity="0.7">
                    <animate attributeName="r" values="2.5;5;2.5" dur="5s" repeatCount="indefinite"/>
                </circle>
                <circle cx="490" cy="136" r="3" opacity="0.8">
                    <animate attributeName="r" values="3;5.5;3" dur="4s" repeatCount="indefinite" begin="1s"/>
                </circle>
                <circle cx="420" cy="446" r="4.5" opacity="1">
                    <animate attributeName="r" values="4.5;8;4.5" dur="4s" repeatCount="indefinite"/>
                    <animate attributeName="opacity" values="1;0.4;1" dur="4s" repeatCount="indefinite"/>
                </circle>
                <circle cx="160" cy="396" r="2.5" opacity="0.6">
                    <animate attributeName="r" values="2.5;4.5;2.5" dur="6s" repeatCount="indefinite" begin="2s"/>
                </circle>
                <circle cx="550" cy="616" r="3" opacity="0.7">
                    <animate attributeName="r" values="3;5;3" dur="7s" repeatCount="indefinite"/>
                </circle>
                <circle cx="290" cy="616" r="2" opacity="0.5">
                    <animate attributeName="r" values="2;4;2" dur="8s" repeatCount="indefinite" begin="1.5s"/>
                </circle>
            </g>
        </svg>

        {{-- Contenu textuel en bas --}}
        <div class="kz-visual-content">
            <div class="kz-visual-badge">
                <div class="kz-visual-badge-dot"></div>
                <span class="kz-visual-badge-text" x-text="labels[lang].live"></span>
            </div>
            <div class="kz-visual-tagline">
                <span x-text="labels[lang].tagline1"></span><br>
                <em x-text="labels[lang].tagline2"></em>
            </div>
            <p class="kz-visual-desc" x-text="labels[lang].desc"></p>
        </div>
    </div>

    {{-- ======================================================
         PANNEAU DROIT : FORMULAIRE
         ====================================================== --}}
    <div class="kz-form-side">

        {{-- Sélecteur de langue --}}
        <div class="kz-lang-switch">
            <button
                type="button"
                class="kz-lang-btn"
                :class="{ 'is-active': lang === 'fr' }"
                wire:click="setLocale('fr')"
                @click="lang = 'fr'"
            >FR</button>
            <button
                type="button"
                class="kz-lang-btn"
                :class="{ 'is-active': lang === 'en' }"
                wire:click="setLocale('en')"
                @click="lang = 'en'"
            >EN</button>
        </div>

        {{-- Contenu du formulaire --}}
        <div class="kz-form-box">

            {{-- Brand --}}
            <div class="kz-brand">
                <div class="kz-brand-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z"
                              stroke="white" stroke-width="1.5" fill="rgba(255,255,255,0.12)"/>
                        <path d="M9 12L11 14L15 10"
                              stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="kz-brand-name">Kazi<span>Trust</span></div>
            </div>

            {{-- Titre --}}
            <h1 class="kz-title" x-text="labels[lang].welcome"></h1>
            <p class="kz-subtitle" x-text="labels[lang].subtitle"></p>

            <div class="kz-divider"></div>

            {{-- Formulaire Filament --}}
            <form wire:submit.prevent="authenticate">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </form>

            {{-- Lien d'inscription --}}
            @if (filament()->hasRegistration())
                <div class="kz-register-link">
                    <span x-text="labels[lang].newHere"></span>
                    <a href="{{ filament()->getRegistrationUrl() }}" x-text="labels[lang].register"></a>
                </div>
            @endif

        </div>

        {{-- Lien retour --}}
        <a href="/" class="kz-back-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            <span x-text="labels[lang].back"></span>
        </a>
    </div>

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('refresh-page', (event) => {
            window.location.reload();
        });
    });
</script>

</div>