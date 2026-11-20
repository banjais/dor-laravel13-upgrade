<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PasswordResetOtpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['mail.default' => 'array']);

        View::share('site_name', 'DoR Test');
        View::share('abs_site_url', config('app.url'));

        Schema::create('configuration', function (Blueprint $table) {
            $table->id();
            $table->string('configuration_name')->unique();
            $table->text('configuration_value')->nullable();
        });

        DB::table('configuration')->insert([
            ['configuration_name' => 'absolute_site_path', 'configuration_value' => 'http://localhost'],
            ['configuration_name' => 'relative_site_path', 'configuration_value' => '/'],
            ['configuration_name' => 'site_name', 'configuration_value' => 'DoR Test'],
            ['configuration_name' => 'no_reply_email', 'configuration_value' => 'noreply@example.test'],
            ['configuration_name' => 'from_name', 'configuration_value' => 'DoR Test'],
            ['configuration_name' => 'feedback_cc_to', 'configuration_value' => ''],
        ]);

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
            $table->timestamp('password_changed_on')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('otp_hash', 255);
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    private function makeUser(int $role, string $email): int
    {
        return DB::table('users')->insertGetId([
            'name' => 'Test User',
            'email' => $email,
            'email_address' => $email,
            'username' => $email,
            'password' => Hash::make('old-password'),
            'user_type_id' => $role,
            'is_active' => 1,
            'is_deleted' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createOtp(int $userId, string $otp = '123456', int $expiryMinutes = 5): int
    {
        return DB::table('password_reset_otps')->insertGetId([
            'user_id' => $userId,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes($expiryMinutes),
            'attempts' => 0,
            'used_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_super_admin_office_admin_and_circular_admin_can_request_otp(): void
    {
        foreach ([2, 12, 13] as $role) {
            $email = "role{$role}@example.test";
            $userId = $this->makeUser($role, $email);

            $this->withSession([])
                ->post(route('admin.password.email'), ['email' => $email])
                ->assertRedirect(route('admin.password.request'))
                ->assertSessionHas('password_reset_user_id', $userId);

            $this->assertDatabaseHas('password_reset_otps', [
                'user_id' => $userId,
                'used_at' => null,
            ]);
        }
    }

    public function test_test_mode_without_valid_recipient_blocks_otp_delivery(): void
    {
        $email = 'test-mode@example.test';
        $this->makeUser(2, $email);

        config([
            'general.email.is_test_mode' => true,
            'general.email.test_email_to' => '',
        ]);

        Mail::shouldReceive('mailer')->never();

        $this->withSession([])
            ->post(route('admin.password.email'), ['email' => $email])
            ->assertRedirect()
            ->assertSessionMissing('password_reset_user_id')
            ->assertSessionHas('error');

        $this->assertDatabaseCount('password_reset_otps', 0);
    }

    public function test_test_mode_sends_otp_only_to_configured_test_recipient(): void
    {
        $email = 'real-account@example.test';
        $testRecipient = 'otp-test-inbox@example.test';
        $userId = $this->makeUser(2, $email);

        config([
            'general.email.is_test_mode' => true,
            'general.email.test_email_to' => $testRecipient,
            'general.email.test_email_from' => 'test-sender@example.test',
        ]);

        $message = \Mockery::mock();
        $message->shouldReceive('from')->once()->with('test-sender@example.test', 'DoR Test');
        $message->shouldReceive('to')->once()->with($testRecipient)->andReturnSelf();
        $message->shouldReceive('subject')->once()->with('Department of Roads - Password Reset OTP')->andReturnSelf();
        $message->shouldNotReceive('cc');

        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')
            ->once()
            ->with('emails.email', \Mockery::type('array'), \Mockery::type('callable'))
            ->andReturnUsing(function ($view, $data, $callback) use ($message) {
                $callback($message);
                return new \stdClass();
            });

        Mail::shouldReceive('mailer')->once()->withNoArgs()->andReturn($mailer);

        $this->withSession([])
            ->post(route('admin.password.email'), ['email' => $email])
            ->assertRedirect(route('admin.password.request'))
            ->assertSessionHas('password_reset_user_id', $userId);

        $this->assertDatabaseHas('password_reset_otps', [
            'user_id' => $userId,
            'used_at' => null,
        ]);
    }

    public function test_failed_email_delivery_preserves_previous_otp(): void
    {
        $email = 'delivery-failure@example.test';
        $userId = $this->makeUser(2, $email);
        $oldOtpId = $this->createOtp($userId);

        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')->once()->andReturn(null);
        Mail::shouldReceive('mailer')->once()->andReturn($mailer);

        $this->withSession(['password_reset_user_id' => $userId])
            ->post(route('admin.password.email'), ['email' => $email])
            ->assertRedirect()
            ->assertSessionHas('password_reset_user_id', $userId);

        $this->assertDatabaseHas('password_reset_otps', [
            'id' => $oldOtpId,
            'used_at' => null,
        ]);

        $this->assertDatabaseCount('password_reset_otps', 1);
    }

    public function test_successful_resend_invalidates_previous_otp(): void
    {
        $email = 'resend@example.test';
        $userId = $this->makeUser(2, $email);
        $oldOtpId = $this->createOtp($userId);

        $this->withSession([])
            ->post(route('admin.password.email'), ['email' => $email])
            ->assertRedirect(route('admin.password.request'))
            ->assertSessionHas('password_reset_user_id', $userId)
            ->assertSessionHas('status');

        $this->assertNotNull(
            DB::table('password_reset_otps')->where('id', $oldOtpId)->value('used_at')
        );

        $newOtps = DB::table('password_reset_otps')
            ->where('user_id', $userId)
            ->whereNull('used_at')
            ->get();

        $this->assertCount(1, $newOtps);
        $this->assertNotSame($oldOtpId, $newOtps->first()->id);
        $this->assertSame(0, (int) $newOtps->first()->attempts);
    }

    public function test_inactive_and_deleted_accounts_cannot_request_otp(): void
    {
        $inactiveId = $this->makeUser(2, 'inactive@example.test');
        DB::table('users')->where('id', $inactiveId)->update(['is_active' => 0]);

        $deletedId = $this->makeUser(12, 'deleted@example.test');
        DB::table('users')->where('id', $deletedId)->update(['deleted_at' => now()]);

        foreach (['inactive@example.test', 'deleted@example.test'] as $email) {
            $this->withSession([])
                ->post(route('admin.password.email'), ['email' => $email])
                ->assertRedirect()
                ->assertSessionMissing('password_reset_user_id');
        }

        $this->assertDatabaseCount('password_reset_otps', 0);
    }

    public function test_email_lookup_is_case_insensitive(): void
    {
        $email = 'case-check@example.test';
        $userId = $this->makeUser(2, $email);

        $this->withSession([])
            ->post(route('admin.password.email'), ['email' => 'CASE-CHECK@EXAMPLE.TEST'])
            ->assertRedirect(route('admin.password.request'))
            ->assertSessionHas('password_reset_user_id', $userId);

        $this->assertDatabaseHas('password_reset_otps', [
            'user_id' => $userId,
            'used_at' => null,
        ]);
    }

    public function test_project_admin_cannot_request_otp(): void
    {
        $email = 'project-admin@example.test';
        $this->makeUser(5, $email);

        $this->post(route('admin.password.email'), ['email' => $email])
            ->assertRedirect()
            ->assertSessionMissing('password_reset_user_id');

        $this->assertDatabaseCount('password_reset_otps', 0);
    }

    public function test_valid_otp_resets_password_and_invalidates_otp(): void
    {
        $userId = $this->makeUser(12, 'office-admin@example.test');
        $otpId = $this->createOtp($userId);

        $this->withSession(['password_reset_user_id' => $userId])
            ->post(route('admin.password.verify'), [
                'otp' => '123456',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionMissing('password_reset_user_id');

        $user = DB::table('users')->where('id', $userId)->first();

        $this->assertTrue(Hash::check('new-password-123', $user->password));
        $this->assertNotNull($user->password_changed_on);

        $this->assertDatabaseHas('password_reset_otps', [
            'id' => $otpId,
        ]);

        $this->assertNotNull(DB::table('password_reset_otps')->where('id', $otpId)->value('used_at'));
    }

    public function test_expired_otp_cannot_reset_password(): void
    {
        $userId = $this->makeUser(2, 'super-admin@example.test');
        $this->createOtp($userId, '123456', -1);

        $this->withSession(['password_reset_user_id' => $userId])
            ->post(route('admin.password.verify'), [
                'otp' => '123456',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertSessionHasErrors('otp');

        $user = DB::table('users')->where('id', $userId)->first();

        $this->assertTrue(Hash::check('old-password', $user->password));
    }

    public function test_five_invalid_attempts_lock_the_otp(): void
    {
        $userId = $this->makeUser(13, 'circular-admin@example.test');
        $otpId = $this->createOtp($userId);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withSession(['password_reset_user_id' => $userId])
                ->post(route('admin.password.verify'), [
                    'otp' => '999999',
                    'password' => 'new-password-123',
                    'password_confirmation' => 'new-password-123',
                ])
                ->assertSessionHasErrors('otp');
        }

        $this->assertDatabaseHas('password_reset_otps', [
            'id' => $otpId,
            'attempts' => 5,
        ]);

        $this->assertNotNull(DB::table('password_reset_otps')->where('id', $otpId)->value('used_at'));
    }
}
