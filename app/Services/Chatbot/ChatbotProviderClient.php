<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ChatbotProviderClient
{
    public function __construct(
        private readonly ChatbotProviderRegistry $registry
    ) {}

    /**
     * Send a chat-completion request to an approved provider.
     *
     * This method deliberately does not retry. Routing and failover
     * will be handled by a separate service.
     */
    public function chat(array $provider, array $messages): array
    {
        $type = strtolower((string) ($provider['type'] ?? ''));

        if (! in_array($type, ['external', 'internal'], true)) {
            throw new ChatbotProviderException('Unsupported provider type.');
        }

        $settings = ChatbotSetting::current();

        // Final security check immediately before any HTTP request.
        if ($type === 'external') {
            $this->registry->assertExternalRequestsAllowed();
        } elseif (! $settings->internal_ai_enabled) {
            throw new ChatbotProviderException(
                'Internal AI is disabled by the Chatbot settings.',
                null,
                'internal_ai_disabled'
            );
        }

        if (($provider['api_format'] ?? '') !== 'openai_compatible') {
            throw new ChatbotProviderException(
                'Unsupported provider API format.'
            );
        }

        $baseUrl = trim((string) ($provider['base_url'] ?? ''));
        $model = trim((string) ($provider['model'] ?? ''));
        $apiKey = trim((string) ($provider['api_key'] ?? ''));

        if ($baseUrl === '' || $model === '') {
            throw new ChatbotProviderException(
                'Provider URL or model is not configured.'
            );
        }

        if ($type === 'external' && $apiKey === '') {
            throw new ChatbotProviderException(
                'External provider API key is not configured.'
            );
        }

        if (! is_array($messages) || $messages === []) {
            throw new ChatbotProviderException(
                'At least one chat message is required.'
            );
        }

        foreach ($messages as $message) {
            if (
                ! is_array($message) ||
                ! isset($message['role'], $message['content']) ||
                ! is_string($message['role']) ||
                ! is_string($message['content'])
            ) {
                throw new ChatbotProviderException(
                    'Invalid chat message structure.'
                );
            }
        }

        $parts = parse_url($baseUrl);

        if (
            $parts === false ||
            empty($parts['host']) ||
            isset($parts['user']) ||
            isset($parts['pass']) ||
            isset($parts['query']) ||
            isset($parts['fragment'])
        ) {
            throw new ChatbotProviderException(
                'Provider base URL is invalid.'
            );
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower(trim((string) $parts['host'], '[]'));

        if (
            ($type === 'external' && $scheme !== 'https') ||
            ($type === 'internal' && ! in_array($scheme, ['http', 'https'], true))
        ) {
            throw new ChatbotProviderException(
                'Provider URL must use an approved HTTP scheme.'
            );
        }

        // External providers must not target local/private IP addresses.
        // Internal DoR AI may legitimately use private network addresses.
        if ($type === 'external') {
            $blockedLocalHosts = [
                'localhost',
                'localhost.localdomain',
            ];

            $isLocalHostname = in_array($host, $blockedLocalHosts, true)
                || str_ends_with($host, '.localhost')
                || str_ends_with($host, '.local')
                || str_ends_with($host, '.internal');

            $isPrivateOrReservedIp = filter_var(
                $host,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            ) === false && filter_var($host, FILTER_VALIDATE_IP) !== false;

            if ($isLocalHostname || $isPrivateOrReservedIp) {
                throw new ChatbotProviderException(
                    'External provider URL cannot target a local or private address.',
                    null,
                    'external_url_not_allowed'
                );
            }
        }

        $baseUrl = rtrim($baseUrl, '/');

        $endpoint = str_ends_with($baseUrl, '/chat/completions')
            ? $baseUrl
            : $baseUrl . '/chat/completions';

        $timeout = max(
            5,
            min(120, (int) ($provider['timeout_seconds'] ?? 30))
        );

        try {
            $request = Http::acceptJson()
                ->asJson()
                ->withOptions(['allow_redirects' => false])
                ->connectTimeout(min(10, $timeout))
                ->timeout($timeout);

            if ($apiKey !== '') {
                $request = $request->withToken($apiKey);
            }

            $response = $request->post($endpoint, [
                'model' => $model,
                'messages' => $messages,
            ]);
        } catch (ConnectionException) {
            throw new ChatbotProviderException(
                'Provider connection failed or timed out.',
                null,
                'provider_unavailable'
            );
        }

        if (! $response->successful()) {
            $status = $response->status();

            $code = match ($status) {
                401, 403 => 'authentication_error',
                408, 504 => 'timeout',
                429 => 'rate_limit',
                500, 502, 503 => 'provider_unavailable',
                default => 'provider_http_error',
            };

            // Do not expose the provider response body or API key.
            $retryAfter = null;

            if ($status === 429) {
                $header = trim((string) $response->header('Retry-After'));

                if ($header !== '') {
                    if (ctype_digit($header)) {
                        $retryAfter = (int) $header;
                    } else {
                        $retryAt = strtotime($header);

                        if ($retryAt !== false) {
                            $retryAfter = max(0, $retryAt - time());
                        }
                    }

                    // Keep provider-supplied delays within a safe 24-hour range.
                    $retryAfter = max(1, min(86400, $retryAfter));
                }
            }

            throw new ChatbotProviderException(
                'Provider request failed.',
                $status,
                $code,
                $retryAfter
            );
        }

        $data = $response->json();

        $content = data_get($data, 'choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new ChatbotProviderException(
                'Provider returned an empty or unsupported response.'
            );
        }

        return [
            'content' => $content,
            'http_status' => $response->status(),
            'model' => data_get($data, 'model', $model),
            'usage' => data_get($data, 'usage', []),
            'provider_id' => $provider['id'] ?? null,
            'provider_code' => $provider['code'] ?? null,
            'provider_type' => $type,
        ];
    }
}
