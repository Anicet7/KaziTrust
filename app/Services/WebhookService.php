<?php

namespace App\Services;

use App\Models\App;
use App\Models\TrustLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function dispatch(App $app, TrustLog $log, string $requestId): void
    {
        $payload = [
            'event'       => 'trust.analyzed',
            'request_id'  => $requestId,
            'phone_number'=> $log->phone_number,
            'decision'    => $log->ai_response['decision'] ?? null,
            'score'       => $log->ai_response['score'] ?? null,
            'timestamp'   => $log->created_at->toIso8601String(),
        ];

        $signature = $this->sign($payload, $app->webhook_secret);

        try {
            Http::withHeaders([
                'X-KaziTrust-Signature' => 'sha256=' . $signature,
                'X-KaziTrust-Event'     => 'trust.analyzed',
                'Content-Type'          => 'application/json',
            ])->timeout(5)->post($app->webhook_url, $payload);
        } catch (\Exception $e) {
            // Fire & forget — on logue mais on ne bloque jamais la réponse client
            Log::warning('Webhook delivery failed', [
                'app_id' => $app->id,
                'url'    => $app->webhook_url,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    private function sign(array $payload, ?string $secret): string
    {
        if (!$secret) return '';
        return hash_hmac('sha256', json_encode($payload), $secret);
    }
}