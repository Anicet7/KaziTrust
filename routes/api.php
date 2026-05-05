<?php

use App\Http\Controllers\Api\V1\TrustController;
use App\Http\Controllers\Api\V1\StatusController;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



/*
|--------------------------------------------------------------------------
| KaziTrust API v1
|--------------------------------------------------------------------------
*/

// ✅ Route publique — santé de l'API (pas d'auth)
Route::get('/v1/status', [StatusController::class, 'index']);

// ✅ Routes protégées par clé API
Route::middleware('kazi.auth')->prefix('v1')->group(function () {

    // Analyse de confiance (cœur du produit)
    Route::post('/trust/analyze', [TrustController::class, 'analyze']);

    // Historique des analyses de cette app
    Route::get('/trust/logs', [TrustController::class, 'logs']);

    // Détail d'un log
    Route::get('/trust/logs/{requestId}', [TrustController::class, 'show']);

    // Quota restant
    Route::get('/trust/quota', [TrustController::class, 'quota']);
});