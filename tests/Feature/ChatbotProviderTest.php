<?php

namespace Tests\Feature;

use App\Models\ChatbotProvider;
use App\Models\ChatbotSetting;
use App\Models\ChatbotUsageLog;
use App\Services\Chatbot\ChatbotProviderClient;
use App\Services\Chatbot\ChatbotProviderException;
use App\Services\Chatbot\ChatbotProviderRouter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChatbotProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('chatbot_usage_logs');
        Schema::dropIfExists('chatbot_providers');
        Schema::dropIfExists('chatbot_settings');

        Schema::create('chatbot_settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 30)->default('global')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('bot_name', 100)->default('DoR Assistant');
            $table->text('welcome_message')->nullable();
            $table->text('offline_message')->nullable();
            $table->string('response_language', 10)->default('both');
            $table->string('position', 20)->default('bottom_right');
            $table->string('panel_color', 7)->default('#FFFFFF');
            $table->string('header_color', 7)->default('#142D4E');
            $table->string('user_message_color', 7)->default('#1769AA');
            $table->string('bot_message_color', 7)->default('#F1F4F7');
            $table->string('text_color', 7)->default('#263238');
            $table->string('ai_provider', 20)->default('openai');
            $table->string('ai_model', 100)->default('gpt-4o-mini');
            $table->text('api_key')->nullable();
            $table->string('api_format', 30)->default('openai_compatible');
            $table->string('website_base_url', 2048)->default('https://dor.gov.np');
            $table->boolean('website_index_enabled')->default(true);
            $table->boolean('external_api_enabled')->default(false);
            $table->boolean('internal_ai_enabled')->default(false);
            $table->string('hardware_mode', 10)->default('cpu');
            $table->boolean('gpu_enabled')->default(false);
            $table->boolean('supported_by_visible')->default(false);
            $table->boolean('provider_info_visible')->default(false);
            $table->boolean('primary_enabled')->default(false);
            $table->string('primary_provider', 30)->nullable();
            $table->string('primary_model', 150)->nullable();
            $table->text('primary_base_url')->nullable();
            $table->text('primary_api_key')->nullable();
            $table->boolean('secondary_enabled')->default(false);
            $table->string('secondary_provider', 30)->nullable();
            $table->string('secondary_model', 150)->nullable();
            $table->text('secondary_base_url')->nullable();
            $table->text('secondary_api_key')->nullable();
            $table->boolean('fallback_enabled')->default(false);
            $table->string('fallback_provider', 30)->nullable();
            $table->string('fallback_model', 150)->nullable();
            $table->text('fallback_base_url')->nullable();
            $table->text('fallback_api_key')->nullable();
            $table->boolean('cache_enabled')->default(true);
            $table->boolean('cache_exact_match_enabled')->default(true);
            $table->unsignedInteger('cache_ttl_minutes')->default(1440);
            $table->boolean('voice_enabled')->default(false);
            $table->boolean('speech_input_enabled')->default(false);
            $table->boolean('voice_output_enabled')->default(false);
            $table->string('voice_type', 10)->default('female');
            $table->decimal('voice_speed', 3, 2)->default(1.00);
            $table->timestamps();
        });

        Schema::create('chatbot_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider_code', 80)->unique();
            $table->string('provider_name', 150);
            $table->string('provider_type', 20)->default('external');
            $table->string('api_format', 40)->default('openai_compatible');
            $table->string('model', 150)->nullable();
            $table->text('base_url')->nullable();
            $table->longText('api_key')->nullable();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_enabled')->default(false);
            $table->unsignedSmallInteger('timeout_seconds')->default(30);
            $table->unsignedTinyInteger('max_retries')->default(1);
            $table->string('health_status', 30)->default('unknown');
            $table->timestamp('last_health_check_at')->nullable();
            $table->timestamp('quota_reset_at')->nullable();
            $table->text('last_error')->nullable();
            $table->longText('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('chatbot_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')
                ->nullable()
                ->constrained('chatbot_providers')
                ->nullOnDelete();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->uuid('request_id')->nullable();
            $table->string('provider_slot', 20)->nullable();
            $table->string('model', 150)->nullable();
            $table->string('status', 30);
            $table->string('error_code', 80)->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();
            $table->decimal('estimated_cost', 12, 6)->nullable();
            $table->char('request_hash', 64)->nullable();
            $table->text('safe_error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    private function message(): array
    {
        return [
            ['role' => 'user', 'content' => 'Test question'],
        ];
    }

    private function provider(
        string $code,
        int $priority,
        string $url,
        string $key
    ): ChatbotProvider {
        return ChatbotProvider::create([
            'provider_code' => $code,
            'provider_name' => ucfirst($code),
            'provider_type' => 'external',
            'api_format' => 'openai_compatible',
            'model' => $code . '-model',
            'base_url' => $url,
            'api_key' => $key,
            'priority' => $priority,
            'is_enabled' => true,
            'timeout_seconds' => 10,
            'max_retries' => 0,
        ]);
    }

    public function test_external_api_off_blocks_request_before_http(): void
    {
        ChatbotSetting::current();

        Http::fake();

        try {
            app(ChatbotProviderClient::class)->chat([
                'type' => 'external',
                'api_format' => 'openai_compatible',
                'base_url' => 'https://api.example.com/v1',
                'model' => 'test-model',
                'api_key' => 'test-secret',
            ], $this->message());

            $this->fail('External request should have been blocked.');
        } catch (ChatbotProviderException $e) {
            $this->assertSame('external_api_disabled', $e->errorCode);
        }

        Http::assertNothingSent();
    }

    public function test_internal_ai_off_blocks_request_before_http(): void
    {
        ChatbotSetting::current();

        Http::fake();

        try {
            app(ChatbotProviderClient::class)->chat([
                'type' => 'internal',
                'api_format' => 'openai_compatible',
                'base_url' => 'http://127.0.0.1:8001/v1',
                'model' => 'internal-model',
                'api_key' => '',
            ], $this->message());

            $this->fail('Internal request should have been blocked.');
        } catch (ChatbotProviderException $e) {
            $this->assertSame('internal_ai_disabled', $e->errorCode);
        }

        Http::assertNothingSent();
    }

    public function test_external_provider_returns_successful_response_when_enabled(): void
    {
        $settings = ChatbotSetting::current();
        $settings->external_api_enabled = true;
        $settings->save();

        Http::fake([
            'api.example.com/*' => Http::response([
                'model' => 'test-model',
                'choices' => [
                    ['message' => ['content' => 'DoR test answer']],
                ],
                'usage' => [
                    'prompt_tokens' => 10,
                    'completion_tokens' => 5,
                    'total_tokens' => 15,
                ],
            ], 200),
        ]);

        $result = app(ChatbotProviderClient::class)->chat([
            'type' => 'external',
            'api_format' => 'openai_compatible',
            'base_url' => 'https://api.example.com/v1',
            'model' => 'test-model',
            'api_key' => 'test-secret',
        ], $this->message());

        $this->assertSame('DoR test answer', $result['content']);
        $this->assertSame('test-model', $result['model']);
        $this->assertSame(15, $result['usage']['total_tokens']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.example.com/v1/chat/completions'
                && $request->hasHeader('Authorization', 'Bearer test-secret');
        });
    }

    public function test_registry_skips_rate_limited_provider_until_quota_reset(): void
    {
        $settings = ChatbotSetting::current();
        $settings->external_api_enabled = true;
        $settings->save();

        $first = $this->provider(
            'quota-limited-provider',
            1,
            'https://limited.example.com/v1',
            'limited-secret'
        );

        $this->provider(
            'available-provider',
            2,
            'https://available.example.com/v1',
            'available-secret'
        );

        $first->update([
            'quota_reset_at' => now()->addMinutes(2),
        ]);

        $registry = app(\App\Services\Chatbot\ChatbotProviderRegistry::class);

        $beforeReset = $registry->candidates()->pluck('code')->all();

        $this->assertSame(['available-provider'], $beforeReset);

        $first->update([
            'quota_reset_at' => now()->subSecond(),
        ]);

        $afterReset = $registry->candidates()->pluck('code')->all();

        $this->assertSame(
            ['quota-limited-provider', 'available-provider'],
            $afterReset
        );
    }

    public function test_router_fails_over_after_rate_limit_and_logs_without_api_key(): void
    {
        $settings = ChatbotSetting::current();
        $settings->external_api_enabled = true;
        $settings->save();

        $firstProvider = $this->provider(
            'first-provider',
            1,
            'https://first.example.com/v1',
            'first-secret-key'
        );

        $this->provider(
            'second-provider',
            2,
            'https://second.example.com/v1',
            'second-secret-key'
        );

        Http::fake(function ($request) {
            if (str_contains($request->url(), 'first.example.com')) {
                return Http::response(
                    ['error' => 'rate limited'],
                    429,
                    ['Retry-After' => '120']
                );
            }

            return Http::response([
                'model' => 'second-provider-model',
                'choices' => [
                    ['message' => ['content' => 'Fallback answer']],
                ],
                'usage' => [
                    'prompt_tokens' => 12,
                    'completion_tokens' => 8,
                    'total_tokens' => 20,
                ],
            ], 200);
        });

        $result = app(ChatbotProviderRouter::class)->chat($this->message());

        $this->assertSame('Fallback answer', $result['content']);
        $this->assertSame('second-provider', $result['provider_code']);

        $this->assertSame(2, ChatbotUsageLog::count());
        $this->assertSame(
            ['failed', 'success'],
            ChatbotUsageLog::orderBy('id')->pluck('status')->all()
        );
        $failedLog = ChatbotUsageLog::where('status', 'failed')->first();

        $this->assertSame('rate_limit', $failedLog->error_code);
        $this->assertSame(429, (int) $failedLog->http_status);
        $this->assertSame(200, (int) ChatbotUsageLog::where('status', 'success')->value('http_status'));
        $this->assertNotNull($firstProvider->fresh()->quota_reset_at);
        $this->assertTrue($firstProvider->fresh()->quota_reset_at->isFuture());

        $logs = json_encode(ChatbotUsageLog::all()->toArray());

        $this->assertStringNotContainsString('first-secret-key', $logs);
        $this->assertStringNotContainsString('second-secret-key', $logs);
    }

    public function test_internal_provider_does_not_hide_legacy_external_gemini(): void
    {
        $settings = ChatbotSetting::current();

        $settings->forceFill([
            'external_api_enabled' => true,
            'internal_ai_enabled' => true,
            'primary_enabled' => true,
            'primary_provider' => 'gemini',
            'primary_model' => 'gemini-test-model',
            'primary_base_url' => 'https://example.com/v1',
            'primary_api_key' => 'legacy-test-key',
        ])->save();

        ChatbotProvider::create([
            'provider_code' => 'dor_internal_test',
            'provider_name' => 'DoR Internal Test',
            'provider_type' => 'internal',
            'api_format' => 'openai_compatible',
            'model' => 'internal-test-model',
            'base_url' => 'http://127.0.0.1:9000/v1',
            'api_key' => 'internal-test-key',
            'priority' => 1,
            'is_enabled' => true,
            'timeout_seconds' => 10,
            'max_retries' => 0,
        ]);

        $registry = app(\App\Services\Chatbot\ChatbotProviderRegistry::class);

        $external = $registry->candidates(['external']);
        $internal = $registry->candidates(['internal']);

        $this->assertCount(1, $external);
        $this->assertSame('legacy_primary', $external->first()['source']);
        $this->assertSame('gemini', $external->first()['code']);

        $this->assertCount(1, $internal);
        $this->assertSame('internal', $internal->first()['type']);
        $this->assertSame('provider_registry', $internal->first()['source']);
    }


}
