<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // "Starter", "Pro", "Enterprise"
            $table->string('slug')->unique();              // "starter", "pro", "enterprise"
            $table->text('description')->nullable();

            // Tarification
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->string('currency')->default('XOF');    // XOF pour Bénin, USD si Stripe

            // Limites d'usage
            $table->integer('max_apps')->default(1);
            $table->integer('max_api_keys_per_app')->default(2);
            $table->integer('max_requests_per_month')->default(500);
            $table->integer('max_users')->default(1);

            // Fonctionnalités (JSON flexible pour éviter des colonnes boolean infinies)
            $table->json('features')->nullable();
            // Ex: {"webhook": true, "multi_llm": false, "priority_support": false}

            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);   // false = plan sur-mesure
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};