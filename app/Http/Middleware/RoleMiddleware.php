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

        if (!$user) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        $userRoleCodes = $user->roles()
            ->pluck('code')
            ->map(fn ($role) => strtolower(trim($role)))
            ->toArray();

        $allowedRoles = collect($roles)
            ->map(fn ($role) => strtolower(trim($role)))
            ->toArray();

        $hasRole = count(array_intersect($userRoleCodes, $allowedRoles)) > 0;

        if (!$hasRole) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a este recurso.',
                'required_roles' => $allowedRoles,
                'user_roles' => $userRoleCodes,
            ], 403);
        }

        return $next($request);
    }
}