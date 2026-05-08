<?php

use Knuckles\Scribe\Config\AuthIn;
use Knuckles\Scribe\Config\Defaults;
use Knuckles\Scribe\Extracting\Strategies;

use function Knuckles\Scribe\Config\configureStrategy;

/**
 * ╔══════════════════════════════════════════════════════════════════╗
 * ║           KaziTrust — Configuration Scribe API Docs             ║
 * ║  Hub de Confiance SaaS B2B | Anti-Fraude Mobile | Nokia CAMARA  ║
 * ╚══════════════════════════════════════════════════════════════════╝
 *
 * Ce fichier configure la génération automatique de la documentation
 * de l'API KaziTrust via le package Scribe (knuckleswtf/scribe).
 *
 * Architecture : Laravel + Filament PHP | Multi-tenant SaaS
 * Couverture  : API v1 — Routes préfixées `api/v1/`
 */

return [

    // =========================================================================
    //  IDENTITÉ DU PROJET
    // =========================================================================

    'title' => 'KaziTrust — Référence API',

    'description' => <<<'DESC'
        **KaziTrust** est un Hub de Confiance SaaS B2B qui abstrait la complexité des API réseau
        Nokia CAMARA derrière une interface unifiée, propulsée par des agents IA.
        Permettez à votre application de détecter la fraude mobile (SIM Swap, usurpation d'identité)
        et de vérifier l'identité de vos utilisateurs en quelques lignes de code,
        sans jamais gérer directement les opérateurs télécoms.
        DESC,

    'intro_text' => <<<'INTRO'

        <p style="text-align:right">
            🌐 <a href="/docs/en"><strong>Read in English</strong></a>
        </p>

        ## Bienvenue sur la documentation de l'API KaziTrust

        KaziTrust démocratise l'accès aux capacités avancées des réseaux télécoms en Afrique subsaharienne.
        Notre plateforme orchestre simultanément plusieurs signaux CAMARA de Nokia — **SIM Swap**,
        **Number Verification**, **KYC Match** et **Location Verification** — et vous renvoie
        une **décision métier claire et actionnable** en temps réel.

        ### Ce que vous pouvez faire avec cette API

        - 🔐 **Anti-Fraude & SIM Swap** : Détectez les échanges de carte SIM suspects avant d'approuver
          une transaction ou un prêt. Recevez un score de confiance et un motif de rejet précis.
        - 📍 **Vérification de Localisation** : Confirmez silencieusement la cohérence géographique
          d'un utilisateur via le réseau, sans GPS ni consentement intrusif.
        - 🪪 **KYC & Correspondance d'Identité** : Vérifiez qu'un numéro de téléphone correspond
          bien à l'identité déclarée par l'utilisateur (nom, date de naissance, etc.).
        - 📲 **Vérification de Numéro (Silent Auth)** : Remplacez vos SMS OTP coûteux et vulnérables
          par une vérification silencieuse du numéro via le réseau de l'opérateur.
        - 📊 **Score de Confiance Global** : Notre agent IA analyse tous ces signaux en parallèle
          et renvoie une décision unifiée (ex: *"Score 85 % — Approuvé"* ou *"Rejeté — SIM Swap détecté il y a 2 h"*).

        ### Cas d'usage typiques

        | Secteur | Cas d'usage |
        |---|---|
        | Micro-crédit / IMF | Approbation de prêt instantanée avec vérification SIM + localisation |
        | E-commerce & Paiements | Blocage automatique des transactions à haut risque |
        | Onboarding digital | Remplacement des OTP SMS par une auth silencieuse |
        | Mobile Money | Protection des transferts contre l'usurpation d'identité |

        ### Authentification

        Toutes les requêtes doivent inclure votre clé API KaziTrust dans le header `Authorization` :

        ```
        Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
        ```

        Obtenez ou régénérez vos clés depuis votre **Tableau de Bord Tenant → Mes Services API → Clés API**.

        <aside class="notice">
        Chaque tenant dispose de clés API isolées. Ne partagez jamais votre clé <code>kz_</code>.
        En cas de compromission, révoquez-la immédiatement depuis votre tableau de bord.
        </aside>

        <aside class="success">
        Vous pouvez tester chaque endpoint directement depuis cette page grâce au bouton
        <strong>Try it out</strong>. Assurez-vous d'avoir renseigné votre clé API en haut de page.
        </aside>
        INTRO,

    // =========================================================================
    //  URL DE BASE
    // =========================================================================

    'base_url' => config('app.url'),

    // =========================================================================
    //  ROUTES DOCUMENTÉES
    //  On documente toutes les routes API versionnées (api/v1/*)
    // =========================================================================

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains'  => ['*'],
            ],
            'include' => [],
            // Exclure les routes internes / de santé si nécessaire
            'exclude' => [
                'api/v1/internal/*',
                'api/v1/health',
            ],
        ],
    ],

    // =========================================================================
    //  TYPE & THÈME
    //  'laravel' → Scribe génère des routes dans votre app et sert la doc via /docs
    // =========================================================================

    'type'  => 'laravel',
    'theme' => 'default',

    'static' => [
       // 'output_path' => 'public/docs',

        // Changez ce chemin pour que Scribe n'utilise pas le dossier "docs" par défaut
        'output_path' => 'storage/app/scribe/public',
        
    ],

    'laravel' => [
        'add_routes'       => true,
        'docs_url'         => '/docs',
        'assets_directory' => null,
        // Ajoutez ici les middlewares si la doc doit être protégée en production
        // ex: 'middleware' => ['auth:sanctum', 'role:admin'],
        'middleware' => [],
    ],

    'external' => [
        'html_attributes' => [],
    ],

    // =========================================================================
    //  TRY IT OUT
    //  Permet aux partenaires de tester l'API directement depuis la documentation
    // =========================================================================

    'try_it_out' => [
        'enabled'  => true,
        'base_url' => null, // null = utilise base_url de l'app
        'use_csrf' => false,
        'csrf_url' => '/sanctum/csrf-cookie',
    ],

    // =========================================================================
    //  AUTHENTIFICATION
    //  Toutes les clés KaziTrust sont des Bearer tokens préfixés "kz_"
    // =========================================================================

    'auth' => [
        'enabled'    => true,
        'default'    => true, // Chaque endpoint doit être explicitement marqué @authenticated
        'in'         => AuthIn::BEARER->value,
        'name'       => 'Authorization',
        // La clé de démo est injectée via la variable d'environnement SCRIBE_AUTH_KEY
        // Définissez-la dans votre .env.testing ou dans votre pipeline CI/CD
        'use_value'  => env('SCRIBE_AUTH_KEY', 'kz_demo_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
        'placeholder' => 'kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        'extra_info' => <<<'AUTH'
            Obtenez votre clé API depuis le **Tableau de Bord Tenant → Management → Mes Services API → Clés API**.

            Toutes les clés KaziTrust sont préfixées `kz_` suivi de 32 caractères alphanumériques.
            Chaque clé est liée à un tenant isolé (votre entreprise) et ne donne accès qu'à vos données.

            AUTH,
    ],

    // =========================================================================
    //  LANGAGES DES EXEMPLES DE CODE
    //  Couvre les principaux profils d'intégrateurs KaziTrust (IMF, PME, Dev web)
    // =========================================================================

    'example_languages' => [
        'bash',        // curl — universel, idéal pour les tests rapides
        'javascript',  // Fetch API — intégration frontend / Node.js
        'php',         // Laravel HTTP Client — intégrations backend PHP
        'python',      // Requests — data scientists et intégrations analytics
    ],

    // =========================================================================
    //  EXPORTS — Postman & OpenAPI
    //  Très utile pour les équipes techniques de vos partenaires IMF/PME
    // =========================================================================

    'postman' => [
        'enabled'   => false,
        'overrides' => [
            'info.version' => '1.0.0',
            'info.contact' => [
                'name'  => 'Support KaziTrust',
                'email' => 'dev@kazitrust.io',
                'url'   => 'https://kazitrust.io/support',
            ],
        ],
    ],

    'openapi' => [
        'enabled'    => false,
        'version'    => '3.0.3',
        'overrides'  => [
            'info' => [
                'version'        => '1.0.0',
                'termsOfService' => 'https://kazitrust.io/terms',
                'contact'        => [
                    'name'  => 'Équipe API KaziTrust',
                    'email' => 'dev@kazitrust.io',
                    'url'   => 'https://kazitrust.io/support',
                ],
                'license' => [
                    'name' => 'Propriétaire — Voir conditions d\'utilisation',
                    'url'  => 'https://kazitrust.io/terms',
                ],
                'x-logo' => [
                    'url'             => '/img/kazitrust-logo.png',
                    'backgroundColor' => '#0A0F1E',
                    'altText'         => 'KaziTrust Logo',
                ],
            ],
            'tags' => [
                ['name' => 'Authentification',         'description' => 'Gestion des clés API et sessions'],
                ['name' => 'Score de Confiance',        'description' => 'Analyse IA multi-signaux — décision unifiée anti-fraude'],
                ['name' => 'SIM Swap',                  'description' => 'Détection de remplacement de carte SIM suspect'],
                ['name' => 'Vérification de Numéro',   'description' => 'Silent Auth — remplacement des OTP SMS'],
                ['name' => 'KYC & Correspondance',      'description' => 'Vérification d\'identité par le réseau'],
                ['name' => 'Vérification de Localisation', 'description' => 'Cohérence géographique via signal réseau'],
                ['name' => 'Webhooks',                  'description' => 'Notifications d\'événements en temps réel'],
                ['name' => 'Tenant & Configuration',    'description' => 'Gestion de votre espace tenant'],
                ['name' => 'Logs & Monitoring',         'description' => 'Audit trail et logs de fraudes bloquées'],
            ],
        ],
        'generators' => [],
    ],

    // =========================================================================
    //  GROUPES D'ENDPOINTS
    //  Reflète l'architecture fonctionnelle de KaziTrust
    // =========================================================================

    'groups' => [
        'default' => 'Divers',
        'order'   => [
            'Authentification',
            'Score de Confiance',
            'SIM Swap',
            'Vérification de Numéro',
            'KYC & Correspondance',
            'Vérification de Localisation',
            'Webhooks',
            'Tenant & Configuration',
            'Logs & Monitoring',
        ],
    ],

    // =========================================================================
    //  LOGO
    //  Placez votre logo dans public/img/kazitrust-logo.png et décommentez :
    // =========================================================================

    'logo' => '/img/kazitrust-logo.png',
    // 'logo' => false,

    // =========================================================================
    //  DATE DE MISE À JOUR
    // =========================================================================

    'last_updated' => 'Dernière mise à jour : {date:d/m/Y}',

    // =========================================================================
    //  EXEMPLES & DONNÉES FICTIVES
    //  faker_seed fixe garantit des exemples stables entre les regénérations
    // =========================================================================

    'examples' => [
        'faker_seed'    => 1234,
        'models_source' => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    // =========================================================================
    //  STRATÉGIES D'EXTRACTION
    //  ResponseCalls limitées aux GET pour éviter les effets de bord en génération
    // =========================================================================

    'strategies' => [
        'metadata' => [
            ...Defaults::METADATA_STRATEGIES,
        ],

        'headers' => [
            ...Defaults::HEADERS_STRATEGIES,
            // Headers requis sur toutes les requêtes KaziTrust
            Strategies\StaticData::withSettings(data: [
                'Content-Type'     => 'application/json',
                'Accept'           => 'application/json',
                'X-KaziTrust-Version' => 'v1',  // Versioning explicite recommandé
            ]),
        ],

        'urlParameters' => [
            ...Defaults::URL_PARAMETERS_STRATEGIES,
        ],

        'queryParameters' => [
            ...Defaults::QUERY_PARAMETERS_STRATEGIES,
        ],

        'bodyParameters' => [
            ...Defaults::BODY_PARAMETERS_STRATEGIES,
        ],

        'responses' => configureStrategy(
            Defaults::RESPONSES_STRATEGIES,
            Strategies\Responses\ResponseCalls::withSettings(
                // On n'effectue des appels réels qu'en GET pour éviter de créer
                // de faux enregistrements de fraude / de faux score pendant la génération
                only: ['GET *'],
                config: [
                    'app.debug' => false,
                    // Assurez-vous qu'un tenant de test existe pour la génération
                    // Définissez SCRIBE_TENANT_ID dans votre .env de test
                ]
            )
        ),

        'responseFields' => [
            ...Defaults::RESPONSE_FIELDS_STRATEGIES,
        ],
    ],

    // =========================================================================
    //  BASE DE DONNÉES
    //  Transaction automatique pour éviter la pollution des données de test
    // =========================================================================

    'database_connections_to_transact' => [config('database.default')],

    // =========================================================================
    //  FRACTAL (Transformers)
    //  Si vous utilisez spatie/laravel-fractal pour vos réponses API,
    //  configurez le sérialiseur ici (ex: ArraySerializer, DataArraySerializer)
    // =========================================================================

    'fractal' => [
        'serializer' => null,
        // 'serializer' => \League\Fractal\Serializer\ArraySerializer::class,
    ],

];