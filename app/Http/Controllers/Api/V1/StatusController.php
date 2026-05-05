<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

/**
 * @group Statut
 *
 * Vérifier l'état opérationnel de l'API.
 */
class StatusController extends Controller
{
    /**
     * Statut de l'API
     *
     * Retourne l'état de l'API et la version courante.
     * Aucune authentification requise.
     *
     * @unauthenticated
     *
     * @response {
     *   "status": "operational",
     *   "version": "1.0.0",
     *   "timestamp": "2026-05-03T12:00:00Z"
     * }
     */
    public function index()
    {
        return response()->json([
            'status'    => 'operational',
            'version'   => '1.0.0',
            'product'   => 'KaziTrust API',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}