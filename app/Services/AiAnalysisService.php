<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    public function analyze(
        string $phoneNumber,
        array  $nokiaPayload,
        array  $context,
        App    $app
    ): array {
        $prompt   = $this->buildPrompt($phoneNumber, $nokiaPayload, $context);
        $settings = $app->ai_settings ?? [];

        return match ($app->llm_provider) {
            'openai' => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
            'gemini' => $this->callGemini($prompt, $app->llm_api_key, $settings),
            'claude' => $this->callClaude($prompt, $app->llm_api_key, $settings),
            default  => $this->callOpenAI($prompt, $app->llm_api_key, $settings),
        };
    }

    private function buildPrompt(string $phoneNumber, array $nokia, array $context): string
    {
        $nokiaJson   = json_encode($nokia, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $contextJson = $context ? json_encode($context, JSON_PRETTY_PRINT) : 'Aucun contexte fourni.';

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
Réponds UNIQUEMENT avec ce JSON, sans texte avant ni après :
{
  "decision": "approve|reject|manual_review",
  "score": <entier 0-100, 100 = confiance maximale dans la légitimité>,
  "reasoning": "<explication concise en français, 2-3 phrases>",
  "risk_factors": ["<facteur1>", "<facteur2>"],
  "recommendation": "<action recommandée pour le métier>"
}
PROMPT;
    }

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
                    ['role' => 'system', 'content' => 'Tu es un expert anti-fraude. Réponds toujours en JSON strict.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

        $data        = $response->json();
        $rawContent  = $data['choices'][0]['message']['content'] ?? '{}';
        $parsed      = json_decode($rawContent, true) ?? [];
        $tokenCount  = $data['usage']['total_tokens'] ?? 0;

        return [
            'response'    => $parsed,
            'token_count' => $tokenCount,
            'raw'         => $rawContent,
        ];
    }

    private function callGemini(string $prompt, string $apiKey, array $settings): array
    {
        $model = $settings['model'] ?? 'gemini-1.5-flash';

        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => [
                    'temperature'     => (float) ($settings['temperature'] ?? 0.2),
                    'responseMimeType'=> 'application/json',
                ],
            ]);

        $raw    = $response->json('candidates.0.content.parts.0.text', '{}');
        $parsed = json_decode($raw, true) ?? [];
        $tokens = $response->json('usageMetadata.totalTokenCount', 0);

        return ['response' => $parsed, 'token_count' => $tokens, 'raw' => $raw];
    }

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
              'messages'   => [['role' => 'user', 'content' => $prompt]],
          ]);

        $raw    = $response->json('content.0.text', '{}');
        $parsed = json_decode($raw, true) ?? [];
        $tokens = ($response->json('usage.input_tokens', 0) + $response->json('usage.output_tokens', 0));

        return ['response' => $parsed, 'token_count' => $tokens, 'raw' => $raw];
    }

    /**
     * Coût estimé en USD selon le provider et le nombre de tokens.
     */
    public function estimateCost(int $tokens, string $provider): float
    {
        // Prix pour 1000 tokens (input+output moyenné)
        $pricesPer1k = [
            'openai' => 0.000150, // gpt-4o-mini
            'gemini' => 0.000075, // gemini-1.5-flash
            'claude' => 0.000080, // claude-haiku
        ];

        $price = $pricesPer1k[$provider] ?? 0.000150;
        return round(($tokens / 1000) * $price, 6);
    }
}