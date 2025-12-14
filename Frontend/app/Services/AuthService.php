<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct()
    {
        // Constructor simplificado
    }

    public function login($email, $password)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(env('API_BASE_URL', 'http://localhost:8080') . '/auth/login', [
                    'username' => $email,
                    'password' => $password
                ]);

            if ($response->successful()) {
                $data = $response->json();

                $token = $data['token'] ?? null;
                $usuario = null;

                if ($token) {
                    $usuario = $this->decodeJWT($token);
                }

                return [
                    'success' => true,
                    'token' => $token,
                    'usuario' => $usuario,
                    'mensaje' => $data['mensaje'] ?? 'Login exitoso'
                ];
            }

            $error = $response->json();
            return [
                'success' => false,
                'mensaje' => $error['mensaje'] ?? 'Error al iniciar sesión',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Error en login: ' . $e->getMessage());
            return [
                'success' => false,
                'mensaje' => 'Error de conexión con el servidor'
            ];
        }
    }

    protected function decodeJWT($token)
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return null;
            }

            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
            $data = json_decode($payload, true);

            return [
                'id' => $data['userId'] ?? null,
                'userId' => $data['userId'] ?? null,
                'nombre' => $data['nombre'] ?? null,
                'email' => $data['sub'] ?? null,
                'rol' => $data['rol'] ?? null,
                'tipoUsuario' => $data['tipoUsuario'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Error decodificando JWT: ' . $e->getMessage());
            return null;
        }
    }

    public function registrarCliente($data)
    {
        return $this->registrar('/auth/registro/cliente', $data);
    }

    public function registrarEmpleado($data)
    {
        return $this->registrar('/auth/registro/empleado', $data);
    }

    public function registrarAdmin($data)
    {
        return $this->registrar('/auth/registro/admin', $data);
    }

    protected function registrar($endpoint, $data)
    {
        try {
            Log::info('=== REGISTRO REQUEST ===');
            Log::info('Endpoint: ' . env('API_BASE_URL', 'http://localhost:8080') . $endpoint);
            Log::info('Data recibida:', $data);
            
            $payload = [
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'telefono' => $data['telefono'] ?? null,
                'contrasena' => $data['password']
            ];
            
            Log::info('Payload a enviar:', $payload);
            
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(env('API_BASE_URL', 'http://localhost:8080') . $endpoint, $payload);

            Log::info('Response status: ' . $response->status());
            Log::info('Response body: ' . $response->body());

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'mensaje' => $result['mensaje'] ?? 'Registro exitoso',
                    'tipo' => $result['tipo'] ?? null
                ];
            }

            $error = $response->json();
            Log::error('Error en registro:', $error);
            
            return [
                'success' => false,
                'mensaje' => $error['mensaje'] ?? $error['error'] ?? 'Error al registrar usuario',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Exception en registro: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return [
                'success' => false,
                'mensaje' => 'Error de conexión con el servidor: ' . $e->getMessage()
            ];
        }
    }

    public function validarToken($token)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ])
                ->post(env('API_BASE_URL', 'http://localhost:8080') . '/auth/validar');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'valido' => $data['valido'] ?? false,
                    'usuario' => $data['usuario'] ?? null
                ];
            }

            return [
                'success' => false,
                'valido' => false,
                'mensaje' => 'Token inválido'
            ];
        } catch (\Exception $e) {
            Log::error('Error al validar token: ' . $e->getMessage());
            return [
                'success' => false,
                'valido' => false,
                'mensaje' => 'Error de conexión'
            ];
        }
    }
}
