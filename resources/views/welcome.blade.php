{{-- resources/views/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      x-data="{
          lang: localStorage.getItem('kazi_lang') || 'fr',
          mobileOpen: false,
          scrolled: false,
          faq: null
      }"
      x-init="
          document.documentElement.lang = lang;
          window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 });
      "
      x-bind:lang="lang"
      class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KaziTrust — API Anti-Fraude Mobile pour l'Afrique</title>
    <meta name="description" content="Protégez vos transactions mobiles avec l'IA et Nokia CAMARA. BYO-AI, détection SIM Swap, API REST prête en 5 minutes.">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Google Fonts: Syne (display) + DM Sans (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        kazi: {
                            50:  '#eff3ff',
                            100: '#dce5ff',
                            400: '#6b8cf7',
                            500: '#3f69f5',
                            600: '#2a52e0',
                            700: '#1e3dcc',
                            900: '#0b1854',
                        },
                        ink: {
                            950: '#060a14',
                            900: '#0c1222',
                            800: '#111827',
                            700: '#1a2336',
                            600: '#1e2d42',
                            400: '#3d5170',
                            300: '#6b7fa0',
                            200: '#94a6c3',
                            100: '#c8d3e8',
                        },
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                        }
                    },
                    fontFamily: {
                        display: ['Syne', 'system-ui', 'sans-serif'],
                        sans: ['DM Sans', 'system-ui', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-slow': 'float 9s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4,0,0.6,1) infinite',
                        'shimmer': 'shimmer 2.5s linear infinite',
                        'fade-up': 'fadeUp 0.7s ease forwards',
                        'scan': 'scan 3s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%,100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-12px)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% center' },
                            '100%': { backgroundPosition: '200% center' },
                        },
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(24px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scan: {
                            '0%': { transform: 'translateY(-100%)' },
                            '100%': { transform: 'translateY(400%)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Base */
        body { background-color: #060a14; color: #c8d3e8; }

        /* Noise texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        /* Hero gradient mesh */
        .hero-mesh {
            background:
                radial-gradient(ellipse 80% 60% at 50% -20%, rgba(63,105,245,0.25) 0%, transparent 70%),
                radial-gradient(ellipse 50% 40% at 80% 50%, rgba(30,61,204,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 40% 50% at 10% 80%, rgba(11,24,84,0.3) 0%, transparent 60%),
                #060a14;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #a8bfff 50%, #6b8cf7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gradient-text-gold {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 60%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Shimmer button */
        .btn-shimmer {
            background: linear-gradient(90deg,
                #3f69f5 0%,
                #6b8cf7 40%,
                #3f69f5 60%,
                #2a52e0 100%);
            background-size: 200% auto;
            animation: shimmer 3s linear infinite;
        }

        /* Glass card */
        .glass {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .glass-light {
            background: rgba(30, 45, 66, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.08);
        }

        /* Feature card hover */
        .feature-card {
            transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            border-color: rgba(63,105,245,0.4) !important;
            box-shadow: 0 24px 48px rgba(63,105,245,0.12), 0 0 0 1px rgba(63,105,245,0.15);
        }

        /* Grid lines decoration */
        .grid-lines {
            background-image:
                linear-gradient(rgba(63,105,245,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(63,105,245,0.06) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Code terminal */
        .terminal-glow {
            box-shadow:
                0 0 0 1px rgba(63,105,245,0.2),
                0 32px 64px rgba(0,0,0,0.5),
                0 0 80px rgba(63,105,245,0.08);
        }

        /* Typing cursor */
        .cursor::after {
            content: '▋';
            animation: blink 1s step-end infinite;
            color: #3f69f5;
        }
        @keyframes blink { 50% { opacity: 0; } }

        /* Scan line animation */
        .scan-line {
            position: absolute;
            left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(63,105,245,0.5), transparent);
            animation: scan 3s linear infinite;
        }

        /* FAQ transition */
        .faq-content {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.35s cubic-bezier(0.4,0,0.2,1);
        }
        .faq-content.open {
            grid-template-rows: 1fr;
        }

        /* Logo marquee */
        .marquee-wrapper { overflow: hidden; }
        .marquee-track {
            display: flex;
            gap: 3rem;
            animation: marquee 20s linear infinite;
            width: max-content;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Animated counter dots */
        .status-dot {
            width: 8px; height: 8px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
            animation: pulse 2s cubic-bezier(0.4,0,0.6,1) infinite;
        }

        /* Separator gradient */
        .sep {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
        }

        /* Stagger fade-up on load */
        .stagger-1 { animation: fadeUp 0.7s 0.1s ease both; }
        .stagger-2 { animation: fadeUp 0.7s 0.25s ease both; }
        .stagger-3 { animation: fadeUp 0.7s 0.4s ease both; }
        .stagger-4 { animation: fadeUp 0.7s 0.55s ease both; }
        .stagger-5 { animation: fadeUp 0.7s 0.7s ease both; }

        /* Dashboard mockup */
        .dash-card {
            background: rgba(17,24,39,0.9);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #060a14; }
        ::-webkit-scrollbar-thumb { background: #1a2336; border-radius: 3px; }
    </style>
</head>

<body class="font-sans antialiased overflow-x-hidden">

{{-- ─── NAVBAR ─────────────────────────────────────────────────────── --}}
<nav :class="scrolled
        ? 'border-b border-white/5 bg-ink-950/90 backdrop-blur-xl shadow-2xl'
        : 'border-b border-transparent bg-transparent'"
     class="fixed top-0 left-0 right-0 z-50 transition-all duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-kazi-500 flex items-center justify-center shadow-lg shadow-kazi-500/30 group-hover:shadow-kazi-500/50 transition-shadow">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="font-display font-700 text-white text-lg tracking-tight" style="font-weight:700">KaziTrust</span>
                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-kazi-500/15 text-kazi-400 border border-kazi-500/20">Beta</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="#benefits" class="px-3 py-2 text-sm text-ink-200 hover:text-white rounded-lg hover:bg-white/5 transition-all"
                   x-text="lang === 'fr' ? 'Bénéfices' : 'Benefits'"></a>
                <a href="#devex" class="px-3 py-2 text-sm text-ink-200 hover:text-white rounded-lg hover:bg-white/5 transition-all"
                   x-text="lang === 'fr' ? 'Développeurs' : 'Developers'"></a>
                <a href="#pricing" class="px-3 py-2 text-sm text-ink-200 hover:text-white rounded-lg hover:bg-white/5 transition-all"
                   x-text="lang === 'fr' ? 'Tarifs' : 'Pricing'"></a>
                <a href="#faq" class="px-3 py-2 text-sm text-ink-200 hover:text-white rounded-lg hover:bg-white/5 transition-all">FAQ</a>
            </div>

            <div class="hidden md:flex items-center gap-3">
                {{-- Lang toggle --}}
                <button @click="lang = lang === 'fr' ? 'en' : 'fr'; localStorage.setItem('kazi_lang', lang)"
                        class="flex items-center gap-1.5 text-xs text-ink-300 hover:text-white border border-white/10 rounded-full px-3 py-1.5 transition-all hover:border-white/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    <span x-text="lang === 'fr' ? 'EN' : 'FR'" class="font-medium uppercase tracking-wide"></span>
                </button>

                <a href="{{ route('filament.management.auth.login') }}"
                   class="text-sm text-ink-200 hover:text-white transition-colors px-3 py-2"
                   x-text="lang === 'fr' ? 'Connexion' : 'Sign in'"></a>

                <a href="{{ route('filament.management.auth.register') }}"
                   class="btn-shimmer inline-flex items-center gap-2 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-lg shadow-kazi-500/25 hover:shadow-kazi-500/40 transition-shadow">
                    <span x-text="lang === 'fr' ? 'Essai gratuit' : 'Free trial'"></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            {{-- Mobile toggle --}}
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg hover:bg-white/5 text-ink-200">
                <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-white/5 bg-ink-950/95 backdrop-blur-xl px-4 py-4 space-y-1">
        <a href="#benefits" @click="mobileOpen=false" class="block px-3 py-2.5 text-sm text-ink-200 hover:text-white rounded-lg hover:bg-white/5"
           x-text="lang === 'fr' ? 'Bénéfices' : 'Benefits'"></a>
        <a href="#devex" @click="mobileOpen=false" class="block px-3 py-2.5 text-sm text-ink-200 hover:text-white rounded-lg hover:bg-white/5"
           x-text="lang === 'fr' ? 'Développeurs' : 'Developers'"></a>
        <a href="#pricing" @click="mobileOpen=false" class="block px-3 py-2.5 text-sm text-ink-200 hover:text-white rounded-lg hover:bg-white/5"
           x-text="lang === 'fr' ? 'Tarifs' : 'Pricing'"></a>
        <div class="sep my-3"></div>
        <div class="flex gap-2 pt-1">
            <button @click="lang = lang === 'fr' ? 'en' : 'fr'; localStorage.setItem('kazi_lang', lang)"
                    class="text-xs border border-white/10 rounded-full px-3 py-1.5 text-ink-300"
                    x-text="lang === 'fr' ? 'English' : 'Français'"></button>
            <a href="{{ route('filament.management.auth.register') }}"
               class="flex-1 text-center btn-shimmer text-white text-sm font-medium px-4 py-2 rounded-lg"
               x-text="lang === 'fr' ? 'Essai gratuit' : 'Free trial'"></a>
        </div>
    </div>
</nav>

{{-- ─── HERO ───────────────────────────────────────────────────────── --}}
<section class="hero-mesh relative min-h-screen flex flex-col justify-center pt-16 pb-24 px-4 overflow-hidden">
    {{-- Background decorative grid --}}
    <div class="absolute inset-0 grid-lines opacity-50"></div>

    {{-- Glowing orbs --}}
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-kazi-500/10 rounded-full blur-3xl animate-float-slow pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-indigo-500/8 rounded-full blur-3xl animate-float pointer-events-none" style="animation-delay:-3s"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">

            {{-- Left column --}}
            <div class="stagger-1">
                {{-- Status badge --}}
                <div class="inline-flex items-center gap-2.5 glass rounded-full px-4 py-2 mb-8 stagger-1">
                    <span class="status-dot"></span>
                    <span class="text-xs text-ink-200 font-medium">
                        <span x-show="lang === 'fr'" x-cloak>Propulsé par Nokia CAMARA · API disponible en Afrique de l'Ouest</span>
                        <span x-show="lang === 'en'" x-cloak>Powered by Nokia CAMARA · API live in West Africa</span>
                    </span>
                </div>

                {{-- Headline --}}
                <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl font-800 text-white leading-[1.05] tracking-tight mb-6 stagger-2" style="font-weight:800">
                    <span x-show="lang === 'fr'" x-cloak>
                        Chaque fraude<br>
                        <span class="gradient-text">stoppée net</span><br>
                        avant le débit.
                    </span>
                    <span x-show="lang === 'en'" x-cloak>
                        Every fraud<br>
                        <span class="gradient-text">stopped cold</span><br>
                        before the debit.
                    </span>
                </h1>

                {{-- Sub-headline --}}
                <p class="text-ink-200 text-lg leading-relaxed mb-8 max-w-lg stagger-3">
                    <span x-show="lang === 'fr'" x-cloak>
                        KaziTrust relie vos transactions mobiles aux signaux réseau Nokia CAMARA et à votre propre IA — pour une décision de confiance en&nbsp;<strong class="text-white font-medium">moins de 2 secondes</strong>. Conçu pour les <strong class="text-white font-medium">PME & IMF</strong> d'Afrique francophone.
                    </span>
                    <span x-show="lang === 'en'" x-cloak>
                        KaziTrust connects your mobile transactions to Nokia CAMARA network signals and your own AI — delivering a trust decision in <strong class="text-white font-medium">under 2 seconds</strong>. Built for <strong class="text-white font-medium">SMEs & MFIs</strong> across francophone Africa.
                    </span>
                </p>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-10 stagger-4">
                    <a href="{{ route('filament.management.auth.register') }}"
                       class="btn-shimmer inline-flex items-center justify-center gap-2 text-white font-semibold px-6 py-3.5 rounded-xl shadow-xl shadow-kazi-500/25 hover:shadow-kazi-500/40 transition-shadow text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span x-text="lang === 'fr' ? 'Créer mon espace — gratuit 14j' : 'Create my workspace — free 14d'"></span>
                    </a>
                    <a href="/docs"
                       class="inline-flex items-center justify-center gap-2 text-ink-100 font-medium px-6 py-3.5 rounded-xl border border-white/10 hover:border-white/20 hover:bg-white/5 transition-all text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                        <span x-text="lang === 'fr' ? 'Voir la documentation' : 'View the docs'"></span>
                    </a>
                </div>

                {{-- Trust signals --}}
                <div class="flex flex-wrap items-center gap-5 stagger-5">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                          'fr' => 'Sans carte bancaire', 'en' => 'No credit card'],
                        ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                          'fr' => 'Clés AES-256', 'en' => 'AES-256 keys'],
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                          'fr' => 'Prêt en 5 minutes', 'en' => 'Live in 5 minutes'],
                    ] as $t)
                    <div class="flex items-center gap-2 text-sm text-ink-300">
                        <svg class="w-4 h-4 text-kazi-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/>
                        </svg>
                        <span x-text="lang === 'fr' ? '{{ $t['fr'] }}' : '{{ $t['en'] }}'"></span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right column — Dashboard Mockup --}}
            <div class="relative lg:block stagger-3" style="animation-delay:0.3s">
                <div class="relative animate-float" style="animation-duration:7s">

                    {{-- Main dashboard card --}}
                    <div class="glass rounded-2xl overflow-hidden terminal-glow">
                        {{-- Title bar --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
                            <div class="flex items-center gap-2">
                                <div class="flex gap-1.5">
                                    <div class="w-3 h-3 rounded-full bg-red-500/70"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-500/70"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-500/70"></div>
                                </div>
                                <span class="text-ink-300 text-xs font-mono ml-2">KaziTrust · Dashboard</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="status-dot" style="width:6px;height:6px;box-shadow:0 0 0 2px rgba(34,197,94,0.2)"></span>
                                <span class="text-xs text-green-400 font-medium">Live</span>
                            </div>
                        </div>

                        {{-- Dashboard content --}}
                        <div class="p-5 space-y-4">
                            {{-- Stats row --}}
                            <div class="grid grid-cols-3 gap-3">
                                @foreach([
                                    ['label_fr' => 'Analyses aujourd\'hui', 'label_en' => 'Analyses today', 'val' => '1 247', 'delta' => '+12%', 'up' => true],
                                    ['label_fr' => 'Fraudes bloquées', 'label_en' => 'Frauds blocked', 'val' => '23', 'delta' => '−8%', 'up' => false],
                                    ['label_fr' => 'Score moyen', 'label_en' => 'Avg. score', 'val' => '91.4', 'delta' => '+2pt', 'up' => true],
                                ] as $s)
                                <div class="dash-card p-3">
                                    <div class="text-ink-300 text-xs mb-1" x-text="lang==='fr' ? '{{ $s['label_fr'] }}' : '{{ $s['label_en'] }}'"></div>
                                    <div class="text-white font-display font-700 text-xl" style="font-weight:700">{{ $s['val'] }}</div>
                                    <div class="text-xs mt-1 {{ $s['up'] ? 'text-green-400' : 'text-red-400' }}">{{ $s['delta'] }}</div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Recent transactions --}}
                            <div class="dash-card overflow-hidden">
                                <div class="px-3 py-2 border-b border-white/5 flex items-center justify-between">
                                    <span class="text-xs text-ink-300 font-medium" x-text="lang==='fr' ? 'Analyses récentes' : 'Recent analyses'"></span>
                                    <span class="text-xs text-kazi-400" x-text="lang==='fr' ? 'Voir tout' : 'View all'"></span>
                                </div>
                                @php
                                $txs = [
                                    ['num'=>'+22961****23','score'=>94,'decision'=>'approve','ms'=>1140,'color'=>'green'],
                                    ['num'=>'+22997****88','score'=>12,'decision'=>'reject','ms'=>987,'color'=>'red'],
                                    ['num'=>'+22961****55','score'=>78,'decision'=>'approve','ms'=>1320,'color'=>'green'],
                                    ['num'=>'+22990****07','score'=>45,'decision'=>'review','ms'=>1580,'color'=>'yellow'],
                                ];
                                @endphp
                                <div class="divide-y divide-white/[0.04]">
                                    @foreach($txs as $tx)
                                    <div class="px-3 py-2.5 flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-ink-700 flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-xs text-white font-mono">{{ $tx['num'] }}</div>
                                                <div class="text-xs text-ink-400">{{ $tx['ms'] }}ms</div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-xs font-medium text-ink-200">{{ $tx['score'] }}/100</div>
                                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                                {{ $tx['color'] === 'green' ? 'bg-green-500/15 text-green-400' : '' }}
                                                {{ $tx['color'] === 'red' ? 'bg-red-500/15 text-red-400' : '' }}
                                                {{ $tx['color'] === 'yellow' ? 'bg-yellow-500/15 text-yellow-400' : '' }}">
                                                {{ $tx['decision'] }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating mini-card 1 --}}
                    <div class="absolute -left-12 top-1/3 glass-light rounded-xl px-3.5 py-3 shadow-2xl animate-float hidden lg:block" style="animation-delay:-2s;animation-duration:8s">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-white font-medium">Approuvé</div>
                                <div class="text-xs text-ink-300">Score: 94/100</div>
                            </div>
                        </div>
                    </div>

                    {{-- Floating mini-card 2 --}}
                    <div class="absolute -right-10 bottom-1/4 glass-light rounded-xl px-3.5 py-3 shadow-2xl animate-float hidden lg:block" style="animation-delay:-4s;animation-duration:6s">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-kazi-500/20 flex items-center justify-center">
                                <svg class="w-4 h-4 text-kazi-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-white font-medium">Nokia CAMARA</div>
                                <div class="text-xs text-ink-300">SIM Swap: Aucun</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats bar --}}
        <div class="sep mt-20"></div>
        <div class="flex flex-wrap justify-center lg:justify-between gap-8 pt-10 stagger-5">
            @foreach([
                ['val'=>'< 2s', 'fr'=>'Temps de décision moyen', 'en'=>'Average decision time'],
                ['val'=>'99.2%', 'fr'=>'Précision de détection IA', 'en'=>'AI detection accuracy'],
                ['val'=>'3 LLMs', 'fr'=>'Moteurs IA supportés', 'en'=>'Supported AI engines'],
                ['val'=>'256-bit', 'fr'=>'Chiffrement AES des clés', 'en'=>'AES key encryption'],
            ] as $s)
            <div class="text-center">
                <div class="font-display text-3xl lg:text-4xl font-800 gradient-text mb-1" style="font-weight:800">{{ $s['val'] }}</div>
                <div class="text-sm text-ink-300" x-text="lang==='fr' ? '{{ $s['fr'] }}' : '{{ $s['en'] }}'"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── SOCIAL PROOF / LOGOS ───────────────────────────────────────── --}}
<section class="relative py-16 px-4 border-y border-white/5">
    <div class="max-w-7xl mx-auto">
        <p class="text-center text-xs font-medium text-ink-400 uppercase tracking-widest mb-10"
           x-text="lang === 'fr' ? 'Infrastructure de confiance, partenaires de rang mondial' : 'Trusted infrastructure, world-class partners'"></p>

        <div class="marquee-wrapper">
            <div class="marquee-track items-center">
                @php
                $logos = [
                    ['name'=>'Nokia', 'svg'=>'<svg viewBox="0 0 100 40" class="h-7 fill-current"><text y="32" font-size="28" font-family="Arial,sans-serif" font-weight="700" letter-spacing="-1">Nokia</text></svg>'],
                    ['name'=>'OpenAI', 'svg'=>'<svg viewBox="0 0 100 40" class="h-7 fill-current"><text y="30" font-size="22" font-family="Arial,sans-serif" font-weight="700">OpenAI</text></svg>'],
                    ['name'=>'Laravel', 'svg'=>'<svg viewBox="0 0 100 40" class="h-7 fill-current"><text y="30" font-size="22" font-family="Arial,sans-serif" font-weight="700">Laravel</text></svg>'],
                    ['name'=>'Google Gemini', 'svg'=>'<svg viewBox="0 0 130 40" class="h-7 fill-current"><text y="30" font-size="22" font-family="Arial,sans-serif" font-weight="700">Gemini</text></svg>'],
                    ['name'=>'GSMA CAMARA', 'svg'=>'<svg viewBox="0 0 150 40" class="h-7 fill-current"><text y="30" font-size="20" font-family="Arial,sans-serif" font-weight="700">CAMARA API</text></svg>'],
                    ['name'=>'FilamentPHP', 'svg'=>'<svg viewBox="0 0 160 40" class="h-7 fill-current"><text y="30" font-size="20" font-family="Arial,sans-serif" font-weight="700">FilamentPHP</text></svg>'],
                ];
                @endphp
                {{-- Doubled for seamless loop --}}
                @foreach(array_merge($logos, $logos) as $logo)
                <div class="flex-shrink-0 flex items-center justify-center px-8 text-ink-500 hover:text-ink-300 transition-colors duration-300">
                    {!! $logo['svg'] !!}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ─── BENEFITS ────────────────────────────────────────────────────── --}}
