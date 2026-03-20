<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProduccionService
{
    protected $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base de la API desde el archivo .env
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }

    protected function getApiClient()
    {
        return Http::baseUrl($this->baseUrl)->withHeaders([
             'Accept' => 'application/json',
             'Content-Type' => 'application/json',
        ]);
    }
    
    // =========================================================================
    // GET /inventario/produccion - Obtener Historial
    // =========================================================================
    
    public function obtenerHistorial(): array
    {
        try {
            $response = $this->getApiClient()->get('/inventario/produccion'); 

            if ($response->successful()) {
                return [
                    'success' => true, 
                    'data' => $response->json() // Array de objetos Produccion
                ];
            }

            // Manejo del 204 No Content
            if ($response->status() == 204) {
                 return ['success' => true, 'data' => []];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido al obtener historial (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor de la API: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // POST /inventario/produccion - Registrar Producción
    // =========================================================================

    public function registrarProduccion(array $data): array
    {
        try {
            // Spring espera el formato de ProduccionRequest (idProducto, cantidadProducida, etc.)
            $response = $this->getApiClient()->post('/inventario/produccion', $data);

            if ($response->successful() && $response->status() === 201) {
                return [
                    'success' => true,
                    'response' => $response->json()
                ];
            }

            // Spring devuelve un JSON con 'error' y 'status' para errores 400/500
            $errorBody = $response->json();
            $errorMessage = $errorBody['error'] ?? ('Error desconocido al registrar producción (' . $response->status() . ')');

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar registrar la producción: ' . $e->getMessage()
            ];
        }
    }
    
    // =========================================================================
    // DELETE /inventario/produccion/{id} - Eliminar Producción
    // =========================================================================
    
    public function eliminarProduccion(int $id): array
    {
        try {
            $response = $this->getApiClient()->delete("/inventario/produccion/{$id}");

            // Spring devuelve 204 No Content si es exitoso
            if ($response->successful() || $response->status() === 204) {
                return [
                    'success' => true,
                    'response' => 'Registro de producción y cambios de inventario revertidos con éxito.'
                ];
            }
            
            // Spring devuelve un JSON con error para 404/400/500
            $errorBody = $response->json();
            $errorMessage = $errorBody['error'] ?? ('Error desconocido al eliminar (' . $response->status() . ')');

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar eliminar la producción: ' . $e->getMessage()
            ];
        }
    }

    public function obtenerRecetas(): array
{
    try {
        $response = $this->getApiClient()->get('/inventario/recetas/optimizadas');

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return ['success' => false, 'error' => 'Error al obtener recetas (' . $response->status() . ')'];

    } catch (\Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
}