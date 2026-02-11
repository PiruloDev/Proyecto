<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;

/**
 * Middleware para proteger rutas de páginas (Blade views) que requieren autenticación JWT.
 *
 * - Verifica que exista un token válido en la sesión de Laravel.
 * - Agrega headers no-cache para prevenir que el navegador muestre páginas protegidas
 *   desde el caché después de cerrar sesión (botón "Atrás").
 * - Redirige al login si no hay sesión válida.
 */
class CheckPageAuth
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $token = session('token');
        $usuario = session('usuario');

        // Sin token o sin usuario en sesión → redirigir al login
        if (!$token || !$usuario) {
            Log::info('[CheckPageAuth] Sin sesión activa, redirigiendo al login');
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Verificar que el token siga siendo válido en el backend (blacklist check)
        $validation = $this->authService->validarToken($token);

        if (!$validation['success'] || !($validation['valido'] ?? false)) {
            Log::warning('[CheckPageAuth] Token inválido o revocado, cerrando sesión');
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Tu sesión ha expirado o fue cerrada. Inicia sesión nuevamente.');
        }

        // Verificar roles si se especificaron
        if (!empty($roles)) {
            $userRole = $usuario['rol'] ?? $usuario['tipoUsuario'] ?? null;
            if (!in_array($userRole, $roles)) {
                Log::warning('[CheckPageAuth] Acceso denegado por rol: ' . $userRole);
                return redirect()->route('login')
                    ->with('error', 'No tienes permisos para acceder a esta página.');
            }
        }

        // Procesar la request
        $response = $next($request);

        // Agregar headers anti-caché para que el navegador no muestre esta página
        // desde el caché después del logout (previene el bug del botón "Atrás")
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