<section id="benefits" class="py-28 px-4 relative">
    <div class="absolute inset-0 grid-lines opacity-30"></div>
    <div class="max-w-7xl mx-auto relative z-10">

        {{-- Section header --}}
        <div class="max-w-2xl mb-20">
            <div class="inline-flex items-center gap-2 glass rounded-full px-3.5 py-1.5 mb-6">
                <div class="w-1.5 h-1.5 rounded-full bg-kazi-400"></div>
                <span class="text-xs text-kazi-400 font-medium uppercase tracking-widest"
                      x-text="lang==='fr' ? 'Pourquoi KaziTrust' : 'Why KaziTrust'"></span>
            </div>
            <h2 class="font-display text-4xl sm:text-5xl font-800 text-white leading-tight" style="font-weight:800">
                <span x-show="lang==='fr'" x-cloak>Trois piliers.<br>
                    <span class="gradient-text">Une décision.</span>
                </span>
                <span x-show="lang==='en'" x-cloak>Three pillars.<br>
                    <span class="gradient-text">One decision.</span>
                </span>
            </h2>
        </div>

        {{-- 3-col grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">

            {{-- Card 1: BYO-AI --}}
            <div class="feature-card glass rounded-2xl p-7 border border-white/7 group">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-kazi-500/30 to-kazi-700/20 flex items-center justify-center mb-6 border border-kazi-500/20 group-hover:border-kazi-500/40 transition-colors">
                    <svg class="w-6 h-6 text-kazi-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div class="inline-flex items-center gap-1.5 bg-amber-500/10 border border-amber-500/20 rounded-full px-2.5 py-1 mb-4">
                    <span class="text-amber-400 text-xs font-semibold uppercase tracking-wide">BYO-AI</span>
                </div>
                <h3 class="font-display text-xl font-700 text-white mb-3" style="font-weight:700"
                    x-text="lang==='fr' ? 'Contrôle total de votre IA' : 'Total control of your AI'"></h3>
                <p class="text-sm text-ink-300 leading-relaxed"
                   x-text="lang==='fr'
                     ? 'Apportez votre propre clé OpenAI, Gemini ou Claude. Vos données ne quittent jamais votre périmètre. Zéro lock-in, 100% maîtrise des coûts.'
                     : 'Bring your own OpenAI, Gemini, or Claude key. Your data never leaves your perimeter. Zero lock-in, 100% cost ownership.'">
                </p>
                <div class="mt-6 pt-6 border-t border-white/5 flex flex-wrap gap-2">
                    @foreach(['OpenAI','Gemini','Claude'] as $ai)
                    <span class="text-xs px-2.5 py-1 rounded-lg bg-ink-700/60 text-ink-200 border border-white/5">{{ $ai }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Card 2: Nokia NaC — Featured --}}
            <div class="feature-card relative glass rounded-2xl p-7 border border-kazi-500/30 group overflow-hidden" style="background:rgba(17,24,39,0.8)">
                {{-- Glow --}}
                <div class="absolute inset-0 bg-gradient-to-b from-kazi-500/5 to-transparent pointer-events-none"></div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-px bg-gradient-to-r from-transparent via-kazi-500/60 to-transparent"></div>

                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/30 to-kazi-500/20 flex items-center justify-center mb-6 border border-blue-500/30">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <div class="inline-flex items-center gap-1.5 bg-kazi-500/15 border border-kazi-500/30 rounded-full px-2.5 py-1 mb-4">
                        <svg class="w-3 h-3 text-kazi-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        <span class="text-kazi-400 text-xs font-semibold uppercase tracking-wide">Nokia NaC</span>
                    </div>
                    <h3 class="font-display text-xl font-700 text-white mb-3" style="font-weight:700"
                        x-text="lang==='fr' ? 'Sécurité niveau bancaire' : 'Bank-grade security'"></h3>
                    <p class="text-sm text-ink-300 leading-relaxed"
                       x-text="lang==='fr'
                         ? 'Les signaux réseau Nokia CAMARA (SIM Swap, roaming, statut) vérifient l\'identité à la source — là où les fraudeurs ne peuvent pas tricher.'
                         : 'Nokia CAMARA network signals (SIM Swap, roaming, status) verify identity at source — where fraudsters cannot cheat.'">
                    </p>

                    {{-- Signal indicators --}}
                    <div class="mt-6 pt-6 border-t border-white/5 space-y-2">
                        @foreach([
                            ['fr'=>'SIM Swap détecté','en'=>'SIM Swap detected','ok'=>true],
                            ['fr'=>'Roaming actif','en'=>'Roaming active','ok'=>false],
                            ['fr'=>'Numéro actif 18 mois','en'=>'Number active 18 months','ok'=>true],
                        ] as $sig)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-ink-300" x-text="lang==='fr' ? '{{ $sig['fr'] }}' : '{{ $sig['en'] }}'"></span>
                            <span class="text-xs font-medium {{ $sig['ok'] ? 'text-green-400' : 'text-red-400' }}">
                                {{ $sig['ok'] ? '✓ OK' : '✗ ALERTE' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Card 3: API Gateway --}}
            <div class="feature-card glass rounded-2xl p-7 border border-white/7 group">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500/30 to-emerald-700/20 flex items-center justify-center mb-6 border border-green-500/20 group-hover:border-green-500/40 transition-colors">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="inline-flex items-center gap-1.5 bg-green-500/10 border border-green-500/20 rounded-full px-2.5 py-1 mb-4">
                    <span class="text-green-400 text-xs font-semibold uppercase tracking-wide">API Gateway</span>
                </div>
                <h3 class="font-display text-xl font-700 text-white mb-3" style="font-weight:700"
                    x-text="lang==='fr' ? 'Déployé en 5 minutes' : 'Deployed in 5 minutes'"></h3>
                <p class="text-sm text-ink-300 leading-relaxed"
                   x-text="lang==='fr'
                     ? 'Un endpoint REST, une clé API, une réponse JSON. Intégrez KaziTrust dans votre app mobile ou back-end existant sans refonte architecturale.'
                     : 'One REST endpoint, one API key, one JSON response. Integrate KaziTrust into your mobile app or existing back-end without architectural overhaul.'">
                </p>
                <div class="mt-6 pt-6 border-t border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-1">
                            @foreach(['bg-blue-500','bg-purple-500','bg-green-500'] as $c)
                            <div class="w-6 h-6 rounded-full {{ $c }} border-2 border-ink-900 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">·</span>
                            </div>
                            @endforeach
                        </div>
                        <span class="text-xs text-ink-300"
                              x-text="lang==='fr' ? 'SDK Node, Python & PHP dispo' : 'Node, Python & PHP SDK available'"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Secondary feature row --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-5">
            @php
            $miniFeatures = [
                ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                  'color'=>'kazi',
                  'fr_t'=>'Dashboard analytique','en_t'=>'Analytics dashboard',
                  'fr_d'=>'Visualisez scores, latences et coûts IA par application, en temps réel.',
                  'en_d'=>'Visualize scores, latencies, and AI costs per app, in real time.'],
                ['icon'=>'M5 12h14M12 5l7 7-7 7',
                  'color'=>'amber',
                  'fr_t'=>'Webhooks HMAC signés','en_t'=>'HMAC-signed webhooks',
                  'fr_d'=>'Recevez des alertes instantanées et vérifiables sur votre infrastructure dès qu\'une fraude est détectée.',
                  'en_d'=>'Receive instant, verifiable alerts on your infrastructure the moment fraud is detected.'],
                ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                  'color'=>'green',
                  'fr_t'=>'Multi-tenant natif','en_t'=>'Native multi-tenant',
                  'fr_d'=>'Isolez chaque client dans son propre espace. Gestion des rôles et quotas par tenant via FilamentPHP.',
                  'en_d'=>'Isolate each client in their own workspace. Role and quota management per tenant via FilamentPHP.'],
            ];
            @endphp
            @foreach($miniFeatures as $f)
            @php
            $colorMap = [
                'kazi' => ['bg'=>'bg-kazi-500/15','border'=>'border-kazi-500/25','icon'=>'text-kazi-400'],
                'amber' => ['bg'=>'bg-amber-500/15','border'=>'border-amber-500/25','icon'=>'text-amber-400'],
                'green' => ['bg'=>'bg-green-500/15','border'=>'border-green-500/25','icon'=>'text-green-400'],
            ];
            $c = $colorMap[$f['color']];
            @endphp
            <div class="feature-card glass rounded-2xl p-6 border border-white/7 group flex gap-4">
                <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} border {{ $c['border'] }} flex-shrink-0 flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-display font-600 text-white mb-1.5 text-sm" style="font-weight:600"
                        x-text="lang==='fr' ? '{{ $f['fr_t'] }}' : '{{ $f['en_t'] }}'"></h4>
                    <p class="text-xs text-ink-300 leading-relaxed"
                       x-text="lang==='fr' ? '{{ $f['fr_d'] }}' : '{{ $f['en_d'] }}'"></p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── DEVELOPER EXPERIENCE ────────────────────────────────────────── --}}
