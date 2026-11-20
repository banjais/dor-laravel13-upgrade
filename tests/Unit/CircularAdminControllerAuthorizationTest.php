<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\CircularAdminController;
use Illuminate\Support\Facades\Auth;
use ReflectionMethod;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CircularAdminControllerAuthorizationTest extends TestCase
{
    private function invokeManagerRole(object $user): int
    {
        Auth::shouldReceive('user')
            ->zeroOrMoreTimes()
            ->andReturn($user);

        $method = new ReflectionMethod(
            CircularAdminController::class,
            'managerRole'
        );

        return $method->invoke((new \ReflectionClass(CircularAdminController::class))->newInstanceWithoutConstructor());
    }

    public function test_super_admin_can_manage_circular_admin_accounts(): void
    {
        $this->assertSame(2, $this->invokeManagerRole((object) [
            'user_type_id' => 2,
            'office_id' => null,
        ]));
    }

    public function test_office_admin_with_assigned_office_can_manage_accounts(): void
    {
        $this->assertSame(12, $this->invokeManagerRole((object) [
            'user_type_id' => 12,
            'office_id' => 10,
        ]));
    }

    public function test_office_admin_without_assigned_office_is_blocked(): void
    {
        $this->expectException(HttpException::class);

        $this->invokeManagerRole((object) [
            'user_type_id' => 12,
            'office_id' => null,
        ]);
    }

    public function test_circular_admin_is_blocked_from_account_management(): void
    {
        $this->expectException(HttpException::class);

        $this->invokeManagerRole((object) [
            'user_type_id' => 13,
            'office_id' => 10,
        ]);
    }
}
