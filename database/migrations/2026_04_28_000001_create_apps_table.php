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
       Schema::create('apps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->uuid('uuid')->unique();
                $table->boolean('is_active')->default(true);
                
                // Webhook & Sécurité
                $table->string('webhook_url')->nullable();
                $table->string('webhook_secret')->nullable();

                // Configuration IA Flexible (Scalabilité LLM)
                $table->string('llm_provider')->default('openai'); // openai, gemini, claude...
                $table->text('llm_api_key')->nullable(); // Sera chiffré par Laravel
                $table->json('ai_settings')->nullable(); // Ex: {"model": "gpt-4o", "temp": 0.7}

                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
