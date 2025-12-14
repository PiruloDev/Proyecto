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
            return response()->json([
                'success' => false,
                'mensaje' => 'Token no proporcionado'
            ], 401);
        }

        $validation = $this->authService->validarToken($token);

        if (!$validation['success'] || !$validation['valido']) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Token inválido o expirado'
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
