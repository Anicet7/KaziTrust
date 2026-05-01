<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trust_logs', function (Blueprint $table) {
            $table->id();
            
            // Relation avec l'application
            $table->foreignId('app_id')->constrained()->cascadeOnDelete();
            
            // Informations de la requête
            $table->string('phone_number')->index();
            
            // Données brutes de Nokia (Snapshot pour audit)
            $table->json('nokia_payload'); 
            
            // Analyse IA
            $table->string('ai_provider'); // ex: openai, gemini...
            $table->json('ai_response'); // Contenu structuré : {score, decision, reasoning}
            
            // Métriques de facturation et performance
            $table->integer('token_count')->default(0);
            $table->integer('latency_ms')->default(0); // Temps de réponse total en millisecondes
            $table->decimal('cost_estimate', 10, 6)->default(0); // Coût estimé (ex: 0.000123)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_logs');
    }
};