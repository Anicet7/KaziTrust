<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();

            // Statut du cycle de vie
            $table->enum('status', [
                'trial',
                'active',
                'past_due',
                'cancelled',
                'expired',
                'paused',
            ])->default('trial');

            // Période en cours
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Paiement externe
            $table->string('payment_provider')->nullable();   // 'fedapay', 'stripe', 'manual'
            $table->string('payment_provider_id')->nullable()->index(); // ID externe

            // Prix snapshot au moment de la souscription (si le plan change de prix)
            $table->decimal('price_paid', 10, 2)->nullable();
            $table->string('currency')->default('XOF');
            $table->string('billing_cycle')->default('monthly'); // 'monthly', 'yearly'

            $table->text('notes')->nullable(); // Notes admin (ex: "offert 3 mois")

            $table->timestamps();

            // Un tenant ne peut avoir qu'une souscription active à la fois
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};