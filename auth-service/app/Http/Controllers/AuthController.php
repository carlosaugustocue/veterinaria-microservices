<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    /**
     * Registro de usuarios
     */
    public function register(Request $request)
    {
        // Validación
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telefono' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id'
        ]);

        // Verificar que el email no existe
        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'El correo electrónico ya está en uso'
            ], 422);
        }

        // Crear usuario
        $user = User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => $request->password,
            'telefono' => $request->telefono,
            'role_id' => $request->role_id
        ]);

        $user->load('role');

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => $user
            ]
        ], 201);
    }

    /**
     * Login de usuarios
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Verificar si está bloqueado
        if ($user->isLocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Cuenta bloqueada temporalmente. Intenta en 15 minutos.'
            ], 423);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            $user->incrementFailedAttempts();
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Verificar que esté activo
        if (!$user->active) {
            return response()->json([
                'success' => false,
                'message' => 'Cuenta inactiva'
            ], 403);
        }

        // Reset intentos fallidos
        $user->resetFailedAttempts();

        // Generar JWT
        $token = $this->generateJWT($user);

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => $user,
                'token' => $token,
                'expires_in' => env('JWT_TTL', 60) * 60 // en segundos
            ]
        ]);
    }

    /**
     * Generar JWT
     */
    private function generateJWT($user)
    {
        $payload = [
            'iss' => env('APP_URL'),
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + (env('JWT_TTL', 60) * 60),
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role->name
        ];

        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    /**
     * Obtener roles disponibles
     */
    public function roles()
    {
        $roles = Role::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
 * Obtener información del usuario autenticado
 */
public function me(Request $request)
{
    return response()->json([
        'success' => true,
        'data' => $request->user
    ]);
}

/**
 * Logout (invalidar token del lado del cliente)
 */
public function logout(Request $request)
{
    return response()->json([
        'success' => true,
        'message' => 'Logout exitoso'
    ]);
}

/**
 * Refrescar token
 */
public function refresh(Request $request)
{
    $newToken = $this->generateJWT($request->user);
    
    return response()->json([
        'success' => true,
        'data' => [
            'token' => $newToken,
            'expires_in' => env('JWT_TTL', 60) * 60
        ]
    ]);
}
}