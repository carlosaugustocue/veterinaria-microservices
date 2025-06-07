<?php

namespace App\Http\Middleware;

use Closure;

class GatewayRoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $userRole = $request->attributes->get('user_role');
        
        if (!$userRole) {
            return response()->json([
                'success' => false,
                'message' => 'Rol de usuario no encontrado'
            ], 401);
        }

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a este recurso'
            ], 403);
        }

        return $next($request);
    }
}