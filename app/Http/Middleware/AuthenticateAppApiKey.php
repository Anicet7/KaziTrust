<?php

namespace App\Http\Middleware;

use App\Models\AppApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAppApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        // ① Extraire la clé du header Bearer
        $token = $request->bearerToken();

        if (!$token || !str_starts_with($token, 'kz_')) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'Clé API manquante ou invalide. Format attendu : Bearer kz_xxx',
            ], 401);
        }

        // ② Trouver la clé en base
        /*
        $apiKey = AppApiKey::where('key', $token)
            ->where('is_active', true)
            ->with(['app.tenant.activeSubscription.plan'])
            ->first();
            */


            $apiKey = \App\Models\AppApiKey::query()
                ->where('app_api_keys.key', '=', $token) // On précise "table.colonne" et on ajoute l'opérateur '='
                ->where('is_active', true)
                ->with(['app.tenant.activeSubscription.plan'])
                ->first();


        if (!$apiKey) {
            return response()->json([
                'error' => 'unauthorized',
                'message' => 'Clé API introuvable ou révoquée.',
            ], 401);
        }

        $app    = $apiKey->app;
        $tenant = $app->tenant;

        // ③ Vérifier que le tenant peut utiliser l'API
        if (!$tenant->is_active) {
            return response()->json([
                'error' => 'tenant_inactive',
                'message' => 'Votre compte est désactivé. Contactez le support.',
            ], 403);
        }

        if (!$tenant->canUseApi()) {
            return response()->json([
                'error' => 'subscription_expired',
                'message' => 'Votre abonnement est expiré. Renouvelez depuis le panneau de gestion.',
            ], 402);
        }

        // ④ Vérifier le quota mensuel
        $plan         = $tenant->currentPlan();
        $maxRequests  = $plan?->max_requests_per_month ?? 100;

        if ($maxRequests !== -1) {
            $usedThisMonth = \App\Models\TrustLog::whereHas('app', fn($q) =>
                $q->where('tenant_id', $tenant->id)
            )->whereMonth('created_at', now()->month)
             ->whereYear('created_at', now()->year)
             ->count();

            if ($usedThisMonth >= $maxRequests) {
                return response()->json([
                    'error'   => 'quota_exceeded',
                    'message' => "Quota mensuel atteint ({$maxRequests} requêtes). Passez à un plan supérieur.",
                    'used'    => $usedThisMonth,
                    'limit'   => $maxRequests,
                ], 429);
            }
        }

        // ⑤ Mettre à jour last_used_at (sans bloquer la requête)
        $apiKey->updateQuietly(['last_used_at' => now()]);

        // ⑥ Injecter dans la requête pour le controller
        $request->merge([
            '_kazi_app'    => $app,
            '_kazi_tenant' => $tenant,
            '_kazi_apikey' => $apiKey,
        ]);

        return $next($request);
    }
}