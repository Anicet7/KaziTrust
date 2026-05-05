<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).






###

# Créer d'abord un compte supramanager en Tinker
### Creaation de compte supramanager
php artisan tinker --execute="
App\Models\User::create([
    'name'      => 'Super Admin',
    'email'     => 'ydourossimi@gmail.com',
    'password'  => bcrypt('ydourossimi@gmail.com'),
    'role'      => 'superadmin',
]);
echo 'Supramanager créé';
"

php artisan tinker --execute="
App\Models\User::create([
    'name'      => 'Super Admin',
    'email'     => 'admin@kazitrust.com',
    'password'  => 'superadmin123',  // le cast hashed s'en occupe
    'role'      => 'superadmin',
    'tenant_id' => null,             // pas de tenant pour le superadmin
]);
echo 'Superadmin créé';
"




# Générer la doc Scribe
php artisan scribe:generate

# La doc est accessible sur :
# http://127.0.0.1:8000/docs


# Migrations + cache
php artisan optimize:clear
php artisan serve

# ── Test 1 : Statut public ──────────────────────────────────────────
curl http://127.0.0.1:8000/api/v1/status

# ── Test 2 : Analyse numéro SÛR (terminaison 0-3 → approve) ────────
curl -X POST http://127.0.0.1:8000/api/v1/trust/analyze \
  -H "Authorization: Bearer kz_j1J39BL1NABAKEdKd9Pn7ppS7a6KMsgk" \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "+22961000000"}'

# ── Test 3 : Analyse numéro SUSPECT (terminaison 4-6 → manual_review)
curl -X POST http://127.0.0.1:8000/api/v1/trust/analyze \
  -H "Authorization: Bearer kz_j1J39BL1NABAKEdKd9Pn7ppS7a6KMsgk" \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "+22961000005", "context": {"transaction_amount": 150000, "transaction_currency": "XOF"}}'

# ── Test 4 : Analyse numéro FRAUDULEUX (terminaison 7-9 → reject) ───
curl -X POST http://127.0.0.1:8000/api/v1/trust/analyze \
  -H "Authorization: Bearer kz_j1J39BL1NABAKEdKd9Pn7ppS7a6KMsgk" \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "+22961000009"}'

# ── Test 5 : Quota ──────────────────────────────────────────────────
curl http://127.0.0.1:8000/api/v1/trust/quota \
  -H "Authorization: Bearer kz_j1J39BL1NABAKEdKd9Pn7ppS7a6KMsgk"

# ── Test 6 : Sans clé (doit retourner 401) ──────────────────────────
curl -X POST http://127.0.0.1:8000/api/v1/trust/analyze \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "+22961000000"}'

# ── Documentation ───────────────────────────────────────────────────
open http://127.0.0.1:8000/docs





curl -X POST http://127.0.0.1:8000/api/v1/trust/analyze \
  -H "Authorization: Bearer kz_j1J39BL1NABAKEdKd9Pn7ppS7a6KMsgk" \
  -H "Content-Type: application/json" \
  -d '{"phone_number": "+22961000000"}'



================

# Devices sandbox Nokia (à utiliser dans vos tests)

# SIM Swap : NON détecté (numéro sûr)
network_access_id: "simswap-not-swapped-no-sim-swapped@testcsp.net"
phone_number:      "+99999901000"

# SIM Swap : DÉTECTÉ (fraude)  
network_access_id: "simswap-swapped-2-hours@testcsp.net"
phone_number:      "+99999901001"

# SIM Swap : détecté il y a 7 jours
network_access_id: "simswap-swapped-7-days@testcsp.net"
phone_number:      "+99999901002"

# Number Verification : succès
network_access_id: "numver-verified@testcsp.net"
phone_number:      "+99999902000"

# Device Location : vérifié
network_access_id: "location-retrieval@testcsp.net"
phone_number:      "+99999903000"
# Devices sandbox Nokia (à utiliser dans vos tests)

# SIM Swap : NON détecté (numéro sûr)
network_access_id: "simswap-not-swapped-no-sim-swapped@testcsp.net"
phone_number:      "+99999901000"

# SIM Swap : DÉTECTÉ (fraude)  
network_access_id: "simswap-swapped-2-hours@testcsp.net"
phone_number:      "+99999901001"

