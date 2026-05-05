<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'                   => 'Trial',
                'slug'                   => 'trial',
                'description'            => 'Découvrez KaziTrust gratuitement pendant 14 jours.',
                'price_monthly'          => 0,
                'price_yearly'           => 0,
                'currency'               => 'XOF',
                'max_apps'               => 1,
                'max_api_keys_per_app'   => 1,
                'max_requests_per_month' => 100,
                'max_users'              => 1,
                'features'               => ['webhook' => false, 'multi_llm' => false, 'priority_support' => false],
                'is_public'              => false, // pas affiché sur la page pricing
                'sort_order'             => 0,
            ],
            [
                'name'                   => 'Starter',
                'slug'                   => 'starter',
                'description'            => 'Pour les PME qui démarrent.',
                'price_monthly'          => 15000,  // 15 000 XOF/mois (~25 USD)
                'price_yearly'           => 150000,
                'currency'               => 'XOF',
                'max_apps'               => 3,
                'max_api_keys_per_app'   => 5,
                'max_requests_per_month' => 2000,
                'max_users'              => 3,
                'features'               => ['webhook' => true, 'multi_llm' => false, 'priority_support' => false],
                'is_public'              => true,
                'sort_order'             => 1,
            ],
            [
                'name'                   => 'Pro',
                'slug'                   => 'pro',
                'description'            => 'Pour les entreprises en croissance.',
                'price_monthly'          => 45000, // 45 000 XOF/mois (~75 USD)
                'price_yearly'           => 450000,
                'currency'               => 'XOF',
                'max_apps'               => 10,
                'max_api_keys_per_app'   => 20,
                'max_requests_per_month' => 10000,
                'max_users'              => 10,
                'features'               => ['webhook' => true, 'multi_llm' => true, 'priority_support' => false],
                'is_public'              => true,
                'sort_order'             => 2,
            ],
            [
                'name'                   => 'Enterprise',
                'slug'                   => 'enterprise',
                'description'            => 'Volume illimité, support dédié.',
                'price_monthly'          => 120000, // Négociable
                'price_yearly'           => 1200000,
                'currency'               => 'XOF',
                'max_apps'               => -1,      // -1 = illimité
                'max_api_keys_per_app'   => -1,
                'max_requests_per_month' => -1,
                'max_users'              => -1,
                'features'               => ['webhook' => true, 'multi_llm' => true, 'priority_support' => true],
                'is_public'              => true,
                'sort_order'             => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}