<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NokiaService
{
    private bool   $mockMode;
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->mockMode = (bool) env('NOKIA_MOCK', true);
        $this->apiUrl   = env('NOKIA_API_URL', 'https://gateway.api.globalping.io/network-as-code/v1');
        $this->apiKey   = env('NOKIA_API_KEY', '');
    }

    public function analyze(string $phoneNumber, App $app): array
    {
        if ($this->mockMode) {
            return $this->generateMockPayload($phoneNumber);
        }

        return $this->fetchSandboxPayload($phoneNumber);
    }

    /* -----------------------------------------------------------------
     |  Sandbox Nokia Network as Code (vrai appel API)
     | ----------------------------------------------------------------- */

    private function fetchSandboxPayload(string $phoneNumber): array
    {
        $results = [
            '_mock'   => false,
            '_source' => 'Nokia Network as Code Sandbox',
        ];

        // ① SIM Swap
        try {
            $r = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post("{$this->apiUrl}/sim-swap/v0/retrieve-date", [
                    'phoneNumber' => $phoneNumber,
                ]);

            $body = $r->json();
            $results['sim_swap'] = [
                'swapped'     => isset($body['latestSimChange']),
                'days_ago'    => isset($body['latestSimChange'])
                    ? now()->diffInDays(\Carbon\Carbon::parse($body['latestSimChange']))
                    : null,
                'raw_date'    => $body['latestSimChange'] ?? null,
                'api_version' => 'camara-sim-swap-0.4',
            ];
        } catch (\Exception $e) {
            Log::warning('Nokia SIM Swap failed', ['error' => $e->getMessage()]);
            $results['sim_swap'] = ['error' => $e->getMessage(), 'swapped' => false];
        }

        // ② SIM Swap Verify (vérification booléenne sur 240h)
        try {
            $r = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post("{$this->apiUrl}/sim-swap/v0/check", [
                    'phoneNumber' => $phoneNumber,
                    'maxAge'      => 240, // heures
                ]);

            $results['sim_swap']['verified_swap_240h'] = $r->json('swapped', false);
        } catch (\Exception $e) {
            $results['sim_swap']['verified_swap_240h'] = null;
        }

        // ③ Device Status
        try {
            $r = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post("{$this->apiUrl}/device-status/v0/connectivity", [
                    'device' => ['phoneNumber' => $phoneNumber],
                ]);

            $results['network_status'] = [
                'status'      => $r->json('connectivityStatus', 'unknown'),
                'api_version' => 'camara-device-status-0.6',
            ];
        } catch (\Exception $e) {
            $results['network_status'] = ['status' => 'unknown', 'error' => $e->getMessage()];
        }

        // ④ Roaming
        try {
            $r = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post("{$this->apiUrl}/device-status/v0/roaming", [
                    'device' => ['phoneNumber' => $phoneNumber],
                ]);

            $results['roaming'] = [
                'is_roaming'   => $r->json('roaming', false),
                'country_code' => $r->json('countryCode'),
                'api_version'  => 'camara-roaming-0.2',
            ];
        } catch (\Exception $e) {
            $results['roaming'] = ['is_roaming' => false, 'error' => $e->getMessage()];
        }

        $results['metadata'] = [
            'requested_at' => now()->toIso8601String(),
            'phone_number' => $phoneNumber,
        ];

        return $results;
    }

    /* -----------------------------------------------------------------
     |  Mock (inchangé — pour prototype sans clé Nokia)
     | ----------------------------------------------------------------- */

    private function generateMockPayload(string $phoneNumber): array
    {
        $lastDigit = (int) substr($phoneNumber, -1);
        $profile   = $this->getRiskProfile($lastDigit);

        return [
            '_mock'   => true,
            '_source' => 'KaziTrust CAMARA Simulator',
            'sim_swap' => [
                'swapped'              => $profile['sim_swapped'],
                'days_ago'             => $profile['sim_swapped'] ? rand(1, 7) : null,
                'verified_swap_240h'   => $profile['sim_swapped'],
                'check_performed'      => true,
                'api_version'          => 'camara-sim-swap-0.4',
            ],
            'network_status' => [
                'status'      => $profile['network_status'],
                'operator'    => $this->getRandomOperator(),
                'technology'  => $profile['sim_swapped'] ? '2G' : '4G',
                'api_version' => 'camara-device-status-0.6',
            ],
            'roaming' => [
                'is_roaming'   => $profile['is_roaming'],
                'country_code' => $profile['is_roaming'] ? 'NG' : 'BJ',
                'api_version'  => 'camara-roaming-0.2',
            ],
            'device_location' => [
                'verified'     => !$profile['sim_swapped'],
                'country_code' => $profile['is_roaming'] ? 'NG' : 'BJ',
            ],
            'number_verification' => [
                'is_active'            => $profile['network_status'] === 'active',
                'days_since_activation'=> rand(30, 900),
                'is_ported'            => rand(0, 10) > 8,
            ],
            'metadata' => [
                'requested_at' => now()->toIso8601String(),
                'phone_number' => $phoneNumber,
                'latency_ms'   => rand(80, 350),
            ],
        ];
    }

    private function getRiskProfile(int $lastDigit): array
    {
        return match(true) {
            $lastDigit <= 3 => ['sim_swapped' => false, 'is_roaming' => false, 'network_status' => 'active'],
            $lastDigit <= 6 => ['sim_swapped' => false, 'is_roaming' => (bool) rand(0,1), 'network_status' => 'active'],
            default         => ['sim_swapped' => true,  'is_roaming' => true, 'network_status' => 'active'],
        };
    }

    private function getRandomOperator(): string
    {
        return ['MTN Bénin', 'Moov Africa', 'MTN Nigeria', 'Orange'][rand(0, 3)];
    }
}