# SIM Swap : détecté il y a 7 jours
network_access_id: "simswap-swapped-7-days@testcsp.net"
phone_number:      "+99999901002"

# Number Verification : succès
network_access_id: "numver-verified@testcsp.net"
phone_number:      "+99999902000"

# Device Location : vérifié
network_access_id: "location-retrieval@testcsp.net"
phone_number:      "+99999903000"




##### 
##### Documentation 
#####

php artisan scribe:generate
php artisan route:clear

config/
├── scribe.php        ← Français  → /docs
└── scribe_en.php     ← Anglais   → /docs/en

php artisan scribe:generate && php artisan scribe:generate --config scribe_en


#### 

# Génère la version française
php artisan scribe:generate

# Génère la version anglaise
php artisan scribe:generate --config scribe_en
php artisan scribe:generate --config scribe_en



Pour configurer votre intégration avec l'API **Nokia Network as Code (NaC)**, vous ne trouverez pas ces informations sur un seul tableau de bord classique, car Nokia passe par la plateforme **RapidAPI** pour la distribution de ses APIs de réseau.

Voici l'emplacement exact pour chaque variable sur votre compte :

---

### 1. Nokia Network as Code (Via RapidAPI)
L'immense majorité des développeurs utilisent le portail **RapidAPI** pour accéder aux fonctions réseau de Nokia.

*   **Où aller :** Connectez-vous sur [RapidAPI.com](https://rapidapi.com/nokia-nokia-standard/api/network-as-code).
*   **NOKIA_API_KEY :** Dans l'onglet **"Endpoints"**, regardez dans la colonne de droite (Exemples de code). Votre clé API se trouve dans le header `X-RapidAPI-Key`.
*   **NOKIA_TOKEN_URL :** C'est l'hôte spécifié sur RapidAPI, généralement `network-as-code.nokia.rapidapi.com`.
*   **NOKIA_API_URL :** Si vous utilisez le gateway Globalping comme indiqué dans votre exemple, l'URL est celle fournie dans votre snippet. Si vous passez en direct par RapidAPI, l'URL de base sera `[https://network-as-code.nokia.rapidapi.com/v1](https://network-as-code.nokia.rapidapi.com/v1)`.

---

### 2. Identifiants OAuth (Client ID & Secret)
Ces variables sont nécessaires si vous utilisez le flux d'authentification sécurisé de Nokia.

*   **Où aller :** [Nokia Network as Code Developer Portal](https://www.nokia.com/networks/network-as-code/developer-portal/).
*   **NOKIA_CLIENT_ID & NOKIA_CLIENT_SECRET :** 
    1.  Allez dans votre **Dashboard** Nokia.
    2.  Créez une **"Application"** ou un **"Project"**.
    3.  Une fois l'application créée, le portail générera un `Client ID` et un `Client Secret`. 
    *Note : Le Secret ne s'affiche qu'une seule fois à la création, assurez-vous de bien le copier.*

---

### 3. Configuration de votre fichier `.env`

Voici comment les remplir une fois les informations récupérées :

```env
# Mettre à true pour tester sans appeler l'API réelle (simule les réponses)
NOKIA_MOCK=false

# L'URL du point d'entrée (Gateway)
NOKIA_API_URL=https://gateway.api.globalping.io/network-as-code/v1

# Votre clé RapidAPI (X-RapidAPI-Key)
NOKIA_API_KEY=votre_cle_longue_alphanumerique

# L'hôte du service
NOKIA_TOKEN_URL=network-as-code.nokia.rapidapi.com

# Vos identifiants de projet Nokia Portal
NOKIA_CLIENT_ID=votre_client_id_nokia
NOKIA_CLIENT_SECRET=votre_client_secret_nokia
```

### Un conseil pour KaziTrust
Comme vous travaillez sur la **détection de fraude**, assurez-vous d'utiliser l'API **"SIM Swap"** ou **"Location Verification"** de Nokia. Ce sont ces APIs qui nécessitent ces variables pour confirmer si une carte SIM a été changée récemment, ce qui est un indicateur fort de fraude mobile.

Si vous avez un doute sur la validité d'une clé, testez-la d'abord via le bouton "Test Endpoint" directement sur RapidAPI avant de la mettre dans votre Laravel.