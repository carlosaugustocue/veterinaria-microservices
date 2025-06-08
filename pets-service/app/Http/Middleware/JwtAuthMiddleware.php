<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        // Verificar si viene del Gateway con headers X-User-*
        if ($request->hasHeader('X-User-ID')) {
            $request->attributes->set('user_id', $request->header('X-User-ID'));
            $request->attributes->set('user_email', $request->header('X-User-Email'));
            $request->attributes->set('user_role', $request->header('X-User-Role'));
            return $next($request);
        }

        // Si no viene del Gateway, validar JWT directamente
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            
            $request->attributes->set('user_id', $decoded->user_id);
            $request->attributes->set('user_email', $decoded->email);
            $request->attributes->set('user_role', $decoded->role);

        } catch (ExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expirado'
            ], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar token'
            ], 401);
        }

        return $next($request);
    }
}