/// Production version 
/// <?php

///namespace App\Services;

///use App\Models\App;
///use Illuminate\Support\Facades\Http;
///use Illuminate\Support\Facades\Log;

/*
class NokiaService
{
    /**
     * En prototype : toujours utiliser le mock.
     * En production : passer NOKIA_MOCK=false dans .env et configurer NOKIA_API_URL.
     */
    /*
    private bool $mockMode;

    public function __construct()
    {
        $this->mockMode = (bool) env('NOKIA_MOCK', true);
    }

    /**
     * Point d'entrée principal : collecte tous les signaux CAMARA pour un numéro.
     */
    /* 
    public function analyze(string $phoneNumber, App $app): array
    {
        if ($this->mockMode) {
            return $this->generateMockPayload($phoneNumber);
        }

        return $this->fetchRealPayload($phoneNumber);
    }

    /* -----------------------------------------------------------------
     |  Mock CAMARA — données réalistes pour le prototype
     | ----------------------------------------------------------------- */

      /* 
    private function generateMockPayload(string $phoneNumber): array
    {
        // Seed déterministe basé sur le numéro pour des résultats cohérents
        $seed = crc32($phoneNumber);
        srand($seed);

        // Profils de risque simulés selon la terminaison du numéro
        $lastDigit = (int) substr($phoneNumber, -1);
        $profile   = $this->getRiskProfile($lastDigit);

        return [
            '_mock'    => true,
            '_source'  => 'KaziTrust CAMARA Simulator v1.0',

            // ① SIM Swap Detection (CAMARA SimSwap API)
            'sim_swap' => [
                'swapped'         => $profile['sim_swapped'],
                'days_ago'        => $profile['sim_swapped'] ? rand(1, 7) : null,
                'check_performed' => true,
                'api_version'     => 'camara-sim-swap-0.4',
            ],

            // ② Network Status
            'network_status' => [
                'status'          => $profile['network_status'],
                'operator'        => $this->getRandomOperator(),
                'technology'      => $profile['sim_swapped'] ? '2G' : '4G',
                'signal_quality'  => $profile['sim_swapped'] ? 'poor' : 'good',
                'api_version'     => 'camara-device-status-0.6',
            ],

            // ③ Roaming Status
            'roaming' => [
                'is_roaming'          => $profile['is_roaming'],
                'roaming_country'     => $profile['is_roaming'] ? 'NG' : null,
                'roaming_country_name'=> $profile['is_roaming'] ? 'Nigeria' : null,
                'api_version'         => 'camara-roaming-0.2',
            ],

            // ④ Device Location (approximative)
            'device_location' => [
                'verified'      => !$profile['sim_swapped'],
                'country_code'  => $profile['is_roaming'] ? 'NG' : 'BJ',
                'country_name'  => $profile['is_roaming'] ? 'Nigeria' : 'Bénin',
                'api_version'   => 'camara-device-location-0.3',
            ],

            // ⑤ Number Verification
            'number_verification' => [
                'is_active'            => $profile['network_status'] === 'active',
                'days_since_activation'=> rand(30, 900),
                'is_ported'            => rand(0, 10) > 8, // 20% porté
                'api_version'          => 'camara-number-verification-0.3',
            ],

            // ⑥ Métadonnées de la requête mock
            'metadata' => [
                'requested_at'  => now()->toIso8601String(),
                'phone_number'  => $phoneNumber,
                'latency_ms'    => rand(80, 350),
            ],
        ];
    }

    /**
     * Profils de risque — 3 types pour tests complets
     *
     * Numéros se terminant par 0-3 → SÛRS (approve attendu)
     * Numéros se terminant par 4-6 → SUSPECTS (manual_review attendu)
     * Numéros se terminant par 7-9 → FRAUDULEUX (reject attendu)
     */

     /* 
    private function getRiskProfile(int $lastDigit): array
    {
        return match(true) {
            $lastDigit <= 3 => [
                'sim_swapped'    => false,
                'is_roaming'     => false,
                'network_status' => 'active',
                'risk_level'     => 'low',
            ],
            $lastDigit <= 6 => [
                'sim_swapped'    => false,
                'is_roaming'     => (bool) rand(0, 1),
                'network_status' => 'active',
                'risk_level'     => 'medium',
            ],
            default => [
                'sim_swapped'    => true,
                'is_roaming'     => true,
                'network_status' => rand(0, 1) ? 'active' : 'inactive',
                'risk_level'     => 'high',
            ],
        };
    }

    private function getRandomOperator(): string
    {
        return ['MTN Bénin', 'Moov Africa', 'MTN Nigeria', 'Orange'][rand(0, 3)];
    }

    /* -----------------------------------------------------------------
     |  Production — appel réel à l'API Nokia CAMARA
     | ----------------------------------------------------------------- */

      /* 
    private function fetchRealPayload(string $phoneNumber): array
    {
        $baseUrl = env('NOKIA_API_URL', 'https://api.nokia-camara.com');
        $token   = $this->getAccessToken();

        $results = [];

        // Appels en parallèle (idéalement avec Http::pool en production)
        $endpoints = [
            'sim_swap'            => '/sim-swap/v0/retrieve-date',
            'network_status'      => '/device-status/v0/connectivity',
            'roaming'             => '/roaming/v0/info',
            'device_location'     => '/device-location-verification/v0/verify',
            'number_verification' => '/number-verification/v0/verify',
        ];

        foreach ($endpoints as $key => $endpoint) {
            try {
                $response = Http::withToken($token)
                    ->timeout(5)
                    ->post("{$baseUrl}{$endpoint}", [
                        'phoneNumber' => $phoneNumber,
                    ]);

                $results[$key] = $response->json();
            } catch (\Exception $e) {
                Log::warning("Nokia CAMARA {$key} failed", ['error' => $e->getMessage()]);
                $results[$key] = ['error' => $e->getMessage()];
            }
        }

        $results['metadata'] = ['requested_at' => now()->toIso8601String()];
        return $results;
    }

    private function getAccessToken(): string
    {
        // OAuth2 client credentials flow Nokia
        $response = Http::post(env('NOKIA_TOKEN_URL'), [
            'grant_type'    => 'client_credentials',
            'client_id'     => env('NOKIA_CLIENT_ID'),
            'client_secret' => env('NOKIA_CLIENT_SECRET'),
            'scope'         => 'sim-swap:read device-status:read',
        ]);

        return $response->json('access_token');
    }
}
*/

