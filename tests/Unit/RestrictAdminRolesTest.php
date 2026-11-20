<?php

namespace Tests\Unit;

use App\Http\Middleware\RestrictAdminRoles;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RestrictAdminRolesTest extends TestCase
{
    private function runMiddleware(int $roleId, string $routeName, ?int $officeId = 10)
    {
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn((object) [
            'user_type_id' => $roleId,
            'office_id' => $officeId,
        ]);

        $request = Request::create('/admin/test', 'GET');
        $route = new Route('GET', 'admin/test', [
            'as' => $routeName,
            'uses' => static fn () => null,
        ]);

        $request->setRouteResolver(static fn () => $route);

        return (new RestrictAdminRoles())->handle(
            $request,
            static fn () => response('allowed')
        );
    }

    public function test_office_admin_can_access_circular_admin_management(): void
    {
        $response = $this->runMiddleware(12, 'admin.circular-admin.index');

        $this->assertSame('allowed', $response->getContent());
    }

    public function test_circular_admin_cannot_access_account_management(): void
    {
        $this->expectException(HttpException::class);

        $this->runMiddleware(13, 'admin.circular-admin.index');
    }

    public function test_circular_admin_can_view_circulars(): void
    {
        $response = $this->runMiddleware(13, 'admin.circulars');

        $this->assertSame('allowed', $response->getContent());
    }

    public function test_office_admin_cannot_access_generic_user_management(): void
    {
        $this->expectException(HttpException::class);

        $this->runMiddleware(12, 'admin.users');
    }

    public function test_office_admin_can_access_office_profile(): void
    {
        $response = $this->runMiddleware(12, 'admin.office.edit');

        $this->assertSame('allowed', $response->getContent());
    }

    public function test_super_admin_retains_existing_route_access(): void
    {
        $response = $this->runMiddleware(2, 'admin.users');

        $this->assertSame('allowed', $response->getContent());
    }
}
