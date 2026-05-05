<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // On supprime les colonnes "bricolées" de la phase 1
            $table->dropColumn(['subscription_plan', 'trial_ends_at']);
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('subscription_plan')->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
        });
    }
};