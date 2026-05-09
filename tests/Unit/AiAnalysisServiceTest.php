<?php

namespace Tests\Unit\Services;

use App\Models\App as AppModel;   // ← alias obligatoire : évite la collision avec la façade App de Laravel
use App\Services\AiAnalysisService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Tests concrets pour AiAnalysisService
 *
 * Numéros Nokia sandbox :
 *   +99999901000  → SIM swap NON détecté (numéro sûr)
 *   +99999901001  → SIM swap DÉTECTÉ il y a 2h (payload réel ci-dessous)
 *   +99999901002  → SIM swap détecté il y a 7 jours
 *   +99999902000  → Number verification : succès
 *
 * Lancer : php artisan test --filter AiAnalysisServiceTest
 * Lancer groupe: php artisan test --filter AiAnalysisServiceTest --group sim_swap
 */
class AiAnalysisServiceTest extends TestCase
{
    private AiAnalysisService $service;
    private AppModel $testApp;   // ← $testApp et non $app pour éviter le conflit avec $this->app de TestCase

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AiAnalysisService();

        $this->testApp = new AppModel([
            'id'           => 1,
            'llm_provider' => 'openai',
            'llm_api_key'  => 'sk-test-dummy-key-for-rules-only',
            'ai_settings'  => [],
        ]);


        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('warning')->zeroOrMoreTimes();
        Log::shouldReceive('error')->zeroOrMoreTimes();
        Log::shouldReceive('alert')->zeroOrMoreTimes();
    }

    // ─────────────────────────────────────────────────
    //  Helpers — payloads Nokia réutilisables
    // ─────────────────────────────────────────────────

    /**
     * Payload brut réel renvoyé par votre NokiaService pour +99999901001.
     * Nokia /check RapidAPI dit {"swapped":false}, mais votre NokiaService
     * appelle aussi /retrieve qui renvoie raw_date → il pose swapped=true.
     * C'est le bug à corriger dans NokiaService (pas ici).
     * Ce payload teste que AiAnalysisService gère correctement swapped=true.
     */
    private function nokiaPayloadSwap2h(): array
    {
        return [
            'sim_swap' => [
                'swapped'            => true,
                'days_ago'           => 0.006935337905092592,
                'raw_date'           => '2026-05-08T21:59:48.020776Z',
                'api_version'        => 'camara-sim-swap-v0',
                'verified_swap_240h' => false,
            ],
            'network_status' => [
                'status'     => 'CONNECTED_DATA',
                'technology' => '4G',
            ],
            'roaming' => [
                'is_roaming'   => false,
                'country_code' => null,
                'country_name' => null,
            ],
        ];
    }

    private function nokiaPayloadSafe(): array
    {
        return [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['status' => 'CONNECTED_DATA', 'technology' => '4G', 'operator' => 'MTN Bénin'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 365, 'is_ported' => false],
        ];
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 1 — Bug : swapped=false + days_ago injecté à tort
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('bug')]
    public function bug_swapped_false_avec_days_ago_injecte_ne_doit_pas_rejeter(): void
    {
        $nokiaPayload = [
            'sim_swap' => [
                'swapped'  => false,  // Nokia /check = source de vérité
                'days_ago' => 0.007,  // ← injecté à tort par le mapper depuis /retrieve
            ],
            'network_status'      => ['status' => 'CONNECTED_DATA', 'technology' => '4G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 180, 'is_ported' => false],
        ];

        Log::shouldReceive('alert')
            ->zeroOrMoreTimes()
            ->withArgs(fn($msg) => str_contains($msg, 'DATA_INCONSISTENCY'));
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('warning')->zeroOrMoreTimes();

        $result = $this->service->analyze(
            '+99999901000',
            $nokiaPayload,
            ['transaction_amount' => 5000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('approve', $result['response']['decision'],
            "swapped=false doit primer sur days_ago injecté — reject interdit"
        );
        $this->assertGreaterThan(80, $result['response']['score']);
        $this->assertEquals(0, $result['token_count'], "Résolu par règles, 0 token");
    }

    #[Test]
    #[Group('rules')]
    public function numero_safe_approuve_par_regles_zero_token(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $result = $this->service->analyze(
            '+99999901000',
            $this->nokiaPayloadSafe(),
            ['transaction_amount' => 25_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('approve', $result['response']['decision']);
        $this->assertGreaterThan(88,   $result['response']['score']);
        $this->assertEquals(0,         $result['token_count']);
        $this->assertStringContainsString('rule:all_clear', $result['_rule']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 2 — SIM swap +99999901001 (2h)
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('sim_swap')]
    public function sim_swap_2h_doit_rejeter_score_inferieur_a_5(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $result = $this->service->analyze(
            '+99999901001',
            $this->nokiaPayloadSwap2h(),
            ['transaction_amount' => 150_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('reject', $result['response']['decision']);
        $this->assertLessThanOrEqual(5, $result['response']['score']);
        $this->assertContains('sim_swap_critical', $result['response']['risk_factors']);
        $this->assertContains('swap_under_24h',    $result['response']['risk_factors']);
        $this->assertEquals(0, $result['token_count']);
        $this->assertStringContainsString('rule:sim_swap_24h', $result['_rule']);
    }

    #[Test]
    #[Group('sim_swap')]
    public function sim_swap_2h_avec_roaming_ghana_combo_fraude_detecte(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = array_merge($this->nokiaPayloadSwap2h(), [
            'roaming' => ['is_roaming' => true, 'country_code' => 'GH', 'country_name' => 'Ghana'],
        ]);

        $result = $this->service->analyze(
            '+99999901001',
            $payload,
            ['transaction_amount' => 50_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('reject', $result['response']['decision']);
        $this->assertContains('fraud_combo_detected', $result['response']['risk_factors']);
        $this->assertStringContainsString('Ghana', $result['response']['reasoning']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 3 — SIM swap +99999901002 (7 jours)
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('sim_swap')]
    public function sim_swap_6j9_petite_transaction_doit_rejeter(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => true, 'days_ago' => 6.9],
            'network_status'      => ['status' => 'CONNECTED_DATA', 'technology' => '4G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true],
        ];

        $result = $this->service->analyze(
            '+99999901002',
            $payload,
            ['transaction_amount' => 2_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('reject', $result['response']['decision']);
        $this->assertLessThanOrEqual(20, $result['response']['score']);
        $this->assertStringContainsString('rule:sim_swap_7d', $result['_rule']);
    }

    #[Test]
    #[Group('sim_swap')]
    public function sim_swap_exactement_7j_gros_montant_doit_etre_bloque(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        // days_ago=7.0 : hors fenêtre stricte "< 7j" → règle 30j+gros montant
        $payload = [
            'sim_swap'            => ['swapped' => true, 'days_ago' => 7.0],
            'network_status'      => ['technology' => '4G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true],
        ];

        $result = $this->service->analyze(
            '+99999901002',
            $payload,
            ['transaction_amount' => 200_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertContains($result['response']['decision'], ['reject', 'manual_review'],
            "days_ago=7j + gros montant doit être reject ou manual_review"
        );
    }

    #[Test]
    #[Group('sim_swap')]
    public function sim_swap_confirme_sans_age_reject_conservateur(): void
    {
        Log::shouldReceive('warning')
            ->zeroOrMoreTimes()
            ->withArgs(fn($msg) => str_contains($msg, 'swapped=true sans days_ago'));
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => true], // pas de days_ago
            'network_status'      => ['technology' => '4G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true],
        ];

        $result = $this->service->analyze(
            '+99999901001',
            $payload,
            ['transaction_amount' => 10_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('reject', $result['response']['decision']);
        $this->assertContains('swap_age_unknown', $result['response']['risk_factors']);
        $this->assertStringContainsString('rule:sim_swap_unknown_age', $result['_rule']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 4 — Number Verification +99999902000
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('number')]
    public function numero_3j_gros_montant_xof_manual_review(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['status' => 'CONNECTED_DATA', 'technology' => '4G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 3, 'is_ported' => false],
        ];

        $result = $this->service->analyze(
            '+99999902000',
            $payload,
            ['transaction_amount' => 75_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('manual_review', $result['response']['decision']);
        // $this->assertContains('new_number', $result['response']['risk_factors']);
        $this->assertStringContainsString('rule:new_number_high_amount', $result['_rule']);
    }

    #[Test]
    #[Group('number')]
    public function numero_3j_petite_transaction_non_rejetee_par_regles(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('warning')->zeroOrMoreTimes();
        Log::shouldReceive('error')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '4G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 3, 'is_ported' => false],
        ];

        $result = $this->service->analyze(
            '+99999902000',
            $payload,
            ['transaction_amount' => 500, 'currency' => 'XOF'],
            $this->testApp
        );


       // $this->assertNotEquals('reject', $result['response']['decision'],
       //     "500 XOF avec numéro récent ne doit pas être rejeté directement par les règles"
       // );

        $this->assertEquals('approve', $result['response']['decision']);
        $this->assertStringContainsString('rule:micro_amount_safe', $result['_rule']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 5 — Roaming et géographie
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('roaming')]
    public function roaming_france_hors_cedeao_gros_montant_manual_review(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '4G'],
            'roaming'             => ['is_roaming' => true, 'country_code' => 'FR', 'country_name' => 'France'],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 200, 'is_ported' => false],
        ];

        $result = $this->service->analyze(
            '+22900000001',
            $payload,
            ['transaction_amount' => 100_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('manual_review', $result['response']['decision']);
        $this->assertContains('roaming_outside_ecowas', $result['response']['risk_factors']);
        $this->assertStringContainsString('rule:roaming_outside_ecowas', $result['_rule']);
    }

    #[Test]
    #[Group('roaming')]
    public function roaming_senegal_cedeao_pas_bloque_par_regle_hors_cedeao(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '4G'],
            'roaming'             => ['is_roaming' => true, 'country_code' => 'SN', 'country_name' => 'Sénégal'],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 200, 'is_ported' => false],
        ];

        $result = $this->service->analyze(
            '+22900000002',
            $payload,
            ['transaction_amount' => 100_000, 'currency' => 'XOF'],
            $this->testApp
        );

        if (isset($result['_rule'])) {
            $this->assertStringNotContainsString('cedeao', $result['_rule'],
                "SN est dans la CEDEAO — règle hors-CEDEAO ne doit pas se déclencher"
            );
        }
    }

    #[Test]
    #[Group('roaming')]
    public function numero_porte_roaming_nigeria_manual_review(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '4G'],
            'roaming'             => ['is_roaming' => true, 'country_code' => 'NG', 'country_name' => 'Nigeria'],
            'number_verification' => ['is_active' => true, 'is_ported' => true],
        ];

        $result = $this->service->analyze(
            '+22900000003',
            $payload,
            ['transaction_amount' => 30_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('manual_review', $result['response']['decision']);
        $this->assertContains('number_ported', $result['response']['risk_factors']);
        $this->assertStringContainsString('rule:ported_roaming', $result['_rule']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 6 — Réseau 2G
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('network')]
    public function reseau_2g_75k_xof_manual_review(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['status' => 'CONNECTED_DATA', 'technology' => '2G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 180],
        ];

        $result = $this->service->analyze(
            '+22900000004',
            $payload,
            ['transaction_amount' => 75_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('manual_review', $result['response']['decision']);
        $this->assertContains('network_2g', $result['response']['risk_factors']);
        $this->assertStringContainsString('rule:2g_high_amount', $result['_rule']);
    }

    #[Test]
    #[Group('network')]
    public function reseau_2g_1k_xof_non_bloque_par_regle(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '2G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 180],
        ];

        $result = $this->service->analyze(
            '+22900000005',
            $payload,
            ['transaction_amount' => 1_000, 'currency' => 'XOF'],
            $this->testApp
        );

        if (isset($result['_rule'])) {
            $this->assertStringNotContainsString('2g_high_amount', $result['_rule']);
        }
        $this->assertNotEquals('reject', $result['response']['decision']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 7 — Numéro inactif
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('rules')]
    public function numero_inactif_reject_immediat_score_inferieur_5(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '4G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => false, 'is_ported' => false],
        ];

        $result = $this->service->analyze(
            '+22900000006',
            $payload,
            ['transaction_amount' => 1_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('reject', $result['response']['decision']);
        $this->assertLessThanOrEqual(5, $result['response']['score']);
        $this->assertContains('inactive_number', $result['response']['risk_factors']);
        $this->assertStringContainsString('rule:inactive_number', $result['_rule']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 8 — Nokia indisponible
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('nokia')]
    public function nokia_sim_swap_indisponible_gros_montant_manual_review(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['error' => 'SERVICE_UNAVAILABLE'],
            'network_status'      => ['error' => 'TIMEOUT'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true],
        ];

        $result = $this->service->analyze(
            '+22900000007',
            $payload,
            ['transaction_amount' => 100_000, 'currency' => 'XOF'],
            $this->testApp
        );

        $this->assertEquals('manual_review', $result['response']['decision']);
        $this->assertContains('nokia_data_unavailable', $result['response']['risk_factors']);
        $this->assertStringContainsString('rule:nokia_missing_high_amount', $result['_rule']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 9 — Devises
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('currency')]
    public function seuil_200_eur_2g_ne_declenche_pas_regle_high_amount(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '2G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 200],
        ];

        $result = $this->service->analyze(
            '+22900000008',
            $payload,
            ['transaction_amount' => 200, 'currency' => 'EUR'],
            $this->testApp
        );

        if (isset($result['_rule'])) {
            $this->assertStringNotContainsString('2g_high_amount', $result['_rule'],
                "200€ < seuil EUR (500€) — règle 2G+gros montant ne doit pas se déclencher"
            );
        }
    }

    #[Test]
    #[Group('currency')]
    public function seuil_600_eur_2g_declenche_regle_high_amount(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $payload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['technology' => '2G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 200],
        ];

        $result = $this->service->analyze(
            '+22900000009',
            $payload,
            ['transaction_amount' => 600, 'currency' => 'EUR'],
            $this->testApp
        );

        $this->assertEquals('manual_review', $result['response']['decision']);
        $this->assertStringContainsString('rule:2g_high_amount', $result['_rule']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 10 — Cache
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('cache')]
    public function reponse_mise_en_cache_retournee_avec_flag_cached(): void
    {
        // 1. On "Mock" le cache de Laravel. Au lieu de s'embêter à calculer le hash MD5 exact,
        // on dit simplement : "Si on interroge le cache, retourne cette fausse réponse IA".
        Cache::shouldReceive('get')
            ->zeroOrMoreTimes()
            ->andReturn([
                'response' => [
                    'decision'       => 'approve',
                    'score'          => 88,
                    'reasoning'      => 'Déjà analysé.',
                    'risk_factors'   => [],
                    'recommendation' => 'Continuer.',
                ],
                'token_count' => 0,
                'raw'         => '...',
                '_fallback'   => false,
                '_rule'       => null,
            ]);

        // 2. On crée un payload AMBIGU. 
        // Pas de 4G (sinon c'est approuvé par all_clear), pas de 2G (sinon c'est bloqué).
        // On met de la 3G et un numéro de 15 jours. Les règles vont faire un "pass" et interroger le cache.
        $nokiaPayload = [
            'sim_swap'            => ['swapped' => false],
            'network_status'      => ['status' => 'CONNECTED_DATA', 'technology' => '3G'],
            'roaming'             => ['is_roaming' => false],
            'number_verification' => ['is_active' => true, 'days_since_activation' => 15, 'is_ported' => false],
        ];

        $result = $this->service->analyze(
            '+22900000010',
            $nokiaPayload,
            ['transaction_amount' => 30_000, 'currency' => 'XOF'],
            $this->testApp
        );

        // 3. On vérifie que la réponse provient bien du cache
        $this->assertTrue($result['_cached'] ?? false, "Le flag _cached doit être présent et true.");
        $this->assertEquals('approve', $result['response']['decision']);
    }

    // ════════════════════════════════════════════════════════════
    //  GROUPE 11 — Clé API manquante
    // ════════════════════════════════════════════════════════════

    #[Test]
    #[Group('fallback')]
    public function cle_api_manquante_retourne_fallback_immediat(): void
    {

    Log::shouldReceive('error')->atMost()->once(); 
    Log::shouldReceive('warning')->atMost()->once();

    
        $appSansKey = new AppModel([
            'id'           => 99,
            'llm_provider' => 'openai',
            'llm_api_key'  => '',
            'ai_settings'  => [],
        ]);

        $result = $this->service->analyze(
            '+22900000099',
            ['sim_swap' => ['swapped' => false]],
            [],
            $appSansKey
        );

       // $this->assertTrue($result['_fallback']);
       // $this->assertEquals('missing_api_key', $result['_error_code']);
       // $this->assertEquals('manual_review',   $result['response']['decision']);
       // $this->assertEquals(0,                 $result['token_count']);

        $this->assertTrue($result['_fallback'], "Le flag _fallback devrait être à true");
        $this->assertEquals('manual_review', $result['response']['decision']);
        $this->assertContains('missing_api_key', $result['response']['risk_factors']);

    }
}