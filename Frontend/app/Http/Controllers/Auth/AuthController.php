<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        Log::info('=== LOGIN REQUEST ===');
        Log::info('Email: ' . $request->email);

        $result = $this->authService->login($request->email, $request->password);

        Log::info('=== RESULTADO DEL AUTHSERVICE ===');
        Log::info('Success: ' . ($result['success'] ? 'true' : 'false'));
        Log::info('Token presente: ' . (isset($result['token']) ? 'SI' : 'NO'));
        Log::info('Usuario presente: ' . (isset($result['usuario']) ? 'SI' : 'NO'));
        if (isset($result['usuario'])) {
            Log::info('Usuario data: ' . json_encode($result['usuario']));
        }

      if ($result['success']) {
    $response = [
        'success' => true,
        'token' => $result['token'],
        'usuario' => $result['usuario'],
        'mensaje' => $result['mensaje']
    ];

    Log::info('=== RESPUESTA ENVIADA AL FRONTEND ===');
    Log::info(json_encode($response));

    // 🔥 GUARDAR EN SESIÓN (CORREGIDO)
    session([
        'usuario' => $result['usuario'],
        'token' => $result['token'],
    ]);

    Log::info('Usuario guardado en sesión', [
        'usuario' => session('usuario')
    ]);

    return response()->json($response);
}

        return response()->json([
            'success' => false,
            'mensaje' => $result['mensaje']
        ], 401);
    }

    public function registrarCliente(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->authService->registrarCliente($request->all());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'mensaje' => $result['mensaje']
            ]);
        }

        return response()->json([
            'success' => false,
            'mensaje' => $result['mensaje']
        ], 400);
    }

    public function registrarEmpleado(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->authService->registrarEmpleado($request->all());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'mensaje' => $result['mensaje']
            ]);
        }

        return response()->json([
            'success' => false,
            'mensaje' => $result['mensaje']
        ], 400);
    }

    public function registrarAdmin(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->authService->registrarAdmin($request->all());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'mensaje' => $result['mensaje']
            ]);
        }

        return response()->json([
            'success' => false,
            'mensaje' => $result['mensaje']
        ], 400);
    }

    public function validarToken(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'valido' => false,
                'mensaje' => 'Token no proporcionado'
            ], 400);
        }

        $result = $this->authService->validarToken($token);

        return response()->json($result);
    }
}
