<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;

class IngredientesService
{
    protected $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base de la API desde el archivo .env
        $this->baseUrl = env('API_BASE_URL', 'http://localhost:8080'); 
    }

    /**
     * Helper para obtener el cliente HTTP. (Sin token de autorización).
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function getApiClient()
    {
        // Se elimina la lógica del token JWT.
        // Crea un cliente HTTP con la configuración base.
        return Http::baseUrl($this->baseUrl);
    }
    
    /**
     * Helper privado para filtrar y asegurar que solo se envían los 4 campos principales.
     */
    private function filterIngredienteData(array $data): array
    {
        $allowedKeys = [
            'idProveedor', 
            'idCategoria', 
            'nombreIngrediente', 
            'referenciaIngrediente'
        ];
        
        // Retorna un array que solo contiene las claves especificadas
        return array_intersect_key($data, array_flip($allowedKeys));
    }


    // =========================================================================
    // CRUD: OBTENER TODOS (GET /ingredientes/lista)
    // =========================================================================
    
    /**
     * Obtiene el listado de ingredientes (DTO optimizado) desde la API.
     * @return array Retorna un array con 'success' y 'data' o 'error'.
     */
    public function obtenerIngredientes(): array
    {
        try {
            // Consume el endpoint optimizado
            $response = $this->getApiClient()->get('/ingredientes/lista'); 

            if ($response->successful()) {
                return [
                    'success' => true, 
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido al obtener ingredientes (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor de la API: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: CREAR (POST /crearingrediente)
    // =========================================================================

    /**
     * Agrega un nuevo ingrediente, enviando solo los 4 campos básicos.
     * @param array $data Array con idProveedor, idCategoria, nombreIngrediente, referenciaIngrediente.
     */
    public function agregarIngredientes(array $data): array
    {
        try {
            // 🎯 Usa el helper para asegurar que solo se envían los 4 campos al backend
            $payload = $this->filterIngredienteData($data); 

            $response = $this->getApiClient()->post('/crearingrediente', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al guardar el ingrediente (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar crear: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: ACTUALIZAR (PUT /ingrediente/{id})
    // =========================================================================

    /**
     * Actualiza un ingrediente existente por ID con solo los 4 campos básicos.
     */
    public function actualizarIngrediente(int $id, array $data): array
    {
        try {
            // 🎯 Usa el helper para asegurar que solo se envían los 4 campos al backend
            $payload = $this->filterIngredienteData($data); 

            $response = $this->getApiClient()->put("/ingrediente/{$id}", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al actualizar el ingrediente (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar actualizar: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: ELIMINAR (DELETE /ingrediente/{id})
    // =========================================================================

    /**
     * Elimina un ingrediente por ID.
     */
    public function eliminarIngrediente(int $id): array
    {
        try {
            $response = $this->getApiClient()->delete("/ingrediente/{$id}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al eliminar el ingrediente (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar eliminar: ' . $e->getMessage()
            ];
        }
    }
    
    // =========================================================================
    // ACTUALIZACIÓN PARCIAL DE CANTIDAD (PATCH /{id}/cantidad) - Se mantiene para stock
    // =========================================================================

    /**
     * Actualiza solo la cantidad de un ingrediente.
     */
    public function actualizarCantidadIngrediente(int $id, array $data): array
    {
        try {
            // Nos aseguramos de enviar solo la clave 'cantidadIngrediente'
            $payload = array_intersect_key($data, array_flip(['cantidadIngrediente']));

            $response = $this->getApiClient()->patch("/{$id}/cantidad", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al actualizar la cantidad (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar actualizar la cantidad: ' . $e->getMessage()
            ];
        }
    }
}