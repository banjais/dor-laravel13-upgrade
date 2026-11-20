<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class OfficeProfileEmailSyncTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
        ]);

        Schema::create('language', function (Blueprint $table) {
            $table->id();
            $table->string('language_code');
            $table->string('language_name')->nullable();
            $table->string('is_active')->default('1');
            $table->string('is_deleted')->default('0');
            $table->timestamps();
        });

        Schema::create('office', function (Blueprint $table) {
            $table->id();
            $table->string('is_deleted')->default('0');
            $table->string('is_active')->default('1');
            $table->string('office_email_address')->nullable();
            $table->string('office_alternative_email')->nullable();
            $table->string('office_facebook_page')->nullable();
            $table->string('office_instagram_page')->nullable();
            $table->text('google_map_embed')->nullable();
            $table->string('office_subdomain')->nullable();
            $table->timestamps();
        });

        Schema::create('notice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->string('is_active')->default('1');
            $table->string('is_deleted')->default('0');
        });

        Schema::create('office_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('language_id');
            $table->string('office_name')->nullable();
            $table->string('office_address')->nullable();
            $table->string('office_phone_number1')->nullable();
            $table->string('office_phone_number2')->nullable();
            $table->timestamps();
        });

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

        DB::table('language')->insert([
            'id' => 2,
            'language_code' => 'en',
            'language_name' => 'English',
            'is_active' => '1',
            'is_deleted' => '0',
        ]);

        DB::table('office')->insert([
            'id' => 10,
            'is_deleted' => '0',
            'is_active' => '1',
            'office_email_address' => 'old-office@example.test',
            'office_alternative_email' => null,
            'office_subdomain' => 'test-office',
        ]);

        DB::table('office_detail')->insert([
            'office_id' => 10,
            'language_id' => 2,
            'office_name' => 'Test Office',
            'office_address' => 'Test Address',
            'office_phone_number1' => '012345678',
        ]);
    }

    private function makeUser(int $role, string $email, int $officeId = 10): User
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = $email;
        $user->email_address = $email;
        $user->username = $email;
        $user->password = bcrypt('password123');
        $user->office_id = $officeId;
        $user->user_type_id = $role;
        $user->is_active = 1;
        $user->save();

        return $user;
    }

    private function validProfileData(array $overrides = []): array
    {
        return array_merge([
            'name_en' => 'Updated Test Office',
            'office_address_en' => 'Updated Test Address',
            'primary_contact_en' => '012345678',
            'secondary_contact_en' => '',
            'email' => 'primary@example.test',
            'alternative_email' => 'alternative@example.test',
            'facebook_page' => '',
            'google_maps_link' => '',
        ], $overrides);
    }

    public function test_primary_email_is_synchronized_to_office_admin_login(): void
    {
        $admin = $this->makeUser(12, 'old-admin@example.test');

        $this->actingAs($admin)
            ->post(route('admin.office.update', 10), $this->validProfileData())
            ->assertRedirect(route('admin.office.edit', 10));

        $this->assertDatabaseHas('office', [
            'id' => 10,
            'office_email_address' => 'primary@example.test',
            'office_alternative_email' => 'alternative@example.test',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email_address' => 'primary@example.test',
            'username' => 'primary@example.test',
        ]);
    }

    public function test_alternative_email_is_used_when_primary_email_is_blank(): void
    {
        $admin = $this->makeUser(12, 'old-admin@example.test');

        $this->actingAs($admin)
            ->post(route('admin.office.update', 10), $this->validProfileData([
                'email' => '',
                'alternative_email' => 'alternative@example.test',
            ]))
            ->assertRedirect(route('admin.office.edit', 10));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email_address' => 'alternative@example.test',
            'username' => 'alternative@example.test',
        ]);
    }

    public function test_existing_login_email_is_preserved_when_both_profile_emails_are_blank(): void
    {
        $admin = $this->makeUser(12, 'old-admin@example.test');

        $this->actingAs($admin)
            ->post(route('admin.office.update', 10), $this->validProfileData([
                'email' => '',
                'alternative_email' => '',
            ]))
            ->assertRedirect(route('admin.office.edit', 10));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email_address' => 'old-admin@example.test',
            'username' => 'old-admin@example.test',
        ]);

        $this->assertDatabaseHas('office', [
            'id' => 10,
            'office_email_address' => null,
            'office_alternative_email' => null,
        ]);
    }

    public function test_duplicate_active_account_email_rejects_and_rolls_back_profile_update(): void
    {
        $admin = $this->makeUser(12, 'old-admin@example.test');
        $this->makeUser(13, 'taken@example.test');

        $this->actingAs($admin)
            ->from(route('admin.office.edit', 10))
            ->post(route('admin.office.update', 10), $this->validProfileData([
                'email' => 'taken@example.test',
            ]))
            ->assertRedirect(route('admin.office.edit', 10))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseHas('office', [
            'id' => 10,
            'office_email_address' => 'old-office@example.test',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email_address' => 'old-admin@example.test',
            'username' => 'old-admin@example.test',
        ]);
    }

    public function test_only_authenticated_office_admin_account_is_synchronized(): void
    {
        $admin = $this->makeUser(12, 'old-admin@example.test');
        $otherAdmin = $this->makeUser(12, 'other-admin@example.test');

        $this->actingAs($admin)
            ->post(route('admin.office.update', 10), $this->validProfileData())
            ->assertRedirect(route('admin.office.edit', 10));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'email_address' => 'primary@example.test',
            'username' => 'primary@example.test',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $otherAdmin->id,
            'email_address' => 'other-admin@example.test',
            'username' => 'other-admin@example.test',
        ]);
    }
}
