<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || ! $user->role || ! in_array($user->role->name, $roles, true)) {
            abort(403, 'You are not authorized for this action.');
        }

        return $next($request);
    }
}