<section id="devex" class="py-28 px-4 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-kazi-500/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Terminal --}}
            <div class="order-2 lg:order-1">
                <div class="relative terminal-glow rounded-2xl overflow-hidden" style="background:#0a0f1e">
                    {{-- Title bar --}}
                    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-white/5" style="background:#0d1425">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="bg-white/5 rounded-md px-3 py-1 text-center">
                                <span class="text-xs text-ink-400 font-mono">POST /api/v1/trust/analyze</span>
                            </div>
                        </div>
                    </div>

                    {{-- Scan line --}}
                    <div class="relative overflow-hidden" style="height:2px">
                        <div class="scan-line"></div>
                    </div>

                    {{-- Code --}}
                    <div class="p-6 font-mono text-xs sm:text-sm space-y-1 leading-relaxed">
                        <div class="text-ink-500 text-xs mb-4">{{-- cURL · request --}}</div>

                        <div><span class="text-kazi-400">curl</span> <span class="text-green-400">-X POST</span> <span class="text-amber-300">\</span></div>
                        <div class="pl-4"><span class="text-amber-300">"https://api.kazitrust.io/v1/trust/analyze"</span> <span class="text-amber-300">\</span></div>
                        <div class="pl-4"><span class="text-kazi-400">-H</span> <span class="text-green-400">"Authorization: Bearer sk-kazi-••••••••"</span> <span class="text-amber-300">\</span></div>
                        <div class="pl-4"><span class="text-kazi-400">-H</span> <span class="text-green-400">"Content-Type: application/json"</span> <span class="text-amber-300">\</span></div>
                        <div class="pl-4"><span class="text-kazi-400">-d</span> <span class="text-amber-300">'</span><span class="text-blue-300">{</span></div>
                        <div class="pl-8"><span class="text-yellow-300">"phone_number"</span><span class="text-ink-300">:</span> <span class="text-green-300">"+22961000000"</span><span class="text-ink-300">,</span></div>
                        <div class="pl-8"><span class="text-yellow-300">"context"</span><span class="text-ink-300">:</span> <span class="text-blue-300">{</span></div>
                        <div class="pl-12"><span class="text-yellow-300">"amount"</span><span class="text-ink-300">:</span> <span class="text-orange-300">150000</span><span class="text-ink-300">,</span></div>
                        <div class="pl-12"><span class="text-yellow-300">"currency"</span><span class="text-ink-300">:</span> <span class="text-green-300">"XOF"</span></div>
                        <div class="pl-8"><span class="text-blue-300">}</span></div>
                        <div class="pl-4"><span class="text-blue-300">}</span><span class="text-amber-300">'</span></div>

                        <div class="sep my-5" style="margin:20px 0"></div>
                        <div class="text-ink-500 text-xs">{{-- HTTP 200 · response --}}</div>
                        <div class="mt-3"></div>

                        <div><span class="text-blue-300">{</span></div>
                        <div class="pl-4"><span class="text-yellow-300">"decision"</span><span class="text-ink-300">:</span> <span class="text-green-300">"approve"</span><span class="text-ink-300">,</span></div>
                        <div class="pl-4"><span class="text-yellow-300">"score"</span><span class="text-ink-300">:</span> <span class="text-orange-300">94</span><span class="text-ink-300">,</span></div>
                        <div class="pl-4"><span class="text-yellow-300">"signals"</span><span class="text-ink-300">:</span> <span class="text-blue-300">{</span></div>
                        <div class="pl-8"><span class="text-yellow-300">"sim_swap"</span><span class="text-ink-300">:</span> <span class="text-green-300">false</span><span class="text-ink-300">,</span></div>
                        <div class="pl-8"><span class="text-yellow-300">"roaming"</span><span class="text-ink-300">:</span> <span class="text-green-300">false</span><span class="text-ink-300">,</span></div>
                        <div class="pl-8"><span class="text-yellow-300">"account_age_months"</span><span class="text-ink-300">:</span> <span class="text-orange-300">18</span></div>
                        <div class="pl-4"><span class="text-blue-300">}</span><span class="text-ink-300">,</span></div>
                        <div class="pl-4"><span class="text-yellow-300">"reasoning"</span><span class="text-ink-300">:</span> <span class="text-green-300">"Aucun SIM swap détecté. Numéro actif depuis 18 mois. Transaction dans les limites normales."</span><span class="text-ink-300">,</span></div>
                        <div class="pl-4"><span class="text-yellow-300">"latency_ms"</span><span class="text-ink-300">:</span> <span class="text-orange-300">1 243</span></div>
                        <div><span class="text-blue-300">}</span> <span class="cursor"></span></div>
                    </div>
                </div>
            </div>

            {{-- Right: Copy --}}
            <div class="order-1 lg:order-2">
                <div class="inline-flex items-center gap-2 glass rounded-full px-3.5 py-1.5 mb-6">
                    <div class="w-1.5 h-1.5 rounded-full bg-green-400"></div>
                    <span class="text-xs text-green-400 font-medium uppercase tracking-widest"
                          x-text="lang==='fr' ? 'Expérience développeur' : 'Developer experience'"></span>
                </div>

                <h2 class="font-display text-4xl sm:text-5xl font-800 text-white leading-tight mb-6" style="font-weight:800">
                    <span x-show="lang==='fr'" x-cloak>Une ligne.<br><span class="gradient-text">Une décision.</span><br>Zéro complexité.</span>
                    <span x-show="lang==='en'" x-cloak>One line.<br><span class="gradient-text">One decision.</span><br>Zero complexity.</span>
                </h2>

                <p class="text-ink-200 leading-relaxed mb-8"
                   x-text="lang==='fr'
                     ? 'L\'API KaziTrust est conçue pour les équipes tech exigeantes. Documentée, versionnée, testable en sandbox. Votre back-end envoie un numéro, reçoit une décision argumentée par l\'IA.'
                     : 'The KaziTrust API is designed for demanding tech teams. Documented, versioned, testable in sandbox. Your back-end sends a number, receives an AI-reasoned decision.'">
                </p>

                <ul class="space-y-4 mb-8">
                    @php
                    $devFeatures = [
                        ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                          'fr'=>'Clé API générée en 30 secondes','en'=>'API key generated in 30 seconds'],
                        ['icon'=>'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                          'fr'=>'SDK officiel Node.js, Python, PHP','en'=>'Official Node.js, Python, PHP SDK'],
                        ['icon'=>'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z',
                          'fr'=>'Environnement sandbox Nokia CAMARA','en'=>'Nokia CAMARA sandbox environment'],
                        ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                          'fr'=>'Logs et métriques temps réel','en'=>'Real-time logs and metrics'],
                    ];
                    @endphp
                    @foreach($devFeatures as $f)
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-kazi-500/20 border border-kazi-500/30 flex-shrink-0 flex items-center justify-center mt-0.5">
                            <svg class="w-3 h-3 text-kazi-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-sm text-ink-200"
                              x-text="lang==='fr' ? '{{ $f['fr'] }}' : '{{ $f['en'] }}'"></span>
                    </li>
                    @endforeach
                </ul>

                <a href="/docs"
                   class="inline-flex items-center gap-2 text-kazi-400 hover:text-kazi-300 font-medium text-sm transition-colors group">
                    <span x-text="lang==='fr' ? 'Lire la documentation complète' : 'Read the full documentation'"></span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ─── PRICING ─────────────────────────────────────────────────────── --}}
