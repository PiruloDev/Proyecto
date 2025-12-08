<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://localhost:8080');
    }

    public function get($endpoint)
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status()
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al obtener datos de la API',
                'status' => $response->status(),
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('API GET Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión con la API: ' . $e->getMessage()
            ];
        }
    }

    public function post($endpoint, $data)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                    'message' => 'Operación exitosa'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al crear el registro',
                'status' => $response->status(),
                'error' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('API POST Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión con la API: ' . $e->getMessage()
            ];
        }
    }

    public function patch($endpoint, $data)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->patch($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status' => $response->status(),
                    'message' => 'Actualización exitosa'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al actualizar el registro',
                'status' => $response->status(),
                'error' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error('API PATCH Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión con la API: ' . $e->getMessage()
            ];
        }
    }

    public function delete($endpoint)
    {
        try {
            $response = Http::timeout(10)->delete($this->baseUrl . $endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Registro eliminado exitosamente',
                    'status' => $response->status()
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al eliminar el registro',
                'status' => $response->status(),
                'error' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('API DELETE Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión con la API: ' . $e->getMessage()
            ];
        }
    }
}
