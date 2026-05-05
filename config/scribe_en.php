<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;

/**
 * ╔══════════════════════════════════════════════════════════════════╗
 * ║        KaziTrust — Scribe API Docs Config (ENGLISH)             ║
 * ║  Trust Hub SaaS B2B | Mobile Anti-Fraud | Nokia CAMARA          ║
 * ╚══════════════════════════════════════════════════════════════════╝
 *
 * English version of the API documentation.
 * Generated at: /docs/en
 *
 * To regenerate:
 *   php artisan scribe:generate --config scribe_en
 */

return [

    // =========================================================================
    //  PROJECT IDENTITY
    // =========================================================================

    'title' => 'KaziTrust — API Reference',

    'description' => <<<'DESC'
        **KaziTrust** is a B2B SaaS Trust Hub that abstracts the complexity of Nokia CAMARA
        network APIs behind a unified, AI-powered interface.
        Integrate telecom-grade fraud detection and identity verification
        into your application in just a few lines of code — no direct operator integration required.
        DESC,

    'intro_text' => <<<'INTRO'
        <p style="text-align:right">
            🌐 <a href="/docs"><strong>Lire en Français</strong></a>
        </p>

        ## Welcome to the KaziTrust API Documentation

        KaziTrust democratizes access to advanced telecom network capabilities across Sub-Saharan Africa.
        Our platform simultaneously orchestrates multiple Nokia CAMARA signals — **SIM Swap**,
        **Number Verification**, **KYC Match**, and **Location Verification** — and returns
        a **clear, actionable business decision** in real time.

        ### What you can do with this API

        - 🔐 **Anti-Fraud & SIM Swap Detection**: Detect suspicious SIM card swaps before approving
          a transaction or loan. Receive a trust score and a precise rejection reason.
        - 📍 **Location Verification**: Silently confirm a user's geographic consistency
          via the network — no GPS, no intrusive consent required.
        - 🪪 **KYC & Identity Matching**: Verify that a phone number matches
          the identity declared by the user (name, date of birth, etc.).
        - 📲 **Number Verification (Silent Auth)**: Replace costly, vulnerable SMS OTPs
          with silent phone number verification via the operator's network.
        - 📊 **Global Trust Score**: Our AI agent analyzes all these signals in parallel
          and returns a unified decision (e.g., *"Score 85% — Approved"* or *"Rejected — SIM Swap detected 2h ago"*).

        ### Typical Use Cases

        | Sector | Use Case |
        |---|---|
        | Micro-lending / MFIs | Instant loan approval with SIM + location verification |
        | E-commerce & Payments | Automatic blocking of high-risk transactions |
        | Digital Onboarding | Replacing SMS OTPs with silent authentication |
        | Mobile Money | Protecting transfers against identity theft |

        ### Authentication

        All requests must include your KaziTrust API key in the `Authorization` header:

        ```
        Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        ```

        Generate or rotate your keys from your **Tenant Dashboard → My API Services → API Keys**.

        <aside class="notice">
        Each tenant has isolated API keys. Never share your <code>kz_</code> key.
        If compromised, revoke it immediately from your dashboard.
        </aside>

        <aside class="success">
        You can test every endpoint directly on this page using the
        <strong>Try it out</strong> button. Make sure your API key is set at the top of the page.
        </aside>
        INTRO,

    'base_url' => config('app.url'),

    // =========================================================================
    //  DOCUMENTED ROUTES
    // =========================================================================

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains'  => ['*'],
            ],
            'include' => [],
            'exclude' => [
                'api/v1/internal/*',
                'api/v1/health',
            ],
        ],
    ],

    'type'  => 'laravel',
    'theme' => 'default',

    'static' => [
       /// 'output_path' => 'public/docs/en',
        'output_path' => 'storage/app/scribe/public_en',
    ],

    'laravel' => [
        'add_routes'       => true,
        'docs_url'         => '/docs/en',
        'assets_directory' => null,
        'middleware'       => [],
    ],

    'external' => [
        'html_attributes' => [],
    ],

    'try_it_out' => [
        'enabled'  => true,
        'base_url' => null,
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    // =========================================================================
    //  AUTHENTICATION
    // =========================================================================

    'auth' => [
        'enabled'     => true,
        'default'     => true,
        'in'          => AuthIn::BEARER->value,
        'name'        => 'Authorization',
        'use_value'   => env('SCRIBE_AUTH_KEY', 'kz_demo_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
        'placeholder' => 'kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        'extra_info'  => <<<'AUTH'
            Get your API key from the **Tenant Dashboard → Management → My API Services → API Keys**.

            All KaziTrust keys are prefixed with `kz_` followed by 32 alphanumeric characters.
            Each key is tied to an isolated tenant (your company) and only grants access to your data.

            **Available environments:**
            | Environment | Key prefix | Base URL |
            |---|---|---|
            | Sandbox | `kz_test_` | `https://sandbox.kazitrust.io` |
            | Production | `kz_live_` | `https://api.kazitrust.io` |
            AUTH,
    ],

    // =========================================================================
    //  CODE EXAMPLE LANGUAGES
    // =========================================================================

    'example_languages' => [
        'bash',
        'javascript',
        'php',
        'python',
    ],

    // =========================================================================
    //  EXPORTS — Postman & OpenAPI
    // =========================================================================

    'postman' => [
        'enabled'   => true,
        'overrides' => [
            'info.version' => '1.0.0',
            'info.contact' => [
                'name'  => 'KaziTrust Support',
                'email' => 'dev@kazitrust.io',
                'url'   => 'https://kazitrust.io/support',
            ],
        ],
    ],

    'openapi' => [
        'enabled'    => true,
        'version'    => '3.0.3',
        'overrides'  => [
            'info' => [
                'version'        => '1.0.0',
                'termsOfService' => 'https://kazitrust.io/terms',
                'contact'        => [
                    'name'  => 'KaziTrust API Team',
                    'email' => 'dev@kazitrust.io',
                    'url'   => 'https://kazitrust.io/support',
                ],
                'license' => [
                    'name' => 'Proprietary — See Terms of Use',
                    'url'  => 'https://kazitrust.io/terms',
                ],
                'x-logo' => [
                    'url'             => '/img/kazitrust-logo.png',
                    'backgroundColor' => '#0A0F1E',
                    'altText'         => 'KaziTrust Logo',
                ],
            ],
            'tags' => [
                ['name' => 'Authentication',        'description' => 'API key management and sessions'],
                ['name' => 'Trust Score',           'description' => 'AI multi-signal analysis — unified anti-fraud decision'],
                ['name' => 'SIM Swap',              'description' => 'Detection of suspicious SIM card replacement'],
                ['name' => 'Number Verification',   'description' => 'Silent Auth — SMS OTP replacement'],
                ['name' => 'KYC & Identity Match',  'description' => 'Network-based identity verification'],
                ['name' => 'Location Verification', 'description' => 'Geographic consistency via network signal'],
                ['name' => 'Webhooks',              'description' => 'Real-time event notifications'],
                ['name' => 'Tenant & Settings',     'description' => 'Manage your tenant workspace'],
                ['name' => 'Logs & Monitoring',     'description' => 'Audit trail and blocked fraud logs'],
            ],
        ],
        'generators' => [],
    ],

    // =========================================================================
    //  ENDPOINT GROUPS
    // =========================================================================

    'groups' => [
        'default' => 'Miscellaneous',
        'order'   => [
            'Authentication',
            'Trust Score',
            'SIM Swap',
            'Number Verification',
            'KYC & Identity Match',
            'Location Verification',
            'Webhooks',
            'Tenant & Settings',
            'Logs & Monitoring',
        ],
    ],

     'logo' => '/img/kazitrust-logo.png',
     //'logo' => false,

    'last_updated' => 'Last updated: {date:F j, Y}',

    'examples' => [
        'faker_seed'    => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    // =========================================================================
    //  EXTRACTION STRATEGIES
    // =========================================================================

    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],

        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            Strategies\StaticData::withSettings(data: [
                'Content-Type'        => 'application/json',
                'Accept'              => 'application/json',
                'X-KaziTrust-Version' => 'v1',
            ]),
        ],

        'urlParameters'  => [...Defaults::URL_PARAMETERS_STRATEGIES],
        'queryParameters' => [...Defaults::QUERY_PARAMETERS_STRATEGIES],
        'bodyParameters'  => [...Defaults::BODY_PARAMETERS_STRATEGIES],

        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                only: ['GET *'],
                config: ['app.debug' => false]
            )
        ),

        'responseFields' => [...Defaults::RESPONSE_FIELDS_STRATEGIES],
    ],

    'database_connections_to_transact' => [config('database.default')],

    'fractal' => [
        'serializer' => null,
    ],

];