<section id="pricing" class="py-28 px-4 relative">
    <div class="absolute inset-0 grid-lines opacity-20"></div>
    <div class="max-w-7xl mx-auto relative z-10">

        <div class="text-center max-w-2xl mx-auto mb-16">
            <div class="inline-flex items-center gap-2 glass rounded-full px-3.5 py-1.5 mb-6">
                <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                <span class="text-xs text-amber-400 font-medium uppercase tracking-widest"
                      x-text="lang==='fr' ? 'Tarifs' : 'Pricing'"></span>
            </div>
            <h2 class="font-display text-4xl sm:text-5xl font-800 text-white mb-4" style="font-weight:800">
                <span x-show="lang==='fr'" x-cloak>Simple, transparent,<br><span class="gradient-text">sans surprise.</span></span>
                <span x-show="lang==='en'" x-cloak>Simple, transparent,<br><span class="gradient-text">no surprises.</span></span>
            </h2>
            <p class="text-ink-300"
               x-text="lang==='fr'
                   ? '14 jours d\'essai gratuit, sans carte bancaire. Passez à un plan supérieur à tout moment.'
                   : '14-day free trial, no credit card. Upgrade anytime.'">
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-{{ $plans->count() }} gap-5">
            @foreach($plans as $plan)
            @php $isPro = $plan->slug === 'pro'; $features = $plan->features ?? []; @endphp


            
            <div class="relative rounded-2xl p-7 flex flex-col border transition-all
                {{ $isPro
                    ? 'border-kazi-500/40 shadow-2xl shadow-kazi-500/15'
                    : 'border-white/7 glass' }}"
                 style="{{ $isPro ? 'background:linear-gradient(160deg, rgba(17,24,39,0.95), rgba(20,35,80,0.9))' : '' }}">
                 


                 
                @if($isPro)
                {{-- Top glow --}}
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-px bg-gradient-to-r from-transparent via-kazi-500 to-transparent"></div>
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                    <span class="btn-shimmer text-white text-xs font-semibold px-4 py-1.5 rounded-full shadow-lg shadow-kazi-500/30"
                          x-text="lang==='fr' ? '✦ Populaire' : '✦ Popular'"></span>
                </div>
                @endif

                {{-- Plan header --}}
                <div class="mb-6 pt-2">
                    <h3 class="font-display text-xl font-700 text-white" style="font-weight:700">{{ $plan->name }}</h3>
                    <p class="text-sm text-ink-400 mt-1">{{ $plan->description }}</p>
                </div>

                {{-- Price --}}
                <div class="mb-7 pb-7 border-b border-white/5">
                    <div class="flex items-baseline gap-1.5">
                        @if($plan->price_monthly == 0)
                            <span class="font-display text-4xl font-800 text-white" style="font-weight:800"
                                  x-text="lang==='fr' ? 'Gratuit' : 'Free'"></span>
                        @else
                            <span class="font-display text-4xl font-800 text-white" style="font-weight:800">
                                {{ number_format($plan->price_monthly, 0, '.', ' ') }}
                            </span>
                            <span class="text-ink-400 text-sm">XOF / <span x-text="lang==='fr' ? 'mois' : 'month'"></span></span>
                        @endif
                    </div>
                    @if($plan->price_yearly > 0)
                    <p class="text-xs text-ink-500 mt-1.5">
                        <span x-text="lang==='fr' ? 'Annuel :' : 'Annual:'"></span>
                        <span class="text-green-400 font-medium">{{ number_format($plan->price_yearly, 0, '.', ' ') }} XOF</span>
                        <span x-text="lang==='fr' ? '/ an — économisez 2 mois' : '/ yr — save 2 months'"></span>
                    </p>
                    @endif
                </div>

                {{-- Features --}}
                <ul class="space-y-3 flex-1 mb-8">
                    @php
                    $planFeatures = [
                        ['val' => ($plan->max_apps === -1 ? '∞' : $plan->max_apps), 'fr_suffix'=>'application(s)', 'en_suffix'=>'application(s)'],
                        ['val' => ($plan->max_requests_per_month === -1 ? '∞' : number_format($plan->max_requests_per_month)), 'fr_suffix'=>'analyses/mois', 'en_suffix'=>'analyses/month'],
                        ['val' => ($plan->max_users === -1 ? '∞' : $plan->max_users), 'fr_suffix'=>'utilisateur(s)', 'en_suffix'=>'user(s)'],
                    ];
                    @endphp
                    @foreach($planFeatures as $pf)
                    <li class="flex items-center gap-3 text-sm text-ink-200">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span><strong class="text-white">{{ $pf['val'] }}</strong> <span x-text="lang==='fr' ? '{{ $pf['fr_suffix'] }}' : '{{ $pf['en_suffix'] }}'"></span></span>
                    </li>
                    @endforeach

                    @if(!empty($features['webhook']))
                    <li class="flex items-center gap-3 text-sm text-ink-200">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Webhooks HMAC
                    </li>
                    @endif
                    @if(!empty($features['multi_llm']))
                    <li class="flex items-center gap-3 text-sm text-ink-200">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Multi-LLM (OpenAI, Gemini, Claude)
                    </li>
                    @endif
                    @if(!empty($features['priority_support']))
                    <li class="flex items-center gap-3 text-sm text-ink-200">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span x-text="lang==='fr' ? 'Support prioritaire 24/7' : 'Priority support 24/7'"></span>
                    </li>
                    @endif
                </ul>

                <a href="{{ route('filament.management.auth.register') }}"
                   class="block text-center py-3 px-4 rounded-xl font-semibold text-sm transition-all
                          {{ $isPro
                             ? 'btn-shimmer text-white shadow-lg shadow-kazi-500/25 hover:shadow-kazi-500/40'
                             : 'bg-white/5 text-ink-100 hover:bg-white/10 border border-white/10 hover:border-white/20' }}"
                   x-text="lang==='fr' ? 'Commencer gratuitement' : 'Start for free'">
                </a>
            </div>
            @endforeach
        </div>

        <p class="text-center text-xs text-ink-500 mt-8"
           x-text="lang==='fr'
               ? '⚡ Paiements simulés en mode prototype. FedaPay & Stripe disponibles en production.'
               : '⚡ Payments simulated in prototype mode. FedaPay & Stripe available in production.'">
        </p>
    </div>
