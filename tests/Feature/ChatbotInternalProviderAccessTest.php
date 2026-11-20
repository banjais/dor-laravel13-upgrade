<?php

namespace Tests\Feature;

use App\Models\ChatbotProvider;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChatbotInternalProviderAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('email_address')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_type_id');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
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
    }

    private function makeUser(int $role): User
    {
        $user = new User();
        $user->name = 'Test Admin';
        $user->email = uniqid('admin') . '@example.test';
        $user->username = uniqid('admin') . '@example.test';
        $user->password = bcrypt('password123');
        $user->user_type_id = $role;
        $user->is_active = 1;
        $user->save();

        return $user;
    }

    private function provider(): ChatbotProvider
    {
        return ChatbotProvider::create([
            'provider_code' => 'test-internal',
            'provider_name' => 'Test Internal AI',
            'provider_type' => 'internal',
            'api_format' => 'openai_compatible',
            'model' => 'test-model',
            'base_url' => 'http://internal-ai.example.test/v1',
            'priority' => 100,
            'is_enabled' => true,
            'timeout_seconds' => 30,
            'max_retries' => 1,
        ]);
    }

    public function test_role_12_and_role_13_cannot_access_internal_provider_management(): void
    {
        foreach ([12, 13] as $role) {
            $admin = $this->makeUser($role);

            $this->actingAs($admin)
                ->get(route('admin.chatbot-internal-providers.index'))
                ->assertForbidden();

            $this->actingAs($admin)
                ->post(route('admin.chatbot-internal-providers.store'), [])
                ->assertForbidden();

            $provider = $this->provider();

            $this->actingAs($admin)
                ->post(route('admin.chatbot-internal-providers.update', $provider->id), [])
                ->assertForbidden();

            $this->actingAs($admin)
                ->post(route('admin.chatbot-internal-providers.destroy', $provider->id))
                ->assertForbidden();

            $this->assertDatabaseHas('chatbot_providers', [
                'id' => $provider->id,
                'provider_code' => 'test-internal',
            ]);

            $provider->delete();
        }
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.chatbot-internal-providers.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_super_admin_can_create_internal_provider_with_encrypted_api_key(): void
    {
        $admin = $this->makeUser(2);
        $plainKey = 'test-internal-secret-key';

        $response = $this->actingAs($admin)->post(
            route('admin.chatbot-internal-providers.store'),
            [
                'provider_code' => 'dor-test-internal',
                'provider_name' => 'DoR Test Internal AI',
                'model' => 'test-model',
                'base_url' => 'http://internal-ai.example.test/v1',
                'api_key' => $plainKey,
                'priority' => 100,
                'timeout_seconds' => 30,
                'max_retries' => 1,
                'is_enabled' => 1,
            ]
        );

        $response->assertRedirect(route('admin.chatbot-internal-providers.index'));

        $provider = ChatbotProvider::where('provider_code', 'dor-test-internal')->firstOrFail();

        $this->assertSame('internal', $provider->provider_type);
        $this->assertSame($plainKey, $provider->api_key);

        $storedKey = DB::table('chatbot_providers')
            ->where('id', $provider->id)
            ->value('api_key');

        $this->assertNotSame($plainKey, $storedKey);
        $this->assertNotEmpty($storedKey);
    }
}
