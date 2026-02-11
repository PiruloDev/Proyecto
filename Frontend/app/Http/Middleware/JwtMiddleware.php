<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;

class JwtMiddleware
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $token = $request->bearerToken();

        if (!$token) {
            // Si no hay bearer token, intentar obtenerlo de la sesión
            $token = session('token');
        }

        if (!$token) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Token no proporcionado'
            ], 401);
        }

        $validation = $this->authService->validarToken($token);

        if (!$validation['success'] || !$validation['valido']) {
            // Token inválido o en blacklist - limpiar sesión
            session()->flush();
            return response()->json([
                'success' => false,
                'mensaje' => 'Token inválido, expirado o revocado'
            ], 401);
        }

        if (!empty($roles)) {
            $userRole = $validation['usuario']['rol'] ?? null;

            if (!in_array($userRole, $roles)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No tiene permisos para acceder a este recurso'
                ], 403);
            }
        }

        $request->attributes->add(['usuario' => $validation['usuario']]);

        return $next($request);
    }
}