</section>

{{-- ─── FAQ ─────────────────────────────────────────────────────────── --}}
<section id="faq" class="py-28 px-4 relative" x-data="{ faq: null }">
    <div class="max-w-3xl mx-auto relative z-10">

        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 glass rounded-full px-3.5 py-1.5 mb-6">
                <div class="w-1.5 h-1.5 rounded-full bg-kazi-400"></div>
                <span class="text-xs text-kazi-400 font-medium uppercase tracking-widest">FAQ</span>
            </div>
            <h2 class="font-display text-4xl sm:text-5xl font-800 text-white" style="font-weight:800">
                <span x-show="lang==='fr'" x-cloak>Questions fréquentes</span>
                <span x-show="lang==='en'" x-cloak>Frequently asked questions</span>
            </h2>
        </div>

        @php
        $faqs = [
            [
                'fr_q'=>'Qu\'est-ce que le modèle BYO-AI et pourquoi est-ce important pour mon DSI ?',
                'en_q'=>'What is the BYO-AI model and why does it matter to my CTO?',
                'fr_a'=>'BYO-AI (Bring Your Own AI) signifie que vous fournissez votre propre clé API (OpenAI, Gemini, Claude). Vos données de transaction ne transitent jamais par nos serveurs pour l\'inférence IA — elles vont directement chez le fournisseur de votre choix. Cela garantit la souveraineté de vos données, le contrôle total de vos coûts IA, et vous affranchit de tout risque de lock-in sur notre plateforme.',
                'en_a'=>'BYO-AI means you supply your own API key (OpenAI, Gemini, Claude). Your transaction data never flows through our servers for AI inference — it goes directly to your chosen provider. This guarantees data sovereignty, full AI cost ownership, and freedom from lock-in risk on our platform.',
            ],
            [
                'fr_q'=>'Comment fonctionne l\'intégration Nokia CAMARA ? Faut-il un accord opérateur ?',
                'en_q'=>'How does the Nokia CAMARA integration work? Do I need an operator agreement?',
                'fr_a'=>'Nokia CAMARA est un standard d\'API ouvert (initiative GSMA) permettant aux développeurs d\'accéder à des données réseau anonymisées : statut SIM, roaming, âge du compte. KaziTrust s\'intègre via l\'API Nokia Network as Code, qui agrège déjà des accords multi-opérateurs en Afrique de l\'Ouest. En mode prototype, un environnement sandbox est disponible sans aucun accord préalable.',
                'en_a'=>'Nokia CAMARA is an open API standard (GSMA initiative) allowing developers to access anonymized network data: SIM status, roaming, account age. KaziTrust integrates via the Nokia Network as Code API, which already aggregates multi-operator agreements in West Africa. In prototype mode, a sandbox environment is available with no prior agreement required.',
            ],
            [
                'fr_q'=>'Nos données clients sont-elles sécurisées ? Êtes-vous conformes aux réglementations locales ?',
                'en_q'=>'Is our customer data secure? Are you compliant with local regulations?',
                'fr_a'=>'Toutes les clés API IA que vous nous confiez sont chiffrées AES-256 au repos. Les numéros de téléphone analysés sont hashés en transit. KaziTrust est architecturé pour respecter le RGPD et les recommandations de la CEDEAO sur la protection des données. Chaque tenant dispose d\'une isolation complète : vos données ne sont jamais mélangées à celles d\'un autre client.',
                'en_a'=>'All AI API keys you entrust us with are AES-256 encrypted at rest. Phone numbers analyzed are hashed in transit. KaziTrust is architected to comply with GDPR and ECOWAS data protection recommendations. Each tenant is fully isolated: your data is never commingled with another client\'s.',
            ],
            [
                'fr_q'=>'Combien de temps faut-il pour intégrer l\'API KaziTrust dans mon système existant ?',
                'en_q'=>'How long does it take to integrate the KaziTrust API into my existing system?',
                'fr_a'=>'Pour un développeur expérimenté, la première analyse en production est réalisable en moins de 30 minutes : création du compte, génération de la clé API, premier appel cURL. Nos SDK officiels (Node.js, Python, PHP) et notre documentation interactive réduisent encore davantage le temps d\'intégration. Aucune modification de votre base de données ou de votre architecture n\'est requise.',
                'en_a'=>'For an experienced developer, the first production analysis is achievable in under 30 minutes: account creation, API key generation, first cURL call. Our official SDKs (Node.js, Python, PHP) and interactive documentation further reduce integration time. No changes to your database or architecture are required.',
            ],
            [
                'fr_q'=>'Que se passe-t-il si le service IA ou Nokia CAMARA est indisponible ?',
                'en_q'=>'What happens if the AI service or Nokia CAMARA is unavailable?',
                'fr_a'=>'KaziTrust implémente un mécanisme de fallback intelligent : si votre LLM primaire est indisponible, le système peut basculer sur un LLM de secours configuré. Si les signaux CAMARA sont partiellement indisponibles, l\'analyse s\'effectue sur les signaux disponibles avec un score de confiance ajusté, signalé dans la réponse. Vous gardez le contrôle via des règles de décision personnalisables.',
                'en_a'=>'KaziTrust implements intelligent fallback: if your primary LLM is unavailable, the system can switch to a configured backup LLM. If CAMARA signals are partially unavailable, analysis proceeds on available signals with an adjusted confidence score, reported in the response. You remain in control via customizable decision rules.',
            ],
            [
                'fr_q'=>'Les plans incluent-ils un SLA de disponibilité et un support technique ?',
                'en_q'=>'Do plans include an uptime SLA and technical support?',
                'fr_a'=>'Le plan Gratuit inclut un support communautaire et un accès à notre documentation. Les plans payants incluent un SLA de 99,5% de disponibilité et un support par e-mail avec délai de réponse garanti sous 24h (Pro) ou 4h (Entreprise). Le plan Entreprise donne également accès à un Technical Account Manager dédié pour l\'accompagnement à l\'intégration.',
                'en_a'=>'The Free plan includes community support and documentation access. Paid plans include a 99.5% uptime SLA and email support with guaranteed response times of 24h (Pro) or 4h (Enterprise). The Enterprise plan also provides access to a dedicated Technical Account Manager for integration support.',
            ],
        ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $i => $faqItem)
            <div class="glass rounded-2xl border border-white/7 overflow-hidden"
                 x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-white/3 transition-colors group">
                    <span class="font-display font-600 text-white text-sm sm:text-base pr-4 group-hover:text-kazi-300 transition-colors" style="font-weight:600"
                          x-text="lang==='fr' ? '{{ addslashes($faqItem['fr_q']) }}' : '{{ addslashes($faqItem['en_q']) }}'"></span>
                    <div class="flex-shrink-0 w-6 h-6 rounded-full border border-white/10 flex items-center justify-center transition-all duration-300"
                         :class="open ? 'rotate-45 bg-kazi-500/20 border-kazi-500/30' : ''">
                        <svg class="w-3.5 h-3.5 text-ink-300" :class="open ? 'text-kazi-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                </button>
                <div x-show="open"
                     x-collapse
                     x-cloak>
                    <div class="px-6 pb-5 border-t border-white/5 pt-4">
                        <p class="text-sm text-ink-300 leading-relaxed"
                           x-text="lang==='fr' ? '{{ addslashes($faqItem['fr_a']) }}' : '{{ addslashes($faqItem['en_a']) }}'"></p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── CTA FINAL ───────────────────────────────────────────────────── --}}
