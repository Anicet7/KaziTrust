<?php

use Illuminate\Support\Facades\Route;




    /*
    Route::get('/', function () {
        return view('welcome');
    });
    */


    // routes/web.php — ajouter :
    Route::get('/', function () {
        $plans = \App\Models\Plan::active()->public()->orderBy('sort_order')->get();
        return view('welcome', compact('plans'));
    });


    // On définit manuellement les noms de routes que Blade recherche
    Route::get('/docs/en.postman', function() {
        return response()->file(storage_path('app/scribe/collection.json'));
    })->name('scribe_en.postman');

    Route::get('/docs/en.openapi', function() {
        return response()->file(storage_path('app/scribe/openapi.yaml'));
    })->name('scribe_en.openapi');

    // Votre route principale pour la doc EN
    Route::get('/docs/en', function () {
        return view('scribe_en.index');
    })->name('scribe_en');


        // Votre route principale pour la doc EN
    Route::get('/docs', function () {
        return view('scribe.index');
    })->name('scribe');