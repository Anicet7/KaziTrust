<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NokiaService
{
    private bool   $mockMode;
    private string $apiUrl;
    private string $rapidApiKey;
    private string $rapidApiHost;

    public function __construct()
    {
        $this->mockMode     = (bool) env('NOKIA_MOCK', true);
        $this->apiUrl       = rtrim(env('NOKIA_API_URL', 'https://network-as-code.p.rapidapi.com'), '/');
        $this->rapidApiKey  = env('NOKIA_RAPIDAPI_KEY', '');
        $this->rapidApiHost = env('NOKIA_RAPIDAPI_HOST', 'network-as-code.p.rapidapi.com');
    }

    public function analyze(string $phoneNumber, App $app): array
    {
        if ($this->mockMode) {
            return $this->generateMockPayload($phoneNumber);
        }

        return $this->fetchSandboxPayload($phoneNumber);
    }

    /* -----------------------------------------------------------------
     |  Headers RapidAPI — centralisés ici pour ne pas répéter
     | ----------------------------------------------------------------- */

    private function rapidApiHeaders(bool $withCorrelator = false): array
    {
        $headers = [
            'x-rapidapi-key'  => $this->rapidApiKey,
            'x-rapidapi-host' => $this->rapidApiHost,
            'Content-Type'    => 'application/json',
        ];

        // Certains endpoints Nokia CAMARA exigent x-correlator
        if ($withCorrelator) {
            $headers['x-correlator'] = \Illuminate\Support\Str::uuid()->toString();
        }

        return $headers;
    }

    /* -----------------------------------------------------------------
     |  Sandbox Nokia Network as Code via RapidAPI
     |  Endpoints validés le 2026-05-08 avec numéros de test +9999999100x
     | ----------------------------------------------------------------- */

    // Constantes pour le retry et le circuit breaker
    private const MAX_RETRIES        = 2;
    private const RETRY_BASE_DELAY   = 1500;   // ms  (1.5s puis 3s)
    private const CIRCUIT_BREAK_KEY  = 'nokia:cb:429';
    private const CIRCUIT_BREAK_MAX  = 5;      // 429 cumulés → circuit ouvert
    private const CIRCUIT_BREAK_TTL  = 30;     // secondes

    /**
     * Wrapper HTTP avec retry exponentiel sur les 429 (rate limit Nokia BASIC).
     *
     * Stratégie :
     *   - 429 → attendre RETRY_BASE_DELAY * 2^(tentative-1) ms, puis retry
     *   - Après MAX_RETRIES épuisés → exception 'RATE_LIMITED'
     *   - Circuit breaker : si ≥ CIRCUIT_BREAK_MAX 429 cumulés dans les
     *     CIRCUIT_BREAK_TTL secondes → on court-circuite sans appeler Nokia
     *   - Autres erreurs (5xx, timeout) → exception immédiate, pas de retry
     */
    private function callWithRetry(
        string $endpoint,
        array  $body,
        string $label,
        bool   $withCorrelator = false
    ): \Illuminate\Http\Client\Response {

        // Circuit breaker : trop de 429 récents → court-circuit
        $cbCount = \Illuminate\Support\Facades\Cache::get(self::CIRCUIT_BREAK_KEY, 0);
        if ($cbCount >= self::CIRCUIT_BREAK_MAX) {
            Log::warning("{$label} skipped — circuit breaker ouvert ({$cbCount} 429 récents)");
            throw new \RuntimeException('CIRCUIT_BREAKER_OPEN');
        }

        $attempt = 0;

        while (true) {
            if ($attempt > 0) {
                $delayMs = self::RETRY_BASE_DELAY * (2 ** ($attempt - 1));
                Log::info("{$label} retry #{$attempt} — attente {$delayMs}ms après 429");
                usleep($delayMs * 1000);
            }

            $response = Http::withHeaders($this->rapidApiHeaders($withCorrelator))
                ->timeout(10)
                ->post($this->apiUrl . $endpoint, $body);

            Log::debug($label, [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->status() === 429) {
                // Incrémente le compteur circuit breaker
                \Illuminate\Support\Facades\Cache::put(
                    self::CIRCUIT_BREAK_KEY,
                    $cbCount + $attempt + 1,
                    self::CIRCUIT_BREAK_TTL
                );

                if ($attempt >= self::MAX_RETRIES) {
                    Log::warning("{$label} abandonné — rate limit après " . (self::MAX_RETRIES + 1) . " tentatives");
                    throw new \RuntimeException("HTTP 429: {$response->body()}");
                }

                $attempt++;
                continue;
            }

            // Succès ou autre erreur → réinitialise le circuit breaker
            \Illuminate\Support\Facades\Cache::forget(self::CIRCUIT_BREAK_KEY);

            return $response; // L'appelant vérifie $r->failed() lui-même
        }
    }

    private function fetchSandboxPayload(string $phoneNumber): array
    {
        $results = [
            '_mock'   => false,
            '_source' => 'Nokia Network as Code Sandbox (RapidAPI)',
        ];

        // ① SIM Swap — date du dernier changement
        // Endpoint réel : /passthrough/camara/v1/sim-swap/sim-swap/v0/retrieve-date
        // Réponse       : {"latestSimChange":"2026-05-08T15:56:40.436719Z"}
        try {
            $r = Http::withHeaders($this->rapidApiHeaders())
                ->timeout(10)
                ->post("{$this->apiUrl}/passthrough/camara/v1/sim-swap/sim-swap/v0/retrieve-date", [
                    'phoneNumber' => $phoneNumber,
                ]);

            Log::debug('Nokia SIM Swap retrieve-date', [
                'status' => $r->status(),
                'body'   => $r->body(),
            ]);

            if ($r->failed()) {
                throw new \RuntimeException("HTTP {$r->status()}: {$r->body()}");
            }

            $body = $r->json();

            // On stocke UNIQUEMENT la date brute ici.
            // days_ago sera calculé à l'étape ② APRÈS avoir confirmé swapped=true.
            // Calculer days_ago ici (avant de connaître swapped) causait le bug
            // DATA_INCONSISTENCY : swapped=false + days_ago=0.007 → reject injuste.
            $results['sim_swap'] = [
                'raw_date'    => $body['latestSimChange'] ?? null,
                'api_version' => 'camara-sim-swap-v0',
            ];

        } catch (\Exception $e) {
            Log::warning('Nokia SIM Swap retrieve-date failed', ['error' => $e->getMessage()]);
            $results['sim_swap'] = ['error' => $e->getMessage(), 'swapped' => false];
        }

        // ② SIM Swap — vérification booléenne sur 240h
        // Endpoint réel : /passthrough/camara/v1/sim-swap/sim-swap/v0/check
        // Réponse       : {"swapped":true} ou {"swapped":false}
        try {
            $r = Http::withHeaders($this->rapidApiHeaders())
                ->timeout(10)
                ->post("{$this->apiUrl}/passthrough/camara/v1/sim-swap/sim-swap/v0/check", [
                    'phoneNumber' => $phoneNumber,
                    'maxAge'      => 240,
                ]);

            Log::debug('Nokia SIM Swap check', [
                'status' => $r->status(),
                'body'   => $r->body(),
            ]);

            if ($r->failed()) {
                throw new \RuntimeException("HTTP {$r->status()}: {$r->body()}");
            }

            // swapped = SOURCE DE VÉRITÉ (toujours depuis /check, jamais depuis /retrieve)
            $isSwapped = $r->json('swapped', false);
            $results['sim_swap']['swapped']            = $isSwapped;
            $results['sim_swap']['verified_swap_240h'] = $isSwapped;

            // FIX DATA_INCONSISTENCY :
            // days_ago n'est calculé QUE si swapped=true.
            // Si swapped=false, raw_date est aussi purgé → aucune règle ne peut
            // utiliser une date qui ne correspond pas à un vrai swap.
            if ($isSwapped && isset($results['sim_swap']['raw_date'])) {
                try {
                    $results['sim_swap']['days_ago'] = abs(
                        now()->diffInHours(\Carbon\Carbon::parse($results['sim_swap']['raw_date']))
                    ) / 24;
                } catch (\Exception) {
                    $results['sim_swap']['days_ago'] = null;
                }
            } else {
                unset($results['sim_swap']['raw_date']);
                $results['sim_swap']['days_ago'] = null;
            }

        } catch (\Exception $e) {
            Log::warning('Nokia SIM Swap check failed', ['error' => $e->getMessage()]);
            $results['sim_swap']['verified_swap_240h'] = null;
        }

        // ③ Device Status / Connectivity
        // Endpoint réel : /device-status/v0/connectivity
        // Réponse       : {"connectivityStatus":"CONNECTED_SMS","reachabilityStatus":null,...}
        try {
            $r = Http::withHeaders($this->rapidApiHeaders())
                ->timeout(10)
                ->post("{$this->apiUrl}/device-status/v0/connectivity", [
                    'device' => ['phoneNumber' => $phoneNumber],
                ]);

            Log::debug('Nokia Device Connectivity', [
                'status' => $r->status(),
                'body'   => $r->body(),
            ]);

            if ($r->failed()) {
                throw new \RuntimeException("HTTP {$r->status()}: {$r->body()}");
            }

            $results['network_status'] = [
                'status'             => $r->json('connectivityStatus', 'unknown'),
                'reachability_status'=> $r->json('reachabilityStatus'),
                'last_status_time'   => $r->json('lastStatusTime'),
                'api_version'        => 'camara-device-status-v0',
            ];

        } catch (\Exception $e) {
            Log::warning('Nokia Device Connectivity failed', ['error' => $e->getMessage()]);
            $results['network_status'] = ['status' => 'unknown', 'error' => $e->getMessage()];
        }

        // ④ Roaming
        // Endpoint réel : /device-status/device-roaming-status/v1/retrieve
        // Headers requis : x-correlator obligatoire
        // Réponse        : {"roaming":true,"countryCode":36,"countryName":["HU"],...}
        try {
            $r = Http::withHeaders($this->rapidApiHeaders(withCorrelator: true))
                ->timeout(10)
                ->post("{$this->apiUrl}/device-status/device-roaming-status/v1/retrieve", [
                    'device' => ['phoneNumber' => $phoneNumber],
                ]);

            Log::debug('Nokia Roaming', [
                'status' => $r->status(),
                'body'   => $r->body(),
            ]);

            if ($r->failed()) {
                throw new \RuntimeException("HTTP {$r->status()}: {$r->body()}");
            }

            $results['roaming'] = [
                'is_roaming'      => $r->json('roaming', false),
                'country_code'    => $r->json('countryCode'),          // int (ex: 36 = Hongrie)
                'country_name'    => $r->json('countryName', []),      // array (ex: ["HU"])
                'last_status_time'=> $r->json('lastStatusTime'),
                'api_version'     => 'camara-roaming-v1',
            ];

        } catch (\Exception $e) {
            Log::warning('Nokia Roaming failed', ['error' => $e->getMessage()]);
            $results['roaming'] = ['is_roaming' => false, 'error' => $e->getMessage()];
        }

        $results['metadata'] = [
            'requested_at' => now()->toIso8601String(),
            'phone_number' => $phoneNumber,
        ];

        return $results;
    }

    /* -----------------------------------------------------------------
     |  Mock (prototype sans clé Nokia)
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
                'api_version'          => 'camara-sim-swap-v0',
            ],
            'network_status' => [
                'status'      => $profile['network_status'],
                'operator'    => $this->getRandomOperator(),
                'technology'  => $profile['sim_swapped'] ? '2G' : '4G',
                'api_version' => 'camara-device-status-v0',
            ],
            'roaming' => [
                'is_roaming'   => $profile['is_roaming'],
                'country_code' => $profile['is_roaming'] ? 234 : 229, // 234=NG, 229=BJ
                'country_name' => $profile['is_roaming'] ? ['NG'] : ['BJ'],
                'api_version'  => 'camara-roaming-v1',
            ],
            'device_location' => [
                'verified'     => !$profile['sim_swapped'],
                'country_code' => $profile['is_roaming'] ? 234 : 229,
            ],
            'number_verification' => [
                'is_active'             => $profile['network_status'] === 'active',
                'days_since_activation' => rand(30, 900),
                'is_ported'             => rand(0, 10) > 8,
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
            $lastDigit <= 3 => ['sim_swapped' => false, 'is_roaming' => false, 'network_status' => 'CONNECTED_DATA'],
            $lastDigit <= 6 => ['sim_swapped' => false, 'is_roaming' => (bool) rand(0, 1), 'network_status' => 'CONNECTED_SMS'],
            default         => ['sim_swapped' => true,  'is_roaming' => true,  'network_status' => 'CONNECTED_SMS'],
        };
    }

    private function getRandomOperator(): string
    {
        return ['MTN Bénin', 'Moov Africa', 'MTN Nigeria', 'Orange'][rand(0, 3)];
    }
}