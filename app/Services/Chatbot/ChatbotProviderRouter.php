<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotUsageLog;
use Illuminate\Support\Str;
use Throwable;

class ChatbotProviderRouter
{
    public function __construct(
        private readonly ChatbotProviderRegistry $registry,
        private readonly ChatbotProviderClient $client
    ) {}

    /**
     * Try configured providers in registry order.
     * A failed provider does not prevent trying the next provider.
     */
    public function chat(array $messages, ?array $allowedProviderTypes = null): array
    {
        $providers = $this->registry->candidates($allowedProviderTypes);

        if ($providers->isEmpty()) {
            throw new ChatbotProviderException(
                'No AI provider is currently enabled.',
                null,
                'no_provider_available'
            );
        }

        $requestId = (string) Str::uuid();

        $requestJson = json_encode(
            $messages,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $requestHash = hash('sha256', $requestJson ?: '');

        foreach ($providers as $provider) {
            $maxRetries = max(
                0,
                min(3, (int) ($provider['max_retries'] ?? 0))
            );

            for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
                $startedAt = microtime(true);

                try {
                    $result = $this->client->chat($provider, $messages);

                    $latency = (int) round(
                        (microtime(true) - $startedAt) * 1000
                    );

                    $this->recordUsage(
                        $provider,
                        $requestId,
                        $requestHash,
                        $latency,
                        'success',
                        null,
                        null,
                        $result['http_status'] ?? null,
                        $result['usage'] ?? []
                    );

                    $this->updateProviderHealth($provider, 'healthy', null);
                    $result['request_id'] = $requestId;
                    $result['provider_name'] = $provider['name'] ?? null;

                    return $result;
                } catch (ChatbotProviderException $e) {
                    $latency = (int) round(
                        (microtime(true) - $startedAt) * 1000
                    );

                    $this->recordUsage(
                        $provider,
                        $requestId,
                        $requestHash,
                        $latency,
                        'failed',
                        $e->errorCode,
                        $e->getMessage(),
                        $e->httpStatus
                    );

                    $health = match ($e->errorCode) {
                        'authentication_error' => 'auth_error',
                        'rate_limit' => 'rate_limited',
                        'provider_unavailable', 'timeout' => 'unavailable',
                        default => 'degraded',
                    };
                    $this->updateProviderHealth($provider, $health, $e->getMessage());

                    if ($e->errorCode === 'rate_limit') {
                        $this->updateProviderQuotaReset(
                            $provider,
                            $e->retryAfterSeconds
                        );
                    }

                    // Never retry a rate-limited provider immediately.
                    // Fail over to another available provider instead.
                    $retryable = in_array($e->errorCode, [
                        'timeout',
                        'provider_unavailable',
                    ], true);

                    if (! $retryable || $attempt >= $maxRetries) {
                        break;
                    }
                } catch (Throwable $e) {
                    $latency = (int) round(
                        (microtime(true) - $startedAt) * 1000
                    );

                    // Do not expose unexpected exception details.
                    $this->recordUsage(
                        $provider,
                        $requestId,
                        $requestHash,
                        $latency,
                        'failed',
                        'unexpected_provider_error',
                        'Provider request failed unexpectedly.',
                        null
                    );

                    $this->updateProviderHealth(
                        $provider,
                        'degraded',
                        'Provider request failed unexpectedly.'
                    );
                    break;
                }
            }
        }

        throw new ChatbotProviderException(
            'All configured AI providers failed or are unavailable.',
            null,
            'all_providers_failed'
        );
    }


    /**
     * Update health only for persisted registry providers.
     * Legacy settings are intentionally not converted into fake records.
     */
    private function updateProviderHealth(
        array $provider,
        string $status,
        ?string $safeError
    ): void {
        $providerId = $provider['id'] ?? null;

        if (! $providerId || ($provider['source'] ?? '') !== 'provider_registry') {
            return;
        }

        try {
            \App\Models\ChatbotProvider::query()
                ->whereKey($providerId)
                ->update([
                    'health_status' => $status,
                    'last_health_check_at' => now(),
                    'last_error' => $status === 'healthy'
                        ? null
                        : mb_substr((string) $safeError, 0, 500),
                    'updated_at' => now(),
                ]);
        } catch (Throwable) {
            // Health telemetry must never interrupt a chat request.
        }
    }

    private function updateProviderQuotaReset(
        array $provider,
        ?int $retryAfterSeconds
    ): void {
        $providerId = $provider['id'] ?? null;

        if (
            ! $providerId ||
            ($provider['source'] ?? '') !== 'provider_registry' ||
            $retryAfterSeconds === null
        ) {
            return;
        }

        try {
            \App\Models\ChatbotProvider::query()
                ->whereKey($providerId)
                ->update([
                    'quota_reset_at' => now()->addSeconds($retryAfterSeconds),
                    'updated_at' => now(),
                ]);
        } catch (Throwable) {
            // Quota telemetry must never interrupt failover.
        }
    }

    private function recordUsage(
        array $provider,
        string $requestId,
        string $requestHash,
        int $latency,
        string $status,
        ?string $errorCode,
        ?string $safeMessage,
        ?int $httpStatus = null,
        array $usage = []
    ): void {
        try {
            ChatbotUsageLog::create([
                'provider_id' => $provider['id'] ?? null,
                'request_id' => $requestId,
                'provider_slot' => substr(
                    (string) ($provider['source'] ?? 'registry'),
                    0,
                    20
                ),
                'model' => $provider['model'] ?? null,
                'status' => $status,
                'error_code' => $errorCode,
                'http_status' => $httpStatus,
                'latency_ms' => $latency,
                'prompt_tokens' => data_get($usage, 'prompt_tokens'),
                'completion_tokens' => data_get($usage, 'completion_tokens'),
                'total_tokens' => data_get($usage, 'total_tokens'),
                'request_hash' => $requestHash,
                'safe_error_message' => $safeMessage,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Usage logging must not expose secrets or interrupt routing.
        }
    }
}
