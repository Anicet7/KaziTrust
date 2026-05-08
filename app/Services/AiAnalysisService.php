<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    /**
     * Clés obligatoires que la réponse IA doit contenir.
     * Si l'une manque, on bascule sur le fallback.
     */
    private const REQUIRED_KEYS = ['decision', 'score', 'reasoning'];

    /**
     * Valeurs autorisées pour la clé "decision".
     */
    private const VALID_DECISIONS = ['approve', 'reject', 'manual_review'];

    public function analyze(
        string $phoneNumber,
        array  $nokiaPayload,
        array  $context,
        App    $app
    ): array {
        $prompt   = $this->buildPrompt($phoneNumber, $nokiaPayload, $context);
        $settings = $app->ai_settings ?? [];

        try {
            $result = match ($app->llm_provider) {
                'openai' => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
                'gemini' => $this->callGemini($prompt, $app->llm_api_key, $settings),
                'claude' => $this->callClaude($prompt, $app->llm_api_key, $settings),
                default  => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
            };

            // Valider que la réponse contient toutes les clés requises
            // ET que la décision est une valeur connue
            if (!$this->isValidResponse($result['response'] ?? [])) {
                Log::warning('AiAnalysisService: réponse IA incomplète ou invalide', [
                    'provider' => $app->llm_provider,
                    'response' => $result['response'] ?? null,
                    'raw'      => $result['raw']      ?? null,
                ]);
                return $this->fallbackResponse(
                    'Réponse IA non conforme au schéma attendu',
                    $result['token_count'] ?? 0
                );
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('AiAnalysisService: exception inattendue', [
                'provider' => $app->llm_provider,
                'error'    => $e->getMessage(),
            ]);
            return $this->fallbackResponse($e->getMessage());
        }
    }

    /**
     * Vérifie qu'un tableau de réponse IA est exploitable.
     */
    private function isValidResponse(mixed $response): bool
    {
        if (!is_array($response) || empty($response)) {
            return false;
        }

        // Toutes les clés requises doivent être présentes
        foreach (self::REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $response)) {
                return false;
            }
        }

        // La décision doit être une valeur autorisée
        if (!in_array($response['decision'], self::VALID_DECISIONS, true)) {
            return false;
        }

        // Le score doit être un entier ou un float entre 0 et 100
        $score = $response['score'];
        if (!is_numeric($score) || $score < 0 || $score > 100) {
            return false;
        }

        return true;
    }

    private function buildPrompt(string $phoneNumber, array $nokia, array $context): string
    {
        $nokiaJson   = json_encode($nokia,   JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $contextJson = $context
            ? json_encode($context, JSON_PRETTY_PRINT)
            : 'Aucun contexte fourni.';

        return <<<PROMPT
Tu es un expert en détection de fraude mobile pour l'Afrique de l'Ouest.
Analyse les signaux réseau Nokia CAMARA suivants pour le numéro {$phoneNumber} et évalue le risque de fraude.

## Signaux réseau Nokia CAMARA
{$nokiaJson}

## Contexte de la transaction
{$contextJson}

## Ta mission
Basé sur ces signaux, détermine si ce numéro est fiable pour valider une transaction financière.

Règles d'analyse :
- SIM swap récent (< 7 jours) = signal de fraude majeur
- Roaming + SIM swap combinés = quasi-certitude de fraude
- Réseau 2G avec transaction importante = risque élevé (SIM swapping actif)
- Numéro inactif = rejeter immédiatement
- Numéro porté récemment + roaming = risque moyen

## Format de réponse STRICT
Réponds UNIQUEMENT avec ce JSON brut, sans markdown, sans texte avant ni après :
{
  "decision": "approve|reject|manual_review",
  "score": <entier 0-100, 100 = confiance maximale dans la légitimité>,
  "reasoning": "<explication concise en français, 2-3 phrases>",
  "risk_factors": ["<facteur1>", "<facteur2>"],
  "recommendation": "<action recommandée pour le métier>"
}
PROMPT;
    }

    /* -----------------------------------------------------------------
     |  Helper — Nettoyage du JSON retourné par l'IA
     |  Les LLM enveloppent parfois la réponse dans ```json ... ```
     | ----------------------------------------------------------------- */

    private function parseJsonSafe(string $raw): array
    {
        if (empty(trim($raw))) {
            return [];
        }

        // 1. Supprimer les blocs markdown ```json ... ``` ou ``` ... ```
        $clean = preg_replace('/^```(?:json)?\s*/im', '', $raw);
        $clean = preg_replace('/\s*```\s*$/im', '', $clean);
        $clean = trim($clean);

        // 2. Extraire le premier objet JSON complet {…} — ignore le texte qui l'entoure
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

        return is_array($parsed) ? $parsed : [];
    }

    /* -----------------------------------------------------------------
     |  Réponse de secours si l'IA échoue ou retourne du JSON invalide
     | ----------------------------------------------------------------- */

    private function fallbackResponse(string $reason = '', int $tokenCount = 0): array
    {
        return [
            'response' => [
                'decision'       => 'manual_review',
                'score'          => 50,
                'reasoning'      => 'Analyse automatique temporairement indisponible. Une vérification manuelle est requise.',
                'risk_factors'   => ['ai_unavailable'],
                'recommendation' => 'Effectuer une vérification manuelle du numéro avant de valider la transaction.',
            ],
            'token_count' => $tokenCount,
            'raw'         => $reason,
        ];
    }

    /* -----------------------------------------------------------------
     |  OpenAI
     | ----------------------------------------------------------------- */

    private function callOpenAI(string $prompt, string $apiKey, array $settings): array
    {
        $model = $settings['model'] ?? 'gpt-4o-mini';
        $temp  = (float) ($settings['temperature'] ?? 0.2);

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model,
                'temperature' => $temp,
                'messages'    => [
                    ['role' => 'system', 'content' => 'Tu es un expert anti-fraude. Réponds UNIQUEMENT avec du JSON brut valide, sans markdown.'],
                    ['role' => 'user',   'content' => $prompt],
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

        return [
            'response'    => $parsed,
            'token_count' => $tokenCount,
            'raw'         => $rawContent,
        ];
    }

    /* -----------------------------------------------------------------
     |  Gemini
     | ----------------------------------------------------------------- */

    private function callGemini(string $prompt, string $apiKey, array $settings): array
    {
        $model = $settings['model'] ?? 'gemini-1.5-flash';

        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature'      => (float) ($settings['temperature'] ?? 0.2),
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

    /* -----------------------------------------------------------------
     |  Claude (Anthropic)
     | ----------------------------------------------------------------- */

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
              'system'     => 'Tu es un expert anti-fraude. Réponds UNIQUEMENT avec du JSON brut valide, sans markdown, sans texte autour.',
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

    /* -----------------------------------------------------------------
     |  Coût estimé en USD
     | ----------------------------------------------------------------- */

    public function estimateCost(int $tokens, string $provider): float
    {
        $pricesPer1k = [
            'openai' => 0.000150, // gpt-4o-mini
            'gemini' => 0.000075, // gemini-1.5-flash
            'claude' => 0.000080, // claude-haiku
        ];

        $price = $pricesPer1k[$provider] ?? 0.000150;
        return round(($tokens / 1000) * $price, 6);
    }
}