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
            $results['sim_swap'] = [
                'swapped'     => isset($body['latestSimChange']),
                /*
                'days_ago'    => isset($body['latestSimChange'])
                    ? now()->diffInDays(\Carbon\Carbon::parse($body['latestSimChange']))
                    : null,
                */

                'days_ago' => isset($body['latestSimChange'])
                    ? abs(now()->diffInHours(\Carbon\Carbon::parse($body['latestSimChange']))) / 24
                    : null,
                    
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

            $results['sim_swap']['verified_swap_240h'] = $r->json('swapped', false);

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