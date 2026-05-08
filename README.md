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
    'password'  => bcrypt('yd@ur@ssimi@gmail.c@m'),
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



#### LANGUE 
#### php artisan vendor:publish --tag=filament-panels-translations
####



/opt/alt/php84/usr/bin/php composer install

### Netoyage 
### php artisan clear-compiled && php artisan optimize:clear && composer dump-autoload
/opt/alt/php84/usr/bin/php artisan clear-compiled && /opt/alt/php84/usr/bin/php artisan optimize:clear && composer dump-autoload


# Optimise le chargement des classes (Composer)
composer install --optimize-autoloader --no-dev

# Met en cache la configuration et les routes
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimise les icônes et composants Filament
php artisan icons:cache
php artisan filament:cache-components


# Exécute les migrations de production
php artisan migrate --force

APP_ENV=production
APP_DEBUG=false

/opt/alt/php84/usr/bin/php  add_feexpay_ref_to_transactions.php

/opt/alt/php84/usr/bin/php artisan livewire:discover
/opt/alt/php84/usr/bin/php artisan view:clear
/opt/alt/php84/usr/bin/php artisan cache:clear


### Relancer les composants 
/opt/alt/php84/usr/bin/php artisan optimize:clear
/opt/alt/php84/usr/bin/php artisan config:clear
/opt/alt/php84/usr/bin/php artisan cache:clear
/opt/alt/php84/usr/bin/php /usr/local/bin/composer dump-autoload


/opt/alt/php84/usr/bin/php  artisan optimize:clear
/opt/alt/php84/usr/bin/php  artisan route:clear
/opt/alt/php84/usr/bin/php  artisan config:clear
/opt/alt/php84/usr/bin/php  artisan cache:clear

php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
composer dump-autoload



##
php artisan scribe:generate && php artisan scribe:generate --config scribe_en
/opt/alt/php84/usr/bin/php artisan scribe:generate && /opt/alt/php84/usr/bin/php artisan scribe:generate --config scribe_en


