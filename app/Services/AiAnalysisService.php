<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    private const REQUIRED_KEYS    = ['decision', 'score', 'reasoning'];
    private const VALID_DECISIONS  = ['approve', 'reject', 'manual_review'];

    public function analyze(
        string $phoneNumber,
        array  $nokiaPayload,
        array  $context,
        App    $app
    ): array {

        // ── Garde : clé API manquante ────────────────────────────────────────────
        if (empty($app->llm_api_key)) {
            Log::error('AiAnalysisService: llm_api_key absent ou vide pour app #' . $app->id);
            return $this->fallbackResponse('Clé API LLM non configurée sur cette application.');
        }

        $prompt   = $this->buildPrompt($phoneNumber, $nokiaPayload, $context);
        $settings = $app->ai_settings ?? [];

        try {
            $result = match ($app->llm_provider) {
                'openai' => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
                'gemini' => $this->callGemini($prompt, $app->llm_api_key, $settings),
                'claude' => $this->callClaude($prompt, $app->llm_api_key, $settings),
                default  => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
            };

            if (!$this->isValidResponse($result['response'] ?? [])) {
                Log::warning('AiAnalysisService: réponse IA invalide — détail de validation', [
                    'provider'       => $app->llm_provider,
                    'parsed_response'=> $result['response'] ?? null,
                    'raw_response'   => $result['raw']      ?? null,
                    'validation_why' => $this->whyInvalid($result['response'] ?? []),
                ]);
                return $this->fallbackResponse(
                    'Réponse IA non conforme : ' . $this->whyInvalid($result['response'] ?? []),
                    $result['token_count'] ?? 0
                );
            }

            return $result;

        } catch (\Throwable $e) {
            // ⚠ \Throwable attrape aussi TypeError, Error, etc. — pas seulement Exception
            Log::error('AiAnalysisService: erreur lors de l\'appel LLM', [
                'provider' => $app->llm_provider,
                'class'    => get_class($e),
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            return $this->fallbackResponse($e->getMessage());
        }
    }

    // ── Validation ─────────────────────────────────────────────────────────────

    private function isValidResponse(mixed $response): bool
    {
        if (!is_array($response) || empty($response)) {
            return false;
        }

        foreach (self::REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $response)) {
                return false;
            }
        }

        // Normalisation défensive : certains LLM retournent le score en string
        $score = is_numeric($response['score']) ? (float) $response['score'] : null;
        if ($score === null || $score < 0 || $score > 100) {
            return false;
        }

        // Normalisation défensive : trim sur la décision (espaces parasites)
        $decision = is_string($response['decision']) ? trim($response['decision']) : '';
        if (!in_array($decision, self::VALID_DECISIONS, true)) {
            return false;
        }

        return true;
    }

    /**
     * Retourne un message lisible expliquant pourquoi la validation a échoué.
     * Utilisé dans les logs pour diagnostiquer rapidement.
     */
    private function whyInvalid(mixed $response): string
    {
        if (!is_array($response) || empty($response)) {
            return 'Réponse vide ou non-tableau';
        }

        foreach (self::REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $response)) {
                return "Clé manquante : '{$key}'";
            }
        }

        $score = $response['score'];
        if (!is_numeric($score) || (float)$score < 0 || (float)$score > 100) {
            return "Score invalide : " . json_encode($score);
        }

        $decision = is_string($response['decision']) ? trim($response['decision']) : '';
        if (!in_array($decision, self::VALID_DECISIONS, true)) {
            return "Décision inconnue : '{$decision}' (attendu : approve|reject|manual_review)";
        }

        return 'Valide';
    }

    // ── Construction du prompt ──────────────────────────────────────────────────

    private function buildPrompt(string $phoneNumber, array $nokia, array $context): string
    {
        $nokiaJson   = json_encode($nokia, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $contextJson = !empty($context)
            ? json_encode($context, JSON_PRETTY_PRINT)
            : 'Aucun contexte fourni.';

        // NOTE : on écrit les trois valeurs sur des lignes séparées plutôt que "a|b|c"
        // pour éviter que certains LLM retournent littéralement la chaîne avec les pipes.
        return <<<PROMPT
Tu es un expert en détection de fraude mobile pour l'Afrique de l'Ouest.
Analyse les signaux réseau Nokia CAMARA suivants pour le numéro {$phoneNumber} et évalue le risque de fraude.

## Signaux réseau Nokia CAMARA
{$nokiaJson}

## Contexte de la transaction
{$contextJson}

## Règles d'analyse
- SIM swap récent (< 7 jours) = signal de fraude majeur → decision: reject
- Roaming + SIM swap combinés = quasi-certitude de fraude → decision: reject
- Réseau 2G avec transaction importante = risque élevé → decision: manual_review
- Numéro inactif = rejeter immédiatement → decision: reject
- Numéro porté récemment + roaming = risque moyen → decision: manual_review
- Pas de signal de fraude = numéro fiable → decision: approve

## Format de réponse OBLIGATOIRE
Tu DOIS retourner UNIQUEMENT un objet JSON valide, sans aucun texte avant ou après, sans balise markdown.
La valeur de "decision" DOIT être exactement l'une de ces trois chaînes : approve, reject ou manual_review.
La valeur de "score" DOIT être un entier entre 0 et 100 (0 = fraude certaine, 100 = confiance maximale).

{
  "decision": "approve",
  "score": 85,
  "reasoning": "Explication concise en français, 2-3 phrases.",
  "risk_factors": ["facteur1", "facteur2"],
  "recommendation": "Action recommandée pour le métier."
}
PROMPT;
    }

    // ── Nettoyage JSON ──────────────────────────────────────────────────────────

    private function parseJsonSafe(string $raw): array
    {
        if (empty(trim($raw))) {
            return [];
        }

        // Supprimer blocs markdown ```json ... ``` ou ``` ... ```
        $clean = preg_replace('/^```(?:json)?\s*/im', '', $raw);
        $clean = preg_replace('/\s*```\s*$/im', '', $clean);
        $clean = trim($clean);

        // Extraire le premier objet JSON complet {…}
        if (preg_match('/\{.*\}/s', $clean, $matches)) {
            $clean = $matches[0];
        }

        $parsed = json_decode($clean, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('AiAnalysisService: JSON invalide après nettoyage', [
                'raw'   => $raw,
                'clean' => $clean,
                'error' => json_last_error_msg(),
            ]);
            return [];
        }

        // Normalisation post-parse : trim sur decision, cast score en int
        if (is_array($parsed)) {
            if (isset($parsed['decision'])) {
                $parsed['decision'] = trim((string) $parsed['decision']);
            }
            if (isset($parsed['score'])) {
                $parsed['score'] = (int) round((float) $parsed['score']);
            }
        }

        return is_array($parsed) ? $parsed : [];
    }

    // ── Fallback ────────────────────────────────────────────────────────────────

    private function fallbackResponse(string $reason = '', int $tokenCount = 0): array
    {
        return [
            'response' => [
                'decision'       => 'manual_review',
                'score'          => 50,
                'reasoning'      => 'Analyse automatique temporairement indisponible. Une vérification manuelle est requise.',
                'risk_factors'   => ['ai_unavailable'],
                'recommendation' => 'Effectuer une vérification manuelle avant de valider la transaction.',
            ],
            'token_count' => $tokenCount,
            'raw'         => $reason,
            '_fallback'   => true, // ← flag utile pour détecter les fallbacks dans les logs/stats
        ];
    }

    // ── OpenAI ──────────────────────────────────────────────────────────────────

    private function callOpenAI(string $prompt, string $apiKey, array $settings): array
    {
        $model = $settings['model'] ?? 'gpt-4o-mini';
        $temp  = (float) ($settings['temperature'] ?? 0.1); // 0.1 pour plus de déterminisme

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model,
                'temperature' => $temp,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'Tu es un expert anti-fraude. Réponds UNIQUEMENT avec un objet JSON brut valide. Aucun markdown. Aucun texte hors du JSON.',
                    ],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException("OpenAI HTTP {$response->status()}: {$response->body()}");
        }

        $data       = $response->json();
        $rawContent = $data['choices'][0]['message']['content'] ?? '{}';
        $parsed     = $this->parseJsonSafe($rawContent);
        $tokenCount = $data['usage']['total_tokens'] ?? 0;

        return ['response' => $parsed, 'token_count' => $tokenCount, 'raw' => $rawContent];
    }

    // ── Gemini ──────────────────────────────────────────────────────────────────

    private function callGemini(string $prompt, string $apiKey, array $settings): array
    {
        $model = $settings['model'] ?? 'gemini-1.5-flash';

        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature'      => (float) ($settings['temperature'] ?? 0.1),
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException("Gemini HTTP {$response->status()}: {$response->body()}");
        }

        $raw    = $response->json('candidates.0.content.parts.0.text', '{}');
        $parsed = $this->parseJsonSafe($raw);
        $tokens = $response->json('usageMetadata.totalTokenCount', 0);

        return ['response' => $parsed, 'token_count' => $tokens, 'raw' => $raw];
    }

    // ── Claude (Anthropic) ──────────────────────────────────────────────────────

    private function callClaude(string $prompt, string $apiKey, array $settings): array
    {
        $model = $settings['model'] ?? 'claude-haiku-4-5-20251001';

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->timeout(30)
          ->post('https://api.anthropic.com/v1/messages', [
              'model'      => $model,
              'max_tokens' => 1024,
              'system'     => 'Tu es un expert anti-fraude. Réponds UNIQUEMENT avec un objet JSON brut valide. Aucun texte avant ou après. Aucun markdown.',
              'messages'   => [['role' => 'user', 'content' => $prompt]],
          ]);

        if ($response->failed()) {
            throw new \RuntimeException("Claude HTTP {$response->status()}: {$response->body()}");
        }

        $raw    = $response->json('content.0.text', '{}');
        $parsed = $this->parseJsonSafe($raw);
        $tokens = $response->json('usage.input_tokens', 0)
                + $response->json('usage.output_tokens', 0);

        return ['response' => $parsed, 'token_count' => $tokens, 'raw' => $raw];
    }

    // ── Estimation coût ─────────────────────────────────────────────────────────

    public function estimateCost(int $tokens, string $provider): float
    {
        $pricesPer1k = [
            'openai' => 0.000150,
            'gemini' => 0.000075,
            'claude' => 0.000080,
        ];

        $price = $pricesPer1k[$provider] ?? 0.000150;
        return round(($tokens / 1000) * $price, 6);
    }
}