<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    private const KNOWN_ROLES = ['admin', 'teacher', 'student'];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userRole = auth()->user()->role;

        if (!in_array($userRole, self::KNOWN_ROLES, true) || !in_array($userRole, $roles, true)) {
            abort(403, 'У вас нет доступа к этой странице.');
        }

        return $next($request);
    }
}