<section class="py-28 px-4 relative overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0" style="background:radial-gradient(ellipse 80% 70% at 50% 100%, rgba(63,105,245,0.15) 0%, transparent 70%), #060a14"></div>
    <div class="absolute inset-0 grid-lines opacity-20"></div>

    {{-- Top separator --}}
    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-kazi-500/30 to-transparent"></div>

    <div class="max-w-3xl mx-auto text-center relative z-10">
        <div class="inline-flex items-center gap-2 glass rounded-full px-3.5 py-1.5 mb-8">
            <div class="status-dot"></div>
            <span class="text-xs text-green-400 font-medium"
                  x-text="lang==='fr' ? 'Aucune carte bancaire requise · Accès immédiat' : 'No credit card required · Instant access'"></span>
        </div>

        <h2 class="font-display text-5xl sm:text-6xl lg:text-7xl font-800 text-white leading-tight mb-6" style="font-weight:800">
            <span x-show="lang==='fr'" x-cloak>Sécurisez<br><span class="gradient-text">vos transactions</span><br>dès aujourd'hui.</span>
            <span x-show="lang==='en'" x-cloak>Secure your<br><span class="gradient-text">transactions</span><br>starting today.</span>
        </h2>

        <p class="text-ink-200 text-lg mb-10"
           x-text="lang==='fr'
               ? 'Créez votre espace en 2 minutes. 14 jours d\'essai complet, gratuit. Vos clés Nokia et IA restent vôtres.'
               : 'Create your workspace in 2 minutes. 14-day full trial, free. Your Nokia and AI keys stay yours.'">
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('filament.management.auth.register') }}"
               class="btn-shimmer inline-flex items-center justify-center gap-2.5 text-white font-semibold px-8 py-4 rounded-xl shadow-2xl shadow-kazi-500/30 hover:shadow-kazi-500/50 transition-shadow text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span x-text="lang==='fr' ? 'Créer mon espace gratuitement' : 'Create my free workspace'"></span>
            </a>
            <a href="/docs"
               class="inline-flex items-center justify-center gap-2 text-ink-100 font-medium px-8 py-4 rounded-xl border border-white/10 hover:border-white/20 hover:bg-white/5 transition-all text-base">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span x-text="lang==='fr' ? 'Voir la documentation' : 'View the docs'"></span>
            </a>
        </div>
    </div>
