<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class CircularAdminManagementTest extends TestCase
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

        Schema::create('office', function (Blueprint $table) {
            $table->id();
            $table->string('is_deleted')->default('0');
            $table->string('is_active')->default('1');
            $table->string('office_email_address')->nullable();
            $table->string('office_subdomain')->nullable();
            $table->timestamps();
        });

        Schema::create('office_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id');
            $table->string('office_name')->nullable();
            $table->string('office_address')->nullable();
            $table->unsignedBigInteger('language_id')->default(2);
            $table->timestamps();
        });

        // Office model legacy raw SQL uses these physical prefixed table names.
        Schema::create('pndc_office', function (Blueprint $table) {
            $table->id();
            $table->string('is_deleted')->default('0');
            $table->string('is_active')->default('1');
            $table->string('office_email_address')->nullable();
            $table->string('office_alternative_email')->nullable();
            $table->string('office_phone_number1')->nullable();
            $table->string('office_subdomain')->nullable();
        });

        Schema::create('pndc_office_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id');
            $table->string('office_name')->nullable();
            $table->string('office_address')->nullable();
            $table->string('office_phone_number1')->nullable();
            $table->unsignedBigInteger('language_id')->default(2);
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

        Schema::create('user_access_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id')->default(1);
            $table->unsignedBigInteger('user_map_id');
            $table->boolean('access_add')->default(false);
            $table->boolean('access_delete')->default(false);
            $table->boolean('access_update')->default(false);
            $table->boolean('access_publish')->default(false);
            $table->boolean('access_view')->default(true);
            $table->boolean('access_upload')->default(false);
            $table->boolean('access_download')->default(false);
        });

        foreach ([10, 20] as $officeId) {
            DB::table('office')->insert([
                'id' => $officeId,
                'is_deleted' => '0',
                'is_active' => '1',
                'office_subdomain' => 'test-office-' . $officeId,
            ]);

            DB::table('pndc_office')->insert([
                'id' => $officeId,
                'is_deleted' => '0',
                'is_active' => '1',
                'office_email_address' => 'office' . $officeId . '@example.test',
                'office_subdomain' => 'test-office-' . $officeId,
            ]);

            DB::table('office_detail')->insert([
                'office_id' => $officeId,
                'office_name' => 'Test Office ' . $officeId,
                'language_id' => 2,
            ]);

            DB::table('pndc_office_detail')->insert([
                'office_id' => $officeId,
                'office_name' => 'Test Office ' . $officeId,
                'office_address' => 'Test Address ' . $officeId,
                'language_id' => 2,
            ]);
        }
    }

    private function makeUser(int $role, ?int $officeId = null): User
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = uniqid('user') . '@example.test';
        $user->username = uniqid('user') . '@example.test';
        $user->email_address = $user->username;
        $user->password = bcrypt('password123');
        $user->office_id = $officeId;
        $user->user_type_id = $role;
        $user->is_active = 1;
        $user->save();

        \Illuminate\Support\Facades\DB::table('user_access_list')->insert([
            'module_id' => 1,
            'user_map_id' => $user->id,
            'access_view' => 1,
        ]);

        return $user;
    }

    public function test_super_admin_can_create_circular_admin_for_selected_office(): void
    {
        $admin = $this->makeUser(2);

        $response = $this->actingAs($admin)->post(route('admin.circular-admin.store'), [
            'email_address' => 'recovery10@example.test',
            'password' => 'Password123!',
            'confirm_password' => 'Password123!',
            'office_id' => 10,
        ]);

        $response->assertRedirect(route('admin.circular-admin.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'recovery10@example.test',
            'email_address' => 'recovery10@example.test',
            'office_id' => 10,
            'user_type_id' => 13,
            'is_active' => 1,
        ]);
    }

    public function test_office_admin_can_create_account_only_for_own_office(): void
    {
        $admin = $this->makeUser(12, 10);

        $response = $this->actingAs($admin)->post(route('admin.circular-admin.store'), [
            'email_address' => 'office10-recovery@example.test',
            'password' => 'Password123!',
            'confirm_password' => 'Password123!',
        ]);

        $response->assertRedirect(route('admin.circular-admin.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'office10-recovery@example.test',
            'office_id' => 10,
            'user_type_id' => 13,
        ]);
    }

    public function test_office_admin_cannot_submit_another_office_id(): void
    {
        $admin = $this->makeUser(12, 10);

        $response = $this->actingAs($admin)->from(route('admin.circular-admin.create'))
            ->post(route('admin.circular-admin.store'), [
                'username' => 'wrongoffice@example.test',
                'email_address' => 'wrongoffice-recovery@example.test',
                'password' => 'Password123!',
                'confirm_password' => 'Password123!',
                'office_id' => 20,
            ]);

        $response->assertRedirect(route('admin.circular-admin.create'));
        $response->assertSessionHasErrors('office_id');

        $this->assertDatabaseMissing('users', [
            'username' => 'wrongoffice@example.test',
        ]);
    }

    public function test_office_admin_cannot_edit_another_offices_account(): void
    {
        $admin = $this->makeUser(12, 10);
        $otherAccount = $this->makeUser(13, 20);

        $this->actingAs($admin)
            ->get(route('admin.circular-admin.edit', $otherAccount->id))
            ->assertNotFound();
    }

    public function test_office_admin_cannot_change_status_of_another_offices_account(): void
    {
        $admin = $this->makeUser(12, 10);
        $otherAccount = $this->makeUser(13, 20);

        $this->actingAs($admin)
            ->post(route('admin.circular-admin.status', $otherAccount->id))
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $otherAccount->id,
            'is_active' => 1,
        ]);
    }

    public function test_super_admin_can_edit_and_change_account_status(): void
    {
        $admin = $this->makeUser(2);
        $account = $this->makeUser(13, 10);

        $this->actingAs($admin)
            ->post(route('admin.circular-admin.update', $account->id), [
                'email_address' => 'updated-recovery@example.test',
                'office_id' => 20,
                'is_active' => 0,
                'password' => '',
                'confirm_password' => '',
            ])
            ->assertRedirect(route('admin.circular-admin.index'));

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'username' => 'updated-recovery@example.test',
            'email_address' => 'updated-recovery@example.test',
            'office_id' => 20,
            'is_active' => 0,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.circular-admin.status', $account->id))
            ->assertRedirect(route('admin.circular-admin.index'));

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'is_active' => 1,
        ]);
    }

    public function test_office_admin_sees_only_circular_admin_accounts_from_own_office(): void
    {
        $admin = $this->makeUser(12, 10);
        $ownAccount = $this->makeUser(13, 10);
        $otherAccount = $this->makeUser(13, 20);

        $response = $this->actingAs($admin)
            ->get(route('admin.circular-admin.index'));

        $response->assertOk();
        $response->assertSee($ownAccount->username);
        $response->assertDontSee($otherAccount->username);
    }


    public function test_circular_admin_rejects_duplicate_account_email(): void
    {
        $admin = $this->makeUser(2);
        $this->makeUser(13, 10);

        $response = $this->actingAs($admin)
            ->from(route('admin.circular-admin.create'))
            ->post(route('admin.circular-admin.store'), [
                'username' => 'new.circular@example.test',
                'email_address' => User::where('user_type_id', 13)->value('email_address'),
                'password' => 'Password123!',
                'confirm_password' => 'Password123!',
                'office_id' => 10,
            ]);

        $response->assertRedirect(route('admin.circular-admin.create'));
        $response->assertSessionHasErrors('email_address');

        $this->assertDatabaseMissing('users', [
            'username' => 'new.circular@example.test',
        ]);
    }

    public function test_circular_admin_cannot_access_circular_admin_account_management(): void
    {
        $admin = $this->makeUser(13, 10);

        $this->actingAs($admin)
            ->get(route('admin.circular-admin.index'))
            ->assertForbidden();
    }
}
