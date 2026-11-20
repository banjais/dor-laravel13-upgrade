<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class UserAccountEmailTest extends TestCase
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
    }

    private function makeUser(int $role, string $username, ?string $emailAddress = null): User
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = $emailAddress ?? $username;
        $user->email_address = $emailAddress ?? $username;
        $user->username = $username;
        $user->password = bcrypt('password123');
        $user->office_id = $role === 2 ? null : 10;
        $user->user_type_id = $role;
        $user->is_active = 1;
        $user->save();

        return $user;
    }

    public function test_super_admin_can_create_user_with_separate_account_email(): void
    {
        $admin = $this->makeUser(2, 'superadmin@example.test');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'usertype' => 13,
                'username' => 'login.name@example.test',
                'email_address' => 'Recovery.Email@Example.test',
                'password' => 'password123',
                'confirm_password' => 'password123',
                'office_id' => 10,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'username' => 'login.name@example.test',
            'email_address' => 'recovery.email@example.test',
            'office_id' => 10,
            'user_type_id' => 13,
        ]);
    }

    public function test_add_user_rejects_duplicate_account_email(): void
    {
        $admin = $this->makeUser(2, 'superadmin@example.test');
        $this->makeUser(13, 'existing.login@example.test', 'taken@example.test');

        $this->actingAs($admin)
            ->from(route('admin.users.add'))
            ->post(route('admin.users.store'), [
                'usertype' => 13,
                'username' => 'new.login@example.test',
                'email_address' => 'taken@example.test',
                'password' => 'password123',
                'confirm_password' => 'password123',
                'office_id' => 10,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.add'))
            ->assertSessionHasErrors('email_address');

        $this->assertDatabaseMissing('users', [
            'username' => 'new.login@example.test',
        ]);
    }

    public function test_edit_user_can_keep_own_account_email_and_change_username(): void
    {
        $admin = $this->makeUser(2, 'superadmin@example.test');
        $target = $this->makeUser(13, 'old.login@example.test', 'recovery@example.test');

        $this->actingAs($admin)
            ->post(route('admin.users.update', $target->id), [
                'usertype' => 13,
                'username' => 'new.login@example.test',
                'email_address' => 'recovery@example.test',
                'office_id' => 10,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'username' => 'new.login@example.test',
            'email_address' => 'recovery@example.test',
        ]);
    }

    public function test_edit_user_rejects_another_accounts_email(): void
    {
        $admin = $this->makeUser(2, 'superadmin@example.test');
        $target = $this->makeUser(13, 'target.login@example.test', 'target@example.test');
        $this->makeUser(13, 'other.login@example.test', 'other@example.test');

        $this->actingAs($admin)
            ->from(route('admin.users.edit', $target->id))
            ->post(route('admin.users.update', $target->id), [
                'usertype' => 13,
                'username' => 'target.login@example.test',
                'email_address' => 'other@example.test',
                'office_id' => 10,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.edit', $target->id))
            ->assertSessionHasErrors('email_address');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'email_address' => 'target@example.test',
        ]);
    }
}
