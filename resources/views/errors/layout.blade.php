{{--
    resources/views/errors/layout.blade.php
    Layout partagé pour toutes les pages d'erreur KaziTrust
--}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('code', '000') — KaziTrust</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* ── Reset ──────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Variables ──────────────────────────────────────────── */
        :root {
            --bg:          #F8FAFC;
            --bg-card:     #FFFFFF;
            --border:      #E2E8F0;
            --text-head:   #0F172A;
            --text-sub:    #64748B;
            --text-muted:  #94A3B8;
            --code-color:  rgba(37,99,235,0.06);
            --btn-dash-bg: #2563EB;
            --btn-dash-fg: #FFFFFF;
            --btn-home-bg: #F1F5F9;
            --btn-home-fg: #334155;
            --btn-home-bd: #E2E8F0;
            --accent:      #2563EB;
            --accent-glow: rgba(37,99,235,0.18);
            --orb1:        rgba(37,99,235,0.08);
            --orb2:        rgba(29,78,216,0.06);
        }
        .dark {
            --bg:          #080E1A;
            --bg-card:     #0F172A;
            --border:      rgba(255,255,255,0.06);
            --text-head:   #F1F5F9;
            --text-sub:    #64748B;
            --text-muted:  #334155;
            --code-color:  rgba(37,99,235,0.1);
            --btn-dash-bg: #2563EB;
            --btn-dash-fg: #FFFFFF;
            --btn-home-bg: rgba(30,41,59,0.8);
            --btn-home-fg: #94A3B8;
            --btn-home-bd: rgba(255,255,255,0.07);
            --orb1:        rgba(37,99,235,0.2);
            --orb2:        rgba(29,78,216,0.15);
        }

        /* ── Base ───────────────────────────────────────────────── */
        html, body {
            min-height: 100vh;
            background: var(--bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background 0.3s ease;
        }

        /* ── Layout centré ──────────────────────────────────────── */
        .kz-err {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            position: relative;
            overflow: hidden;
            background: var(--bg);
        }

        /* ── Orbes de fond ──────────────────────────────────────── */
        .kz-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
        }
        .kz-orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, var(--orb1) 0%, transparent 70%);
            top: -20%; left: -15%;
            animation: orbDrift 12s ease-in-out infinite;
        }
        .kz-orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, var(--orb2) 0%, transparent 70%);
            bottom: -10%; right: -10%;
            animation: orbDrift 16s ease-in-out infinite reverse;
        }
        @keyframes orbDrift {
            0%,100% { transform: translate(0,0) scale(1); }
            33%  { transform: translate(30px,-20px) scale(1.04); }
            66%  { transform: translate(-20px,28px) scale(0.96); }
        }

        /* Grille de points */
        .kz-dots {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(37,99,235,0.15) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.5;
            pointer-events: none;
        }
        .dark .kz-dots {
            opacity: 0.25;
        }

        /* ── Card principale ────────────────────────────────────── */
        .kz-card {
            position: relative;
            z-index: 10;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 3rem 3.5rem;
            max-width: 540px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 40px rgba(0,0,0,0.06), 0 1px 6px rgba(0,0,0,0.04);
            animation: cardIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @media (max-width: 600px) {
            .kz-card { padding: 2rem 1.5rem; border-radius: 16px; }
        }
        .dark .kz-card {
            box-shadow: 0 4px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.04);
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Brand ──────────────────────────────────────────────── */
        .kz-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 2.5rem;
            text-decoration: none;
        }
        .kz-brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 20px rgba(37,99,235,0.35);
        }
        .kz-brand-name {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-head);
            letter-spacing: -0.02em;
        }
        .kz-brand-name em { font-style: normal; color: #2563EB; }

        /* ── Code d'erreur ──────────────────────────────────────── */
        .kz-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: clamp(5rem, 18vw, 7.5rem);
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.06em;
            color: var(--accent);
            margin-bottom: 0.5rem;
            position: relative;
            display: inline-block;
        }
        .kz-code::after {
            content: attr(data-code);
            position: absolute;
            inset: 0;
            color: transparent;
            -webkit-text-stroke: 1px var(--accent);
            opacity: 0.12;
            transform: translate(4px, 4px);
        }

        /* ── Icône d'erreur ─────────────────────────────────────── */
        .kz-icon-wrap {
            width: 72px; height: 72px;
            background: var(--code-color);
            border: 1px solid rgba(37,99,235,0.15);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            animation: iconPulse 3s ease-in-out infinite;
        }
        @keyframes iconPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(37,99,235,0.15); }
            50%     { box-shadow: 0 0 0 10px rgba(37,99,235,0); }
        }
        .kz-icon-wrap svg { color: var(--accent); }

        /* ── Textes ─────────────────────────────────────────────── */
        .kz-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-head);
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 0.75rem;
        }
        .kz-desc {
            font-size: 0.9rem;
            color: var(--text-sub);
            line-height: 1.7;
            max-width: 380px;
            margin: 0 auto 2rem;
        }

        /* ── Séparateur ─────────────────────────────────────────── */
        .kz-sep {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-glow, rgba(37,99,235,0.2)), transparent);
            margin: 1.5rem 0;
        }

        /* ── Boutons ────────────────────────────────────────────── */
        .kz-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .kz-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.7rem 1.4rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.22s ease;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }
        .kz-btn-primary {
            background: var(--btn-dash-bg);
            color: var(--btn-dash-fg);
            box-shadow: 0 0 20px rgba(37,99,235,0.25), 0 2px 8px rgba(0,0,0,0.1);
        }
        .kz-btn-primary:hover {
            background: #1D4ED8;
            transform: translateY(-1px);
            box-shadow: 0 0 32px rgba(37,99,235,0.4), 0 4px 16px rgba(0,0,0,0.15);
        }
        .kz-btn-secondary {
            background: var(--btn-home-bg);
            color: var(--btn-home-fg);
            border: 1px solid var(--btn-home-bd);
        }
        .kz-btn-secondary:hover {
            background: var(--border);
            transform: translateY(-1px);
        }

        /* ── Code erreur petit (sous les boutons) ───────────────── */
        .kz-err-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            margin-top: 1.75rem;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-family: 'JetBrains Mono', monospace;
        }
        .kz-err-code-badge::before {
            content: '';
            width: 5px; height: 5px;
            background: var(--accent);
            border-radius: 50%;
            opacity: 0.5;
        }

        /* ── Sélecteur de langue ────────────────────────────────── */
        .kz-lang-switch {
            position: fixed;
            top: 1.25rem; right: 1.25rem;
            display: flex; align-items: center; gap: 0.2rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.2rem;
            z-index: 100;
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .kz-lang-btn {
            padding: 0.28rem 0.65rem;
            border-radius: 7px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--text-sub);
            transition: all 0.18s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .kz-lang-btn:hover { color: var(--text-head); }
        .kz-lang-btn.active {
            background: rgba(37,99,235,0.15);
            color: #2563EB;
            border: 1px solid rgba(37,99,235,0.3);
        }

        /* ── Toggle dark mode ───────────────────────────────────── */
        .kz-theme-btn {
            position: fixed;
            top: 1.25rem; left: 1.25rem;
            width: 36px; height: 36px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            z-index: 100;
            transition: all 0.18s;
            color: var(--text-sub);
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .kz-theme-btn:hover { color: var(--text-head); background: var(--border); }
    </style>
</head>

<body
    x-data="{
        lang: localStorage.getItem('kz_lang') || 'fr',
        dark: localStorage.getItem('kz_dark') === 'true' || window.matchMedia('(prefers-color-scheme: dark)').matches,
        init() {
            this.$watch('dark', v => {
                localStorage.setItem('kz_dark', v);
                document.documentElement.classList.toggle('dark', v);
            });
            this.$watch('lang', v => localStorage.setItem('kz_lang', v));
            document.documentElement.classList.toggle('dark', this.dark);
        }
    }"
    :class="dark ? 'dark' : ''"
>

    {{-- Sélecteur langue --}}
    <div class="kz-lang-switch">
        <button class="kz-lang-btn" :class="{ active: lang === 'fr' }" @click="lang = 'fr'">FR</button>
        <button class="kz-lang-btn" :class="{ active: lang === 'en' }" @click="lang = 'en'">EN</button>
    </div>

    {{-- Toggle dark/light --}}
    <button class="kz-theme-btn" @click="dark = !dark" :title="dark ? 'Mode clair' : 'Mode sombre'">
        <svg x-show="dark" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
        </svg>
        <svg x-show="!dark" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
        </svg>
    </button>

    <div class="kz-err">
        {{-- Ambiance --}}
        <div class="kz-orb kz-orb-1"></div>
        <div class="kz-orb kz-orb-2"></div>
        <div class="kz-dots"></div>

        {{-- Card --}}
        <div class="kz-card">

            {{-- Brand --}}
            <a href="/" class="kz-brand">
                <div class="kz-brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2L3 7V12C3 16.55 6.84 20.74 12 22C17.16 20.74 21 16.55 21 12V7L12 2Z"
                              stroke="white" stroke-width="1.5" fill="rgba(255,255,255,0.15)"/>
                        <path d="M9 12L11 14L15 10" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="kz-brand-name">Kazi<em>Trust</em></div>
            </a>

            @yield('content')

            <div class="kz-sep"></div>

            {{-- Actions --}}
            <div class="kz-actions" x-data>
                @auth
                    <a href="{{ url('/management') }}" class="kz-btn kz-btn-primary">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        <span x-text="lang === 'fr' ? 'Tableau de bord' : 'Dashboard'"></span>
                    </a>
                @endauth
                <a href="{{ url('/') }}" class="kz-btn kz-btn-secondary">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M19 12H5M12 5l-7 7 7 7"/>
                    </svg>
                    <span x-text="lang === 'fr' ? 'Page d\'accueil' : 'Home page'"></span>
                </a>
            </div>

            <div class="kz-err-code-badge">
                ERROR @yield('code', '000')
            </div>

        </div>
    </div>
</body>
</html>