# Fais ceci
mkdir -p vendor/scribe
cp -r public/vendor/scribe/* vendor/scribe/

# et pour l’anglais :
mkdir -p vendor/scribe_en
cp -r public/vendor/scribe_en/* vendor/scribe_en/



###

/opt/alt/php84/usr/bin/php  artisan optimize:clear
/opt/alt/php84/usr/bin/php  artisan route:clear
/opt/alt/php84/usr/bin/php  artisan config:clear
/opt/alt/php84/usr/bin/php  artisan cache:clear

php artisan optimize:clear
php artisan route:clear



###
###
###
###
###

###
###
###
###

## Commande Tinker — Import des plans avec vérification

```php
php artisan tinker
```

Puis collez ce bloc en une seule fois :

```php
$plans = [
    [
        'name'                  => 'Trial',
        'slug'                  => 'trial',
        'description'           => 'Découvrez KaziTrust gratuitement pendant 14 jours.',
        'price_monthly'         => 0.00,
        'price_yearly'          => 0.00,
        'currency'              => 'XOF',
        'max_apps'              => 1,
        'max_api_keys_per_app'  => 1,
        'max_requests_per_month'=> 100,
        'max_users'             => 1,
        'features'              => json_encode(['webhook' => false, 'multi_llm' => false, 'priority_support' => false]),
        'is_active'             => true,
        'is_public'             => false,
        'sort_order'            => 0,
    ],
    [
        'name'                  => 'Starter',
        'slug'                  => 'starter',
        'description'           => 'Pour les PME qui démarrent.',
        'price_monthly'         => 15000.00,
        'price_yearly'          => 150000.00,
        'currency'              => 'XOF',
        'max_apps'              => 3,
        'max_api_keys_per_app'  => 5,
        'max_requests_per_month'=> 2000,
        'max_users'             => 3,
        'features'              => json_encode(['webhook' => true, 'multi_llm' => false, 'priority_support' => false]),
        'is_active'             => true,
        'is_public'             => true,
        'sort_order'            => 1,
    ],
    [
        'name'                  => 'Pro',
        'slug'                  => 'pro',
        'description'           => 'Pour les entreprises en croissance.',
        'price_monthly'         => 45000.00,
        'price_yearly'          => 450000.00,
        'currency'              => 'XOF',
        'max_apps'              => 10,
        'max_api_keys_per_app'  => 20,
        'max_requests_per_month'=> 10000,
        'max_users'             => 10,
        'features'              => json_encode(['webhook' => true, 'multi_llm' => true, 'priority_support' => false]),
        'is_active'             => true,
        'is_public'             => true,
        'sort_order'            => 2,
    ],
    [
        'name'                  => 'Enterprise',
        'slug'                  => 'enterprise',
        'description'           => 'Volume illimité, support dédié.',
        'price_monthly'         => 120000.00,
        'price_yearly'          => 1200000.00,
        'currency'              => 'XOF',
        'max_apps'              => -1,
        'max_api_keys_per_app'  => -1,
        'max_requests_per_month'=> -1,
        'max_users'             => -1,
        'features'              => json_encode(['webhook' => true, 'multi_llm' => true, 'priority_support' => true]),
        'is_active'             => true,
        'is_public'             => true,
        'sort_order'            => 3,
    ],
];

foreach ($plans as $data) {
    $plan = \App\Models\Plan::where('slug', $data['slug'])->first();

    if ($plan) {
        echo "⏭️  Déjà existant — ignoré : {$data['name']} (slug: {$data['slug']})\n";
    } else {
        \App\Models\Plan::create($data);
        echo "✅ Créé : {$data['name']} (slug: {$data['slug']})\n";
    }
}

echo "\n✔ Import terminé. Total plans en base : " . \App\Models\Plan::count() . "\n";
```

---

### Output attendu (premier import)

```
✅ Créé : Trial (slug: trial)
✅ Créé : Starter (slug: starter)
✅ Créé : Pro (slug: pro)
✅ Créé : Enterprise (slug: enterprise)

✔ Import terminé. Total plans en base : 4
```

### Output si déjà importé (idempotent)

```
⏭️  Déjà existant — ignoré : Trial (slug: trial)
⏭️  Déjà existant — ignoré : Starter (slug: starter)
...

✔ Import terminé. Total plans en base : 4
```

---

> 💡 **Conseil** : convertissez ensuite ce bloc en `DatabaseSeeder` ou en une classe `PlanSeeder` dédiée — vous pourrez l'appeler avec `php artisan db:seed --class=PlanSeeder` à chaque déploiement plutôt que de repasser par Tinker.


INSERT IGNORE INTO `plans` 
    (`name`, `slug`, `description`, `price_monthly`, `price_yearly`, `currency`, `max_apps`, `max_api_keys_per_app`, `max_requests_per_month`, `max_users`, `features`, `is_active`, `is_public`, `sort_order`, `created_at`, `updated_at`)
VALUES
    ('Trial',      'trial',      'Découvrez KaziTrust gratuitement pendant 14 jours.', 0.00,      0.00,       'XOF', 1,  1,  100,   1,  '{\"webhook\":false,\"multi_llm\":false,\"priority_support\":false}', 1, 0, 0, NOW(), NOW()),
    ('Starter',    'starter',    'Pour les PME qui démarrent.',                        15000.00,  150000.00,  'XOF', 3,  5,  2000,  3,  '{\"webhook\":true,\"multi_llm\":false,\"priority_support\":false}',  1, 1, 1, NOW(), NOW()),
    ('Pro',        'pro',        'Pour les entreprises en croissance.',                45000.00,  450000.00,  'XOF', 10, 20, 10000, 10, '{\"webhook\":true,\"multi_llm\":true,\"priority_support\":false}',   1, 1, 2, NOW(), NOW()),
    ('Enterprise', 'enterprise', 'Volume illimité, support dédié.',                   120000.00, 1200000.00, 'XOF', -1, -1, -1,    -1, '{\"webhook\":true,\"multi_llm\":true,\"priority_support\":true}',    1, 1, 3, NOW(), NOW());




####
$$$$


curl -X POST \
  "https://network-as-code.p.rapidapi.com/sim-swap/v0/check" \
  -H "X-RapidAPI-Key: 1f3b5c8119msh170c93c747d53e0p16f020jsn3f7a15f72bfa" \
  -H "X-RapidAPI-Host: network-as-code.p.rapidapi.com" \
  -H "Content-Type: application/json" \
  -d '{"phoneNumber": "+22961000000", "maxAge": 240}'




  Last login: Thu May  7 14:46:47 on ttys000
mac@Anicet-MacBook-Air ~ % 
mac@Anicet-MacBook-Air ~ % 
mac@Anicet-MacBook-Air ~ % nslookup network-as-code.nokia.rapidapi.com
Server:		192.168.1.1
Address:	192.168.1.1#53

Non-authoritative answer:
*** Can't find network-as-code.nokia.rapidapi.com: No answer

mac@Anicet-MacBook-Air ~ % ping -c 3 network-as-code.nokia.rapidapi.com
ping: cannot resolve network-as-code.nokia.rapidapi.com: Unknown host
mac@Anicet-MacBook-Air ~ % 
mac@Anicet-MacBook-Air ~ % 
mac@Anicet-MacBook-Air ~ % curl -X POST \
  "https://network-as-code.nokia.rapidapi.com/sim-swap/v0/check" \
  -H "X-RapidAPI-Key: ***" \
  -H "X-RapidAPI-Host: network-as-code.nokia.rapidapi.com" \
  -H "Content-Type: application/json" \
  -d '{"phoneNumber": "+22961000000", "maxAge": 240}'
curl: (6) Could not resolve host: network-as-code.nokia.rapidapi.com
mac@Anicet-MacBook-Air ~ % 


# Vérifier l'endpoint sim-swap (certaines versions utilisent /retrieve-date d'autres /latest)
mac@Anicet-MacBook-Air ~ % curl -X POST \
  "https://network-as-code.nokia.rapidapi.com/sim-swap/v0/retrieve-date" \
  -H "X-RapidAPI-Key: ***" \
  -H "X-RapidAPI-Host: network-as-code.nokia.rapidapi.com" \
  -H "Content-Type: application/json" \
  -d '{"phoneNumber": "+22961000000"}'
curl: (6) Could not resolve host: network-as-code.nokia.rapidapi.com
mac@Anicet-MacBook-Air ~ %






###
###### SIM Swap — date du dernier changement
###

curl --request POST \
	--url https://network-as-code.p.rapidapi.com/passthrough/camara/v1/sim-swap/sim-swap/v0/check \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"phoneNumber":"+99999991000","maxAge":240}'

    reponse : {"swapped":true} 

curl --request POST \
	--url https://network-as-code.p.rapidapi.com/passthrough/camara/v1/sim-swap/sim-swap/v0/check \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"phoneNumber":"+99999991000","maxAge":240}'

    reponse : {"swapped":false}



####

curl --request POST \
	--url https://network-as-code.p.rapidapi.com/passthrough/camara/v1/sim-swap/sim-swap/v0/retrieve-date \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"phoneNumber":"+99999991000"}'

    reponse : {"latestSimChange":"2026-05-08T15:56:40.436719Z"}



#####
#####           // ③ Device Status / Connectivity
#####

curl --request POST \
	--url https://network-as-code.p.rapidapi.com/passthrough/camara/v1/device-swap/device-swap/v1/check \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"phoneNumber":"+99999991000","maxAge":120}'
     
     reponse : {"swapped":true}


     curl --request POST \
	--url https://network-as-code.p.rapidapi.com/passthrough/camara/v1/device-swap/device-swap/v1/retrieve-date \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"phoneNumber":"+99999991001"}'

    {"latestDeviceChange":"2026-04-27T15:11:23.573491Z"}

 ④ Roaming

curl --request POST \
	--url https://network-as-code.p.rapidapi.com/device-status/device-roaming-status/v1/retrieve \
	--header 'Content-Type: application/json' \
	--header 'x-correlator: b4333c46-49c0-4f62-80d7-f0ef930f1c46' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"device":{"phoneNumber":"+99999991000"}}'

    reponse : {"device":null,"lastStatusTime":"2026-05-08T16:13:10.320101Z","roaming":true,"countryCode":36,"countryName":["HU"]}




    curl --request POST \
	--url https://network-as-code.p.rapidapi.com/device-status/v0/connectivity \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"device":{"phoneNumber":"+99999991000"}}'

    reponse : {"connectivityStatus":"CONNECTED_SMS","reachabilityStatus":null,"lastStatusTime":null}%



curl --request POST \
	--url https://network-as-code.p.rapidapi.com/passthrough/camara/v1/number-verification/number-verification/v0/verify \
	--header 'Content-Type: application/json' \
	--header 'x-rapidapi-host: network-as-code.p.rapidapi.com' \
	--header 'x-rapidapi-key: ***********' \
	--data '{"phoneNumber":"+99999991000"}'
    reponse : {"detail":"Authorization header is missing"}






curl --request POST \
    --url https://kazitrust.digitalconceptcenter.com/api/v1/trust/analyze \
    --header "Authorization: Bearer kz_MyY5PzFZJhozKSUhSfhCDEAXee9rbnBI" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --header "X-KaziTrust-Version: v1" \
    --data "{
    \"phone_number\": \"+99999991000\",
    \"context\": {
        \"transaction_amount\": 150000,
        \"transaction_currency\": \"XOF\",
        \"ip_address\": \"197.234.10.1\",
        \"user_agent\": \"Mozilla\\/5.0...\"
    }
}


curl --request POST \
    "https://kazitrust.digitalconceptcenter.com/api/v1/trust/analyze" \
    --header "Authorization: Bearer kz_MyY5PzFZJhozKSUhSfhCDEAXee9rbnBI" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --header "X-KaziTrust-Version: v1" \
    --data "{
    \"phone_number\": \"+22961000000\",
    \"context\": {
        \"transaction_amount\": 150000,
        \"transaction_currency\": \"XOF\",
        \"ip_address\": \"197.234.10.1\",
        \"user_agent\": \"Mozilla\\/5.0...\"
    }
}"




##### TEST LOCAL 

php artisan config:clear && php artisan tinker

$app = \App\Models\App::first();
$svc = app(\App\Services\NokiaService::class);

// +99999991000 → sim swappé + roaming (cas fraude)
$result = $svc->analyze('+99999991000', $app);
dd($result);

// +99999991001 → profil différent
$result = $svc->analyze('+99999991001', $app);
dd($result);