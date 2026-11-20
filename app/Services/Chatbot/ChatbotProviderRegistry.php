<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotProvider;
use App\Models\ChatbotSetting;
use Illuminate\Support\Collection;


class ChatbotProviderRegistry
{
    /**
     * Return providers permitted by the global Chatbot settings.
     *
     * API keys are decrypted by the model cast and must never be
     * returned to controllers, views, or diagnostic output.
     */
    public function candidates(?array $allowedTypes = null): Collection
    {
        $settings = ChatbotSetting::current();

        $externalAllowed = (bool) $settings->external_api_enabled;

        $providers = ChatbotProvider::query()
            ->where('is_enabled', true)
            ->orderBy('priority')
            ->get();

        $candidates = collect();

        foreach ($providers as $provider) {
            // Do not send requests to a provider while its known quota
            // reset time is still in the future.
            if (
                $provider->quota_reset_at !== null &&
                $provider->quota_reset_at->isFuture()
            ) {
                continue;
            }

            $type = strtolower((string) $provider->provider_type);

            if ($type === 'external' && ! $externalAllowed) {
                continue;
            }

            if (! in_array($type, ['external', 'internal'], true)) {
                continue;
            }

            if ($allowedTypes !== null && ! in_array($type, $allowedTypes, true)) {
                continue;
            }

            if ($type === 'internal' && ! $settings->internal_ai_enabled) {
                continue;
            }

            $candidates->push([
                'id' => $provider->id,
                'code' => $provider->provider_code,
                'name' => $provider->provider_name,
                'type' => $type,
                'api_format' => $provider->api_format,
                'model' => $provider->model,
                'base_url' => $provider->base_url,
                'api_key' => $provider->api_key,
                'timeout_seconds' => $provider->timeout_seconds,
                'max_retries' => $provider->max_retries,
                'health_status' => $provider->health_status,
                'source' => 'provider_registry',
            ]);
        }

        /*
         * Backward compatibility:
         * Internal database providers must not hide the existing
         * External Primary / Secondary / Fallback configuration.
         * Legacy providers are considered only for requests that
         * permit External AI, and only when no enabled External
         * database provider is available.
         */
        $hasEnabledExternalProvider = $providers->contains(
            fn ($provider) => strtolower((string) $provider->provider_type) === 'external'
        );

        $legacyExternalAllowed = $externalAllowed
            && ($allowedTypes === null || in_array('external', $allowedTypes, true))
            && ! $hasEnabledExternalProvider;

        if ($legacyExternalAllowed) {
            foreach (['primary', 'secondary', 'fallback'] as $slot) {
                if (! $settings->{$slot . '_enabled'}) {
                    continue;
                }

                if (! $externalAllowed) {
                    continue;
                }

                // Legacy primary/secondary/fallback providers are external.
                if ($allowedTypes !== null && ! in_array('external', $allowedTypes, true)) {
                    continue;
                }

                $providerName = $settings->{$slot . '_provider'};

                if (! filled($providerName)) {
                    continue;
                }

                $candidates->push([
                    'id' => null,
                    'code' => $providerName,
                    'name' => ucfirst($providerName),
                    'type' => 'external',
                    'api_format' => 'openai_compatible',
                    'model' => $settings->{$slot . '_model'},
                    'base_url' => $settings->{$slot . '_base_url'},
                    'api_key' => $settings->{$slot . '_api_key'},
                    'timeout_seconds' => 30,
                    'max_retries' => 1,
                    'health_status' => 'unknown',
                    'source' => 'legacy_' . $slot,
                ]);
            }
        }

        return $candidates->values();
    }

    /**
     * External API requests are forbidden unless the Super Admin
     * has enabled the global switch.
     */
    public function externalRequestsAllowed(): bool
    {
        return (bool) ChatbotSetting::current()->external_api_enabled;
    }

    public function assertExternalRequestsAllowed(): void
    {
        if (! $this->externalRequestsAllowed()) {
            throw new ChatbotProviderException(
                'External AI requests are disabled by the global Chatbot setting.',
                null,
                'external_api_disabled'
            );
        }
    }
}
