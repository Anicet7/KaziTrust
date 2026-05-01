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
       // database/migrations/xxxx_xx_xx_create_trust_logs_table.php
        Schema::create('trust_logs', function (Blueprint $table) {
          ///  $table->id();
            $table->foreignId('app_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number')->index();
            
            // Données brutes de Nokia (Snapshot pour audit)
            $table->json('nokia_payload'); 
            
            // Analyse IA
            $table->string('ai_provider'); // openai, gemini...
            $table->json('ai_response'); // {score, decision, reasoning}
            
            // Métriques de facturation et performance
            $table->integer('token_count')->default(0);
            $table->integer('latency_ms')->default(0); // Temps de réponse total
            $table->decimal('cost_estimate', 10, 6)->default(0); // Coût estimé en USD
            
         //   $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trust_logs', function (Blueprint $table) {
            //
        });
    }
};
