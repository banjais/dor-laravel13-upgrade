<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminRoles
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $roleId = (int) Auth::user()->user_type_id;

        // Super Admin and other existing roles retain their current rules.
        if (!in_array($roleId, [12, 13], true)) {
            return $next($request);
        }

        $route = $request->route();
        $routeName = $route ? $route->getName() : null;

        $commonAllowed = [
            'dashboard',
            'admin.logout',
            'admin.change.password',
            'admin.change.password.post',
            'admin.circulars',
            'admin.circular.view',
        ];

        if (in_array($routeName, $commonAllowed, true)) {
            return $next($request);
        }

        // Permit the unnamed /admin landing route as well.
        if (in_array(trim($request->path(), '/'), ['admin', 'admin/home'], true)) {
            return $next($request);
        }

        if ($roleId === 12) {
            // Office Admin may manage Circular Admin accounts through dedicated routes.
            if ($routeName && str_starts_with($routeName, 'admin.circular-admin.')) {
                return $next($request);
            }

            // OfficeController enforces ownership of the Office Profile.
            if (in_array($routeName, ['admin.office.edit', 'admin.office.update'], true)) {
                return $next($request);
            }

            // These modules are not granted to Role 12 in group_access_list.
            $restrictedRoutePrefixes = [
                'admin.users',
                'admin.user.',
                'admin.usertypes',
                'admin.usertype.',
                'admin.offices',
                'admin.office.',
                'office.sortorder',
                'admin.circular.',
                'admin.website-theme.',
                'admin.website-identity.',
                'admin.access.',
                'admin.module.',
            ];

            foreach ($restrictedRoutePrefixes as $prefix) {
                if ($routeName && str_starts_with($routeName, $prefix)) {
                    abort(403, 'You are not authorized to access this administrative section.');
                }
            }

            // Also protect ACL routes that do not have route names.
            $adminPath = trim($request->path(), '/');
            foreach ([
                'admin/access-control',
                'admin/access-control-user',
                'admin/acess-control',
                'admin/acess-control-user',
                'admin/module/',
            ] as $restrictedPath) {
                if ($adminPath === $restrictedPath ||
                    str_starts_with($adminPath, $restrictedPath . '/') ||
                    str_starts_with($adminPath, $restrictedPath)) {
                    abort(403, 'You are not authorized to access this administrative section.');
                }
            }

            // Preserve Role 12's existing content-management routes.
            // Only the explicitly restricted administrative modules above are denied.
            return $next($request);
        }

        // Role 13 remains limited to its explicitly allowed routes.
        abort(403, 'You are not authorized to access this administrative section.');
    }
}
