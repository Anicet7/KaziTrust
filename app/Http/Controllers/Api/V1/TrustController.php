<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TrustLog;
use App\Services\AiAnalysisService;
use App\Services\NokiaService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
/**
 * @group Analyse de confiance
 *
 * Analysez la fiabilité d'un numéro de téléphone mobile via les signaux réseau
 * Nokia CAMARA et l'intelligence artificielle configurée sur votre application.
 */
class TrustController extends Controller
{
    public function __construct(
        private NokiaService      $nokia,
        private AiAnalysisService $ai,
        private WebhookService    $webhook,
    ) {}

    /**
     * Analyser un numéro
     *
     * Lance une analyse complète d'un numéro de téléphone :
     * collecte des signaux réseau Nokia CAMARA (SIM Swap, localisation, statut réseau),
     * puis analyse par le moteur IA configuré sur votre application.
     *
     * @bodyParam phone_number string required Le numéro au format E.164. Example: +22961000000
     * @bodyParam context object Contexte métier optionnel pour affiner l'analyse.
     * @bodyParam context.transaction_amount number Montant de la transaction en cours. Example: 150000
     * @bodyParam context.transaction_currency string Devise. Example: XOF
     * @bodyParam context.ip_address string IP de l'utilisateur final. Example: 197.234.10.1
     * @bodyParam context.user_agent string User-Agent du device. Example: Mozilla/5.0...
     *
     * @response 200 {
     *   "request_id": "uuid-v4",
     *   "phone_number": "+22961000000",
     *   "decision": "approve",
     *   "score": 87,
     *   "reasoning": "Aucun swap SIM détecté. Numéro actif depuis 18 mois...",
     *   "nokia_signals": {
     *     "sim_swap_detected": false,
     *     "sim_change_days_ago": null,
     *     "is_roaming": false,
     *     "network_status": "active",
     *     "location_country": "BJ"
     *   },
     *   "latency_ms": 1243,
     *   "token_count": 387,
     *   "cost_estimate": 0.000193,
     *   "analyzed_at": "2026-05-03T12:00:00Z"
     * }
     *
     * @response 422 {
     *   "error": "validation_failed",
     *   "message": "Le numéro doit être au format E.164.",
     *   "errors": { "phone_number": ["Format invalide."] }
     * }
     *
     * @response 429 {
     *   "error": "quota_exceeded",
     *   "message": "Quota mensuel atteint (500 requêtes).",
     *   "used": 500,
     *   "limit": 500
     * }
     */
    public function analyze(Request $request)
    {
        // ① Validation
        $validated = $request->validate([
            'phone_number'                   => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/'],
            'context'                        => ['sometimes', 'array'],
            'context.transaction_amount'     => ['sometimes', 'numeric', 'min:0'],
            'context.transaction_currency'   => ['sometimes', 'string', 'size:3'],
            'context.ip_address'             => ['sometimes', 'ip'],
            'context.user_agent'             => ['sometimes', 'string', 'max:500'],
        ], [
            'phone_number.regex' => 'Le numéro doit être au format E.164 (ex: +22961000000).',
        ]);

        $app    = $request->get('_kazi_app');
        $tenant = $request->get('_kazi_tenant');
        $startTime = microtime(true);
        $requestId = (string) Str::uuid();

        // ② Collecte Nokia CAMARA
        $nokiaPayload = $this->nokia->analyze(
            phoneNumber: $validated['phone_number'],
            app: $app,
        );

        // ③ Analyse IA
        $aiResult = $this->ai->analyze(
            phoneNumber: $validated['phone_number'],
            nokiaPayload: $nokiaPayload,
            context: $validated['context'] ?? [],
            app: $app,
        );

        // ④ Calcul métriques
        $latencyMs    = (int) round((microtime(true) - $startTime) * 1000);
        $tokenCount   = $aiResult['token_count'] ?? 0;
        $costEstimate = $this->ai->estimateCost($tokenCount, $app->llm_provider);

        // ⑤ Enregistrement du log
        $log = TrustLog::create([
            'app_id'        => $app->id,
            'phone_number'  => $validated['phone_number'],
            'nokia_payload' => $nokiaPayload,
            'ai_provider'   => $app->llm_provider,
            'ai_response'   => $aiResult['response'],
            'token_count'   => $tokenCount,
            'latency_ms'    => $latencyMs,
            'cost_estimate' => $costEstimate,
        ]);

        // ⑥ Webhook (fire & forget — ne bloque pas la réponse)
        if ($app->webhook_url) {
            $this->webhook->dispatch($app, $log, $requestId);
        }

        // ⑦ Réponse JSON
        return response()->json([
            'request_id'    => $requestId,
            'phone_number'  => $validated['phone_number'],
            'decision'      => $aiResult['response']['decision'],
            'score'         => $aiResult['response']['score'],
            'reasoning'     => $aiResult['response']['reasoning'],
            'nokia_signals' => [
                'sim_swap_detected'  => $nokiaPayload['sim_swap']['swapped'] ?? false,
                'sim_change_days_ago'=> $nokiaPayload['sim_swap']['days_ago'] ?? null,
                'is_roaming'         => $nokiaPayload['roaming']['is_roaming'] ?? false,
                'network_status'     => $nokiaPayload['network_status']['status'] ?? 'unknown',
                'location_country'   => $nokiaPayload['device_location']['country_code'] ?? null,
            ],
            'latency_ms'    => $latencyMs,
            'token_count'   => $tokenCount,
            'cost_estimate' => $costEstimate,
            'analyzed_at'   => now()->toIso8601String(),
        ]);
    }

