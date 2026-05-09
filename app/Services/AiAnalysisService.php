<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AiAnalysisService — Moteur d'analyse anti-fraude
 *
 * Architecture à 3 niveaux :
 *   1. Moteur de règles déterministe  → 0 token, <1ms
 *   2. Cache par numéro+signaux       → 0 token, <5ms
 *   3. Appel LLM (cas ambigus seuls)  → N tokens, ~2s
 *
 * ─────────────────────────────────────────────────────
 * OPTIMISATIONS v4
 * ─────────────────────────────────────────────────────
 * [FIX-A1] Mapping connectivityStatus → technology :
 *          Nokia renvoie "connectivityStatus" dans la réponse
 *          Device Connectivity, pas "technology". L'ancien code
 *          lisait $n['technology'] qui était toujours null, ce qui
 *          empêchait la règle all_clear de se déclencher et forçait
 *          un appel LLM inutile à chaque fois.
 *          Fix : extractSignals() lit maintenant les deux champs et
 *          normalise les valeurs Nokia (CONNECTED_DATA → 4G-equivalent).
 *
 * [FIX-A2] Commande artisan pour débloquer le cache quota :
 *          Changer la clé API ne vide pas le cache kazitrust:quota_pause.
 *          Appeler `php artisan kazitrust:clear-ai-cache {app_id}` pour
 *          débloquer manuellement sans toucher au reste du cache.
 *          Voir : App\Console\Commands\ClearAiCache (à créer séparément).
 *
 * [FIX-B1] Seuil micro-montant relevé de 10% → 20% du seuil haut :
 *          Avant : micro = ≤ 5 000 XOF → une transaction de 10 000 XOF
 *          passait en LLM malgré des signaux parfaitement propres.
 *          Après : micro = ≤ 10 000 XOF → approuvé directement par règle.
 *
 * [FIX-B2] Nouvelle règle all_clear_no_tech :
 *          Lorsque tous les signaux de risque sont verts (pas de swap,
 *          pas de roaming, numéro actif, pas d'erreurs Nokia) mais que
 *          la technologie réseau est inconnue ou non-communiquée par Nokia,
 *          on approuve quand même (montant < seuil haut). Score 88 au lieu
 *          de 95 pour refléter l'absence d'info technologie.
 * ─────────────────────────────────────────────────────
 * OPTIMISATIONS v3
 * ─────────────────────────────────────────────────────
 * [FIX-1] Rate limiter par numéro : max 3 appels LLM/heure/numéro
 * [FIX-2] Cache sur les erreurs quota (5 min)
 * [FIX-3] Circuit breaker par provider (15 min après 3x 429)
 * [FIX-4] Prompt réduit de ~70%
 * [FIX-5] Log du prompt désactivé en production
 * ─────────────────────────────────────────────────────
 * BUG CRITIQUE CORRIGÉ (v2)
 * ─────────────────────────────────────────────────────
 * swapped=false + days_ago présent → days_ago forcé à null
 * ─────────────────────────────────────────────────────
 */
class AiAnalysisService
{
    private const REQUIRED_KEYS   = ['decision', 'score', 'reasoning'];
    private const VALID_DECISIONS = ['approve', 'reject', 'manual_review'];
    private const CACHE_TTL       = 3600;  // 1 heure — réponses valides
    private const QUOTA_CACHE_TTL = 300;   // 5 min  — pause après quota dépassé
    private const BREAKER_TTL     = 900;   // 15 min — circuit breaker ouvert

    private const RATE_LIMIT_MAX    = 3;
    private const RATE_LIMIT_WINDOW = 3600;

    // [FIX-A1] Table de normalisation connectivityStatus Nokia → technology interne
    private const CONNECTIVITY_MAP = [
        'CONNECTED_DATA' => 'CONNECTED_DATA', // accepté comme 4G+ par les règles
        'CONNECTED_SMS'  => '2G',             // réseau dégradé
        'NOT_CONNECTED'  => null,             // pas de tech utilisable
    ];

    // ═══════════════════════════════════════════════════
    //  POINT D'ENTRÉE
    // ═══════════════════════════════════════════════════

    public function analyze(
        string $phoneNumber,
        array  $nokiaPayload,
        array  $context,
        App    $app
    ): array {

        if (empty($app->llm_api_key)) {
            Log::error('AiAnalysisService: clé API absente', ['app_id' => $app->id]);
            return $this->fallbackResponse('Clé API LLM non configurée.', 0, 'missing_api_key');
        }

        // ① Extraction + validation de cohérence des signaux Nokia
        $signals = $this->extractSignals($nokiaPayload, $phoneNumber);

        // ② Moteur de règles — résout ~65-70% des cas sans IA
        $rulesResult = $this->applyRules($signals, $context);
        if ($rulesResult !== null) {
            Log::info('AiAnalysisService: décision par règles', [
                'phone'    => $phoneNumber,
                'decision' => $rulesResult['response']['decision'],
                'rule'     => $rulesResult['_rule'],
            ]);
            return $rulesResult;
        }

        // ③ Cache — évite de re-analyser un numéro déjà traité
        $cacheKey = $this->cacheKey($phoneNumber, $app->id, $signals);
        $cached   = Cache::get($cacheKey);
        if ($cached !== null) {
            Log::info('AiAnalysisService: réponse depuis le cache', ['phone' => $phoneNumber]);
            return array_merge($cached, ['_cached' => true]);
        }

        // [FIX-2] Cache quota
        $quotaKey = "kazitrust:quota_pause:{$app->id}:{$app->llm_provider}";
        if (Cache::has($quotaKey)) {
            Log::warning('AiAnalysisService: provider en pause quota, fallback immédiat', [
                'provider' => $app->llm_provider,
            ]);
            return $this->fallbackResponse('Provider en pause quota. Réessayer dans quelques minutes.', 0, 'ai_quota_paused');
        }

        // [FIX-3] Circuit breaker
        $breakerKey = "kazitrust:circuit_breaker:{$app->id}:{$app->llm_provider}";
        if (Cache::has($breakerKey)) {
            Log::warning('AiAnalysisService: circuit breaker ouvert', [
                'provider' => $app->llm_provider,
            ]);
            return $this->fallbackResponse('Circuit breaker ouvert. Service LLM temporairement suspendu.', 0, 'circuit_breaker_open');
        }

        // [FIX-1] Rate limiter par numéro : max 3 appels LLM par heure
        $rateLimitKey = "kazitrust:rate_limit:{$app->id}:{$phoneNumber}";
        $callCount    = (int) Cache::get($rateLimitKey, 0);
        if ($callCount >= self::RATE_LIMIT_MAX) {
            Log::warning('AiAnalysisService: rate limit atteint pour ce numéro', [
                'phone' => $phoneNumber,
                'calls' => $callCount,
                'max'   => self::RATE_LIMIT_MAX,
            ]);
            return $this->fallbackResponse('Rate limit atteint pour ce numéro. Vérification manuelle.', 0, 'rate_limit_exceeded');
        }

        // ④ Cas ambigu → appel LLM
        $settings = $app->ai_settings ?? [];
        $prompt   = $this->buildPrompt($signals, $context);

        Cache::put($rateLimitKey, $callCount + 1, self::RATE_LIMIT_WINDOW);

        try {
            $result = match ($app->llm_provider) {
                'openai' => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
                'gemini' => $this->callGemini($prompt, $app->llm_api_key, $settings),
                'claude' => $this->callClaude($prompt, $app->llm_api_key, $settings),
                default  => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
            };

            if (!$this->isValidResponse($result['response'] ?? [])) {
                $why = $this->whyInvalid($result['response'] ?? []);
                Log::warning('AiAnalysisService: réponse LLM invalide', [
                    'provider' => $app->llm_provider,
                    'reason'   => $why,
                    'raw'      => $result['raw'] ?? null,
                ]);
                return $this->fallbackResponse("Réponse IA non conforme : {$why}", $result['token_count'] ?? 0);
            }

            Cache::put($cacheKey, $result, self::CACHE_TTL);
            return $result;

        } catch (AiQuotaExceededException $e) {
            Log::warning('AiAnalysisService: quota dépassé', [
                'provider' => $app->llm_provider,
                'message'  => $e->getMessage(),
            ]);

            Cache::put($quotaKey, true, self::QUOTA_CACHE_TTL);

            $breakerCount = (int) Cache::get("{$breakerKey}:count", 0) + 1;
            Cache::put("{$breakerKey}:count", $breakerCount, self::BREAKER_TTL);
            if ($breakerCount >= 3) {
                Cache::put($breakerKey, true, self::BREAKER_TTL);
                Log::error('AiAnalysisService: circuit breaker déclenché', [
                    'provider'      => $app->llm_provider,
                    'errors'        => $breakerCount,
                    'pause_minutes' => self::BREAKER_TTL / 60,
                ]);
            }

            return $this->fallbackResponse($e->getMessage(), 0, 'ai_quota_exceeded');

        } catch (\Throwable $e) {
            Log::error('AiAnalysisService: erreur inattendue', [
                'provider' => $app->llm_provider,
                'class'    => get_class($e),
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return $this->fallbackResponse($e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════
    //  ① EXTRACTION DES SIGNAUX NOKIA
    //
    //  [FIX-A1] Nokia Device Connectivity renvoie "connectivityStatus"
    //  et non "technology". On lit les deux champs et on normalise via
    //  CONNECTIVITY_MAP pour que les règles (all_clear etc.) reçoivent
    //  un $tech cohérent et non-null.
    //
    //  Mapping :
    //    connectivityStatus=CONNECTED_DATA → technology=CONNECTED_DATA
    //    connectivityStatus=CONNECTED_SMS  → technology=2G
    //    connectivityStatus=NOT_CONNECTED  → technology=null
    //    technology présent directement    → utilisé tel quel (ex: "4G")
    //
    //  Fix bug v2 :
    //  Si Nokia /check dit swapped=false, alors days_ago DOIT être null.
    // ═══════════════════════════════════════════════════

    private function extractSignals(array $payload, string $phone = ''): array
    {
        $signals = [];

        // ── SIM Swap ────────────────────────────────────
        if (isset($payload['sim_swap'])) {
            $s       = $payload['sim_swap'];
            $swapped = (bool)($s['swapped'] ?? false);
            $daysAgo = isset($s['days_ago']) ? round((float)$s['days_ago'], 4) : null;

            // GUARD : swapped=false + days_ago présent = données incohérentes du mapper
            if (!$swapped && $daysAgo !== null) {
                Log::alert('AiAnalysisService: DATA_INCONSISTENCY — swapped=false mais days_ago présent', [
                    'phone'    => $phone,
                    'days_ago' => $daysAgo,
                    'action'   => 'days_ago ignoré, vérifiez NokiaPayloadMapper::buildSimSwap()',
                ]);
                $daysAgo = null;
            }

            if ($swapped && $daysAgo === null) {
                Log::warning('AiAnalysisService: swapped=true sans days_ago', [
                    'phone'  => $phone,
                    'action' => 'règle swap_unknown_age appliquée',
                ]);
            }

            $signals['sim_swap'] = array_filter([
                'swapped'            => $swapped,
                'days_ago'           => $daysAgo,
                'verified_swap_240h' => $s['verified_swap_240h'] ?? null,
            ], fn($v) => $v !== null && $v !== false);

            $signals['sim_swap']['swapped'] = $swapped;
        }

        // ── Réseau ──────────────────────────────────────
        // [FIX-A1] : Nokia peut envoyer "technology" (ancien format) OU
        //            "connectivityStatus" (Device Connectivity API v2).
        //            On lit les deux et on normalise via CONNECTIVITY_MAP.
        if (isset($payload['network_status'])) {
            $n = $payload['network_status'];

            $rawTech = $n['technology']        ?? null;
            $rawConn = $n['connectivityStatus'] ?? null;

            $technology = null;
            if ($rawTech !== null) {
                // Format direct : "4G", "5G", "2G"
                $technology = strtoupper(trim((string)$rawTech));
            } elseif ($rawConn !== null) {
                // Format connectivityStatus Nokia → normalisation
                $technology = self::CONNECTIVITY_MAP[strtoupper(trim((string)$rawConn))] ?? null;
            }

            // Statut lisible : on préfère connectivityStatus s'il est là
            $status = $n['status'] ?? ($rawConn ? strtolower((string)$rawConn) : 'unknown');

            $signals['network'] = array_filter([
                'status'     => $status,
                'technology' => $technology,
                'operator'   => $n['operator'] ?? null,
            ], fn($v) => $v !== null);

            // Log de normalisation uniquement en debug
            if (config('app.debug') && $rawConn !== null && $rawTech === null) {
                Log::debug('AiAnalysisService: [FIX-A1] connectivityStatus normalisé', [
                    'phone'       => $phone,
                    'raw_conn'    => $rawConn,
                    'mapped_tech' => $technology,
                ]);
            }
        }

        // ── Roaming ─────────────────────────────────────
        if (isset($payload['roaming'])) {
            $r = $payload['roaming'];
            $signals['roaming'] = array_filter([
                'is_roaming'   => (bool)($r['is_roaming']   ?? false),
                'country_code' => $r['country_code'] ?? null,
                'country_name' => $r['country_name'] ?? null,
            ], fn($v) => $v !== null);

            $signals['roaming']['is_roaming'] = (bool)($r['is_roaming'] ?? false);
        }

        // ── Vérification numéro ─────────────────────────
        if (isset($payload['number_verification'])) {
            $v = $payload['number_verification'];
            $signals['number'] = array_filter([
                'is_active'   => $v['is_active']             ?? null,
                'days_active' => $v['days_since_activation'] ?? null,
                'is_ported'   => $v['is_ported']             ?? null,
            ], fn($v) => $v !== null);
        }

        // ── Erreurs Nokia ────────────────────────────────
        foreach (['sim_swap', 'network_status', 'roaming', 'number_verification'] as $key) {
            if (isset($payload[$key]['error'])) {
                $signals['nokia_errors'][$key] = $payload[$key]['error'] ?? 'unavailable';
            }
        }

        return $signals;
    }

    // ═══════════════════════════════════════════════════
    //  Règles spécifiques
    // ═══════════════════════════════════════════════════

    private function ruleRoamingOutsideEcowas(array $signals, array $context): ?array
    {
        $roaming   = $signals['roaming'] ?? [];
        $isRoaming = $roaming['is_roaming'] ?? false;
        $country   = $roaming['country_name'][0] ?? '';
        $amount    = (float)($context['transaction_amount'] ?? 0);

        $ecowas = ['BJ', 'BF', 'CV', 'CI', 'GM', 'GH', 'GN', 'GW', 'LR', 'ML', 'NE', 'NG', 'SN', 'SL', 'TG'];

        if ($isRoaming && !in_array($country, $ecowas) && $amount > 50000) {
            return [
                'decision'     => 'manual_review',
                'risk_factors' => ['roaming_outside_ecowas'],
                'reasoning'    => "Roaming hors zone CEDEAO ($country) pour un montant significatif.",
                '_rule'        => 'rule:roaming_outside_ecowas',
            ];
        }
        return null;
    }

    private function ruleNokiaMissingHighAmount(array $signals, array $context): ?array
    {
        $amount          = (float)($context['transaction_amount'] ?? 0);
        $hasSimSwapError = isset($signals['nokia_errors']['sim_swap']);

        if ($hasSimSwapError && $amount >= 100_000) {
            return [
                'decision'     => 'manual_review',
                'risk_factors' => ['nokia_data_unavailable'],
                'reasoning'    => 'Signaux réseau indisponibles (erreur API Nokia) pour une transaction à haut risque.',
                '_rule'        => 'rule:nokia_missing_high_amount',
            ];
        }
        return null;
    }

    private function formatRuleResponse(array $ruleData): array
    {
        return [
            'response' => [
                'decision'       => $ruleData['decision'],
                'score'          => 30,
                'reasoning'      => $ruleData['reasoning'],
                'risk_factors'   => $ruleData['risk_factors'],
                'recommendation' => 'Vérification manuelle requise.',
            ],
            'token_count' => 0,
            'raw'         => null,
            '_fallback'   => false,
            '_rule'       => $ruleData['_rule'],
        ];
    }

    // ═══════════════════════════════════════════════════
    //  ② MOTEUR DE RÈGLES DÉTERMINISTE
    // ═══════════════════════════════════════════════════

    private function applyRules(array $signals, array $context): ?array
    {
        $nokiaCheck = $this->ruleNokiaMissingHighAmount($signals, $context);
        if ($nokiaCheck) return $this->formatRuleResponse($nokiaCheck);

        $roamingCheck = $this->ruleRoamingOutsideEcowas($signals, $context);
        if ($roamingCheck) return $this->formatRuleResponse($roamingCheck);

        $swap    = $signals['sim_swap']     ?? [];
        $roaming = $signals['roaming']      ?? [];
        $network = $signals['network']      ?? [];
        $number  = $signals['number']       ?? [];
        $errors  = $signals['nokia_errors'] ?? [];

        $swapped    = (bool)($swap['swapped']   ?? false);
        $daysAgo    = isset($swap['days_ago']) ? (float)$swap['days_ago'] : null;
        $isRoaming  = (bool)($roaming['is_roaming'] ?? false);
        $isActive   = $number['is_active']  ?? null;
        $daysActive = isset($number['days_active']) ? (int)$number['days_active'] : null;
        $isPorted   = (bool)($number['is_ported']  ?? false);
        $tech       = $network['technology'] ?? null;
        $amount     = (float)($context['transaction_amount'] ?? 0);
        $currency   = strtoupper($context['currency'] ?? 'XOF');

        $highAmountThreshold = match ($currency) {
            'EUR', 'USD' => 500,
            'XOF', 'XAF' => 50_000,
            'NGN'        => 250_000,
            default      => 50_000,
        };
        $isHighAmount = $amount > $highAmountThreshold;

        // [FIX-B1] Seuil micro-montant relevé de 10% → 20% du seuil haut.
        //
        //   Avant (0.10) : micro ≤  5 000 XOF  |  ≤  50 USD  |  ≤  25 000 NGN
        //   Après (0.20) : micro ≤ 10 000 XOF  |  ≤ 100 USD  |  ≤  50 000 NGN
        //
        //   Impact : une transaction de 10 000 XOF avec signaux propres
        //   est maintenant approuvée par rule:micro_amount_safe au lieu
        //   de tomber dans le LLM.
        $isMicroAmount = $amount > 0 && $amount <= ($highAmountThreshold * 0.20);

        $getCountryName = function ($roaming) {
            $name = $roaming['country_name'] ?? null;
            if (is_array($name)) return $name[0] ?? $roaming['country_code'] ?? 'inconnu';
            return $name ?? $roaming['country_code'] ?? 'inconnu';
        };

        // ── 1. REJECTS CRITIQUES ────────────────────────
        if ($isActive === false) {
            return $this->rulesResponse('reject', 2, 'Numéro désactivé. Authentification impossible.', ['inactive_number'], 'Bloquer.', 'rule:inactive_number');
        }

        if ($swapped && $daysAgo !== null && $daysAgo < 7 && $isRoaming) {
            $country = $getCountryName($roaming);
            return $this->rulesResponse('reject', 5, "SIM swap récent ({$daysAgo}j) + Roaming ({$country}). Schéma de fraude critique.", ['sim_swap_recent', 'roaming_active', 'fraud_combo_detected'], 'Rejeter.', 'rule:sim_swap_roaming');
        }

        if ($isPorted && $isRoaming) {
            $country = $getCountryName($roaming);
            return $this->rulesResponse('manual_review', 38, "Numéro porté en roaming actif ({$country}).", ['number_ported', 'roaming_active'], 'Vérifier.', 'rule:ported_roaming');
        }

        if ($swapped && $daysAgo !== null && $daysAgo < 1) {
            return $this->rulesResponse('reject', 2, "SIM swap il y a moins de 24h. Risque critique.", ['sim_swap_critical', 'swap_under_24h'], 'Bloquer.', 'rule:sim_swap_24h');
        }

        if ($swapped && $daysAgo === null) {
            return $this->rulesResponse('reject', 10, 'SIM swap confirmé, date inconnue.', ['sim_swap_confirmed', 'swap_age_unknown'], 'Rejeter par prudence.', 'rule:sim_swap_unknown_age');
        }

        if ($swapped && $daysAgo !== null && $daysAgo < 7) {
            return $this->rulesResponse('reject', 12, "SIM swap récent ({$daysAgo}j). Délai insuffisant.", ['sim_swap_recent'], 'Refuser.', 'rule:sim_swap_7d');
        }

        // ── 2. APPROVES RAPIDES ─────────────────────────

        // Conditions communes aux deux règles "all_clear"
        $allClearSignals = !$swapped && !$isRoaming && !$isPorted && empty($errors) && $isActive !== false;

        // Règle originale — tech réseau confirmée haute qualité
        if ($allClearSignals
            && ($daysActive === null || $daysActive > 30)
            && in_array($tech, ['4G', '5G', 'CONNECTED_DATA'], true)
        ) {
            return $this->rulesResponse(
                'approve', 95,
                'Profil de confiance validé. Numéro stable sans anomalie réseau.',
                [], 'Autoriser.',
                'rule:all_clear'
            );
        }

        // [FIX-B2] Nouvelle règle all_clear_no_tech
        //
        //  Contexte : Nokia Device Connectivity peut ne pas renvoyer de
        //  technologie (null après [FIX-A1]) pour des raisons légitimes.
        //  Si tous les autres signaux sont verts ET que le montant reste
        //  sous le seuil haut, on approuve avec un score légèrement réduit
        //  (88 au lieu de 95) plutôt que de consommer des tokens LLM
        //  pour une décision évidente.
        //
        //  Conditions : mêmes allClearSignals + numéro > 30j + montant non-élevé.
        if ($allClearSignals
            && ($daysActive === null || $daysActive > 30)
            && $tech === null
            && !$isHighAmount
        ) {
            return $this->rulesResponse(
                'approve', 88,
                'Signaux de risque absents. Technologie réseau non communiquée par Nokia, montant dans les limites.',
                [], 'Autoriser.',
                'rule:all_clear_no_tech'
            );
        }

        // Micro-montant sécurisé — [FIX-B1] seuil élargi à 20%
        if ($isMicroAmount && !$swapped && $isActive !== false) {
            return $this->rulesResponse(
                'approve', 85,
                "Montant faible ({$amount} {$currency}) et aucun SIM swap détecté.",
                [], 'Autoriser sans friction.',
                'rule:micro_amount_safe'
            );
        }

        // ── 3. MANUAL REVIEW / ZONES GRISES ────────────
        if ($daysActive !== null && $daysActive < 7 && $isHighAmount) {
            return $this->rulesResponse('manual_review', 35, "Nouveau numéro (activé il y a {$daysActive}j) avec transaction élevée.", ['new_number', 'high_amount'], "Vérifier l'ancienneté du client.", 'rule:new_number_high_amount');
        }

        if ($tech === '2G' && $isHighAmount) {
            return $this->rulesResponse('manual_review', 32, "Réseau 2G avec transaction élevée.", ['network_2g', 'high_amount'], "Vérifier l'identité.", 'rule:2g_high_amount');
        }

        if ($isPorted && $isRoaming) {
            return $this->rulesResponse('manual_review', 38, "Numéro porté en roaming actif.", ['number_ported', 'roaming_active'], 'Vérifier.', 'rule:ported_roaming');
        }

        // Cas ambigus → LLM
        return null;
    }

    // ═══════════════════════════════════════════════════
    //  ③ CLÉ DE CACHE
    // ═══════════════════════════════════════════════════

    private function cacheKey(string $phone, int|string|null $appId, array $signals): string
    {
        ksort($signals);
        $hash      = md5(json_encode($signals));
        $safeAppId = $appId ?? 'unknown';
        return "kazitrust:analysis:{$safeAppId}:{$phone}:{$hash}";
    }

    private function rulesResponse(
        string $decision,
        int    $score,
        string $reasoning,
        array  $riskFactors,
        string $recommendation,
        string $rule
    ): array {
        return [
            'response' => [
                'decision'       => $decision,
                'score'          => $score,
                'reasoning'      => $reasoning,
                'risk_factors'   => $riskFactors,
                'recommendation' => $recommendation,
            ],
            'token_count' => 0,
            'raw'         => null,
            '_fallback'   => false,
            '_rule'       => $rule,
        ];
    }

    // ═══════════════════════════════════════════════════
    //  ④ PROMPT LLM OPTIMISÉ
    // ═══════════════════════════════════════════════════

    private function buildPrompt(array $signals, array $context): string
    {
        $amount   = (float)($context['transaction_amount'] ?? 0);
        $currency = strtoupper($context['currency'] ?? $context['transaction_currency'] ?? 'XOF');

        $compactSignals = array_filter([
            'sim_swap' => $signals['sim_swap'] ?? null,
            'network'  => $signals['network']  ?? null,
            'roaming'  => $signals['roaming']  ?? null,
            'number'   => $signals['number']   ?? null,
        ], fn($v) => $v !== null);

        $signalsJson = json_encode($compactSignals, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (config('app.debug')) {
            Log::debug('AiAnalysisService: prompt LLM', [
                'tokens_estimate' => (int)(strlen($signalsJson) / 4),
                'amount'          => "{$amount} {$currency}",
            ]);
        }

        return <<<PROMPT
Fraude mobile Afrique Ouest. Signaux:{$signalsJson}|Montant:{$amount}{$currency}
Retourne JSON:{"decision":"approve|reject|manual_review","score":0-100,"reasoning":"<20 mots","risk_factors":["max3items"],"recommendation":"<10 mots"}
PROMPT;
    }

    // ═══════════════════════════════════════════════════
    //  VALIDATION
    // ═══════════════════════════════════════════════════

    private function isValidResponse(mixed $response): bool
    {
        if (!is_array($response) || empty($response)) return false;
        foreach (self::REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $response)) return false;
        }
        $score    = is_numeric($response['score']) ? (float)$response['score'] : null;
        $decision = is_string($response['decision']) ? trim($response['decision']) : '';
        return $score !== null
            && $score >= 0 && $score <= 100
            && in_array($decision, self::VALID_DECISIONS, true);
    }

    private function whyInvalid(mixed $response): string
    {
        if (!is_array($response) || empty($response)) return 'Réponse vide ou non-array';
        foreach (self::REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $response)) return "Clé manquante : '{$key}'";
        }
        $score = $response['score'];
        if (!is_numeric($score) || (float)$score < 0 || (float)$score > 100) {
            return 'Score invalide : ' . json_encode($score);
        }
        $decision = is_string($response['decision']) ? trim($response['decision']) : '';
        if (!in_array($decision, self::VALID_DECISIONS, true)) {
            return "Décision inconnue : '{$decision}' (attendu: approve|reject|manual_review)";
        }
        return 'Valide';
    }

    // ═══════════════════════════════════════════════════
    //  NETTOYAGE JSON LLM
    // ═══════════════════════════════════════════════════

    private function parseJsonSafe(string $raw): array
    {
        if (empty(trim($raw))) return [];

        $clean = preg_replace('/^```(?:json)?\s*/im', '', $raw);
        $clean = preg_replace('/\s*```\s*$/im', '',    $clean);
        $clean = trim($clean);

        if (preg_match('/\{.*\}/s', $clean, $matches)) {
            $clean = $matches[0];
        }

        $parsed = json_decode($clean, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('AiAnalysisService: JSON invalide', [
                'raw'   => substr($raw, 0, 500),
                'error' => json_last_error_msg(),
            ]);
            return [];
        }

        if (is_array($parsed)) {
            if (isset($parsed['decision'])) {
                $parsed['decision'] = strtolower(trim((string)$parsed['decision']));
            }
            if (isset($parsed['score'])) {
                $parsed['score'] = max(0, min(100, (int)round((float)$parsed['score'])));
            }
            if (!isset($parsed['risk_factors']) || !is_array($parsed['risk_factors'])) {
                $parsed['risk_factors'] = [];
            }
            if (!isset($parsed['recommendation'])) {
                $parsed['recommendation'] = '';
            }
        }

        return is_array($parsed) ? $parsed : [];
    }

    // ═══════════════════════════════════════════════════
    //  FALLBACK
    // ═══════════════════════════════════════════════════

    private function fallbackResponse(
        string $reason     = '',
        int    $tokenCount = 0,
        string $errorCode  = 'ai_unavailable'
    ): array {
        return [
            'response' => [
                'decision'       => 'manual_review',
                'score'          => 50,
                'reasoning'      => 'Analyse automatique indisponible. Vérification manuelle requise.',
                'risk_factors'   => [$errorCode],
                'recommendation' => 'Vérifier manuellement avant de valider.',
            ],
            'token_count' => $tokenCount,
            'raw'         => $reason,
            '_fallback'   => true,
            '_error_code' => $errorCode,
        ];
    }

    // ═══════════════════════════════════════════════════
    //  DÉTECTION QUOTA (HTTP 429)
    // ═══════════════════════════════════════════════════

    private function throwIfQuotaExceeded(int $status, string $body, string $provider): void
    {
        if ($status !== 429) return;
        $hint = match ($provider) {
            'openai' => 'Ajoutez un moyen de paiement sur platform.openai.com/billing',
            'gemini' => 'Vérifiez vos quotas sur aistudio.google.com',
            'claude' => 'Vérifiez votre plan sur console.anthropic.com/settings/billing',
            default  => 'Vérifiez la facturation de votre fournisseur IA',
        };
        throw new AiQuotaExceededException("Quota {$provider} dépassé. {$hint}");
    }

    // ═══════════════════════════════════════════════════
    //  UTILITAIRE STATIC : déblocage manuel du cache quota
    //
    //  [FIX-A2] Exposé en public static pour les commandes artisan.
    //
    //  Problème résolu : changer la clé API dans la config ne vide PAS
    //  automatiquement kazitrust:quota_pause:{app_id}:{provider}.
    //  Le circuit breaker et la pause quota restent actifs même avec
    //  une nouvelle clé, ce qui donne l'impression que "ça ne marche
    //  toujours pas" après rotation de clé.
    //
    //  Usage dans App\Console\Commands\ClearAiCache :
    //    AiAnalysisService::clearQuotaCache($appId, 'gemini');
    //
    //  CLI :
    //    php artisan kazitrust:clear-ai-cache {app_id} {--provider=gemini}
    // ═══════════════════════════════════════════════════

    public static function clearQuotaCache(int|string $appId, ?string $provider = null): array
    {
        $providers = $provider ? [$provider] : ['openai', 'gemini', 'claude'];
        $cleared   = [];

        foreach ($providers as $p) {
            $keys = [
                "kazitrust:quota_pause:{$appId}:{$p}",
                "kazitrust:circuit_breaker:{$appId}:{$p}",
                "kazitrust:circuit_breaker:{$appId}:{$p}:count",
            ];
            foreach ($keys as $key) {
                if (Cache::forget($key)) $cleared[] = $key;
            }
        }

        Log::info('AiAnalysisService: cache quota réinitialisé manuellement', [
            'app_id'   => $appId,
            'provider' => $provider ?? 'all',
            'cleared'  => $cleared,
        ]);

        return $cleared;
    }

    // ═══════════════════════════════════════════════════
    //  APPELS LLM
    // ═══════════════════════════════════════════════════

    private function callOpenAI(string $prompt, string $apiKey, array $settings): array
    {
        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'           => $settings['model'] ?? 'gpt-4o-mini',
                'temperature'     => (float)($settings['temperature'] ?? 0.1),
                'max_tokens'      => 200,
                'messages'        => [
                    ['role' => 'system', 'content' => 'Expert anti-fraude Afrique de l\'Ouest. JSON brut uniquement, aucun commentaire.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

        $this->throwIfQuotaExceeded($response->status(), $response->body(), 'openai');
        if ($response->failed()) {
            throw new \RuntimeException("OpenAI HTTP {$response->status()}: {$response->body()}");
        }

        $data = $response->json();
        $raw  = $data['choices'][0]['message']['content'] ?? '{}';
        return [
            'response'    => $this->parseJsonSafe($raw),
            'token_count' => $data['usage']['total_tokens'] ?? 0,
            'raw'         => $raw,
        ];
    }

    private function callGemini(string $prompt, string $apiKey, array $settings): array
    {
        if (config('app.debug')) {
            $maskedKey = substr($apiKey, 0, 8) . '...' . substr($apiKey, -4);
            Log::debug('AiAnalysisService: Appel Gemini', [
                'api_key' => $maskedKey,
                'model'   => $settings['model'] ?? 'gemini-2.0-flash',
            ]);
        }

        $model    = $settings['model'] ?? 'gemini-2.0-flash';
        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature'      => (float)($settings['temperature'] ?? 0.1),
                    'maxOutputTokens'  => 200,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        $this->throwIfQuotaExceeded($response->status(), $response->body(), 'gemini');
        if ($response->failed()) {
            throw new \RuntimeException("Gemini HTTP {$response->status()}: {$response->body()}");
        }

        $raw = $response->json('candidates.0.content.parts.0.text', '{}');
        return [
            'response'    => $this->parseJsonSafe($raw),
            'token_count' => $response->json('usageMetadata.totalTokenCount', 0),
            'raw'         => $raw,
        ];
    }

    private function callClaude(string $prompt, string $apiKey, array $settings): array
    {
        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model'      => $settings['model'] ?? 'claude-haiku-4-5-20251001',
            'max_tokens' => 200,
            'system'     => 'Expert anti-fraude Afrique de l\'Ouest. JSON brut uniquement, aucun commentaire.',
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);

        $this->throwIfQuotaExceeded($response->status(), $response->body(), 'claude');
        if ($response->failed()) {
            throw new \RuntimeException("Claude HTTP {$response->status()}: {$response->body()}");
        }

        $raw    = $response->json('content.0.text', '{}');
        $tokens = $response->json('usage.input_tokens', 0)
                + $response->json('usage.output_tokens', 0);
        return [
            'response'    => $this->parseJsonSafe($raw),
            'token_count' => $tokens,
            'raw'         => $raw,
        ];
    }

    // ═══════════════════════════════════════════════════
    //  UTILITAIRES
    // ═══════════════════════════════════════════════════

    public function estimateCost(int $tokens, string $provider): float
    {
        $prices = [
            'openai' => 0.000150,
            'gemini' => 0.000075,
            'claude' => 0.000080,
        ];
        return round(($tokens / 1000) * ($prices[$provider] ?? 0.000150), 6);
    }
}

class AiQuotaExceededException extends \RuntimeException {}