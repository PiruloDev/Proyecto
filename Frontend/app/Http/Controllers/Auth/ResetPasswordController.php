<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResetPasswordController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('API_BASE_URL', 'http://localhost:8080');
    }

    public function showRequestForm()
    {
        return view('auth.reset-password');
    }

    public function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiBaseUrl . '/reset-pass', [
                    'email' => $request->email,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'mensaje' => $data['mensaje'] ?? 'Email verificado correctamente',
                    'email' => $data['email'] ?? $request->email,
                ]);
            }

            return response()->json([
                'success' => false,
                'mensaje' => $data['mensaje'] ?? 'El correo no está registrado',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error validando email para reset: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error de conexión con el servidor',
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->patch($this->apiBaseUrl . '/change-password', [
                    'email' => $request->email,
                    'nuevaContrasena' => $request->password,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'mensaje' => $data['mensaje'] ?? 'Contraseña actualizada correctamente',
                ]);
            }

            return response()->json([
                'success' => false,
                'mensaje' => $data['mensaje'] ?? 'Error al actualizar la contraseña',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error actualizando contraseña: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error de conexión con el servidor',
            ], 500);
        }
    }
}
