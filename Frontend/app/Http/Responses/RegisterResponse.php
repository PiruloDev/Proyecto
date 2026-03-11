<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // After API-based registration, redirect to login instead of dashboard
        if ($request->wantsJson()) {
            return new JsonResponse([
                'success' => true,
                'mensaje' => 'Registro exitoso. Ya puedes iniciar sesión.',
            ], 201);
        }

        return redirect()->route('login')->with('success', '¡Registro exitoso! Ya puedes iniciar sesión con tus credenciales.');
    }
}