</section>

{{-- ─── FOOTER ──────────────────────────────────────────────────────── --}}
<footer class="border-t border-white/5 py-16 px-4" style="background:#060a14">
    <div class="max-w-7xl mx-auto">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-14">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <a href="/" class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-kazi-500 flex items-center justify-center shadow-lg shadow-kazi-500/20">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="font-display font-700 text-white text-lg" style="font-weight:700">KaziTrust</span>
                </a>
                <p class="text-sm text-ink-400 leading-relaxed mb-5"
                   x-text="lang==='fr'
                       ? 'API anti-fraude mobile pour les PME & IMF d\'Afrique francophone. Propulsé par Nokia CAMARA & votre IA.'
                       : 'Mobile fraud prevention API for SMEs & MFIs in francophone Africa. Powered by Nokia CAMARA & your AI.'">
                </p>
                <div class="flex items-center gap-2">
                    <div class="status-dot" style="width:6px;height:6px;box-shadow:0 0 0 2px rgba(34,197,94,0.2)"></div>
                    <span class="text-xs text-green-400"
                          x-text="lang==='fr' ? 'Tous les systèmes opérationnels' : 'All systems operational'"></span>
                </div>
            </div>

            {{-- Links col 1 --}}
            <div>
                <h5 class="text-white font-display font-600 text-sm mb-4 uppercase tracking-widest" style="font-weight:600"
                    x-text="lang==='fr' ? 'Produit' : 'Product'"></h5>
                <ul class="space-y-3">
                    @foreach([
                        ['fr'=>'Fonctionnalités','en'=>'Features','href'=>'#benefits'],
                        ['fr'=>'Tarifs','en'=>'Pricing','href'=>'#pricing'],
                        ['fr'=>'Documentation API','en'=>'API Docs','href'=>'/docs'],
                        ['fr'=>'Statut du service','en'=>'Service status','href'=>'/status'],
                        ['fr'=>'Changelog','en'=>'Changelog','href'=>'/changelog'],
                    ] as $l)
                    <li><a href="{{ $l['href'] }}" class="text-sm text-ink-400 hover:text-white transition-colors"
                           x-text="lang==='fr' ? '{{ $l['fr'] }}' : '{{ $l['en'] }}'"></a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Links col 2 --}}
            <div>
                <h5 class="text-white font-display font-600 text-sm mb-4 uppercase tracking-widest" style="font-weight:600"
                    x-text="lang==='fr' ? 'Développeurs' : 'Developers'"></h5>
                <ul class="space-y-3">
                    @foreach([
                        ['fr'=>'Guide de démarrage','en'=>'Quick start','href'=>'/docs/quickstart'],
                        ['fr'=>'Référence API','en'=>'API reference','href'=>'/docs/api'],
                        ['fr'=>'SDK & Bibliothèques','en'=>'SDKs & Libraries','href'=>'/docs/sdks'],
                        ['fr'=>'Webhooks','en'=>'Webhooks','href'=>'/docs/webhooks'],
                        ['fr'=>'Environnement sandbox','en'=>'Sandbox environment','href'=>'/docs/sandbox'],
                    ] as $l)
                    <li><a href="{{ $l['href'] }}" class="text-sm text-ink-400 hover:text-white transition-colors"
                           x-text="lang==='fr' ? '{{ $l['fr'] }}' : '{{ $l['en'] }}'"></a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Links col 3 --}}
            <div>
                <h5 class="text-white font-display font-600 text-sm mb-4 uppercase tracking-widest" style="font-weight:600"
                    x-text="lang==='fr' ? 'Légal & Entreprise' : 'Legal & Company'"></h5>
                <ul class="space-y-3">
                    @foreach([
                        ['fr'=>'À propos','en'=>'About','href'=>'/about'],
                        ['fr'=>'Confidentialité','en'=>'Privacy policy','href'=>'/privacy'],
                        ['fr'=>'Conditions d\'utilisation','en'=>'Terms of service','href'=>'/terms'],
                        ['fr'=>'Sécurité','en'=>'Security','href'=>'/security'],
                        ['fr'=>'Contact','en'=>'Contact','href'=>'mailto:hello@kazitrust.io'],
                    ] as $l)
                    <li><a href="{{ $l['href'] }}" class="text-sm text-ink-400 hover:text-white transition-colors"
                           x-text="lang==='fr' ? '{{ $l['fr'] }}' : '{{ $l['en'] }}'"></a></li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="sep mb-6"></div>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-ink-500">
                © {{ date('Y') }} KaziTrust.
                <span x-text="lang==='fr' ? 'Tous droits réservés.' : 'All rights reserved.'"></span>
                <span class="text-ink-600 mx-2">·</span>
                <span x-text="lang==='fr' ? 'Cotonou, Bénin 🇧🇯' : 'Cotonou, Benin 🇧🇯'"></span>
            </p>

            <div class="flex items-center gap-4">
                {{-- Lang toggle --}}
                <button @click="lang = lang === 'fr' ? 'en' : 'fr'; localStorage.setItem('kazi_lang', lang)"
                        class="text-xs text-ink-400 hover:text-white flex items-center gap-1.5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    <span x-text="lang === 'fr' ? 'Switch to English' : 'Passer en Français'"></span>
                </button>

                <a href="{{ route('filament.management.auth.login') }}"
                   class="text-xs text-ink-400 hover:text-white transition-colors"
                   x-text="lang==='fr' ? 'Connexion' : 'Sign in'"></a>

                <a href="{{ route('filament.management.auth.register') }}"
                   class="text-xs font-medium text-kazi-400 hover:text-kazi-300 transition-colors"
                   x-text="lang==='fr' ? 'Essai gratuit →' : 'Free trial →'"></a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>