    /**
     * Historique des analyses
     *
     * Retourne les dernières analyses effectuées par cette application (max 100).
     *
     * @queryParam per_page integer Résultats par page (max 100). Example: 20
     * @queryParam decision string Filtrer par décision (approve/reject/manual_review). Example: reject
     * @queryParam from string Date de début (Y-m-d). Example: 2026-05-01
     * @queryParam until string Date de fin (Y-m-d). Example: 2026-05-31
     *
     * @response 200 {
     *   "data": [...],
     *   "meta": { "total": 42, "per_page": 20, "current_page": 1 }
     * }
     */
    public function logs(Request $request)
    {
        $app = $request->get('_kazi_app');

       // $query = TrustLog::where('app_id', $app->id)
       //     ->orderByDesc('created_at');

        $query = TrustLog::query()
            ->where('app_id', '=', $app->id)
            ->orderByDesc('created_at');


        if ($request->decision) {
            $query->whereJsonContains('ai_response->decision', $request->decision);
        }
        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->until) {
            $query->whereDate('created_at', '<=', $request->until);
        }

        $perPage = min((int) ($request->per_page ?? 20), 100);
        $logs    = $query->paginate($perPage);

        return response()->json([
            'data' => $logs->map(fn($log) => [
                'id'          => $log->id,
                'phone_number'=> $log->phone_number,
                'decision'    => $log->ai_response['decision'] ?? null,
                'score'       => $log->ai_response['score'] ?? null,
                'latency_ms'  => $log->latency_ms,
                'cost_estimate'=> $log->cost_estimate,
                'analyzed_at' => $log->created_at->toIso8601String(),
            ]),
            'meta' => [
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
        ]);
    }

    /**
     * Détail d'une analyse
     *
     * @urlParam requestId string required UUID de la requête. Example: uuid-v4
     */
    public function show(Request $request, string $requestId)
    {
        // Note: on utilise l'id du log comme référence ici
        $app = $request->get('_kazi_app');


       // $log = TrustLog::where('app_id', $app->id)->findOrFail($requestId);
        $log = TrustLog::query()
                ->where('app_id', '=', $app->id)
                ->where('id', '=', $requestId)
                ->firstOrFail();

        return response()->json($log);
    }

    /**
     * Quota de l'application
     *
     * Retourne le quota mensuel et la consommation actuelle.
     *
     * @response 200 {
     *   "plan": "Starter",
     *   "limit": 2000,
     *   "used": 147,
     *   "remaining": 1853,
     *   "resets_at": "2026-06-01"
     * }
     */
    public function quota(Request $request)
    {
        $app    = $request->get('_kazi_app');
        $tenant = $request->get('_kazi_tenant');
        $plan   = $tenant->currentPlan();
        $limit  = $plan?->max_requests_per_month ?? 100;

        $used = TrustLog::whereHas('app', fn($q) =>
            $q->where('tenant_id', $tenant->id)
        )->whereMonth('created_at', now()->month)
         ->whereYear('created_at', now()->year)
         ->count();

        return response()->json([
            'plan'       => $plan?->name ?? 'Trial',
            'limit'      => $limit === -1 ? null : $limit,
            'unlimited'  => $limit === -1,
            'used'       => $used,
            'remaining'  => $limit === -1 ? null : max(0, $limit - $used),
            'resets_at'  => now()->startOfMonth()->addMonth()->toDateString(),
        ]);
    }
}