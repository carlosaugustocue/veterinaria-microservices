<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use App\Models\User;

class Authenticate
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            
            // Verificar que el usuario existe y está activo
            $user = User::with('role')->find($decoded->user_id);
            if (!$user || !$user->active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado o inactivo'
                ], 401);
            }

            // Agregar usuario al request
            $request->auth = $decoded;
            $request->user = $user;

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