<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;

class CategoriaIngredientesService
{
    protected $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base de la API desde el archivo .env
        $this->baseUrl = env('API_BASE_URL', 'http://44.195.189.38'); 
    }

    /**
     * Helper para obtener el cliente HTTP.
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function getApiClient()
    {
        return Http::baseUrl($this->baseUrl);
    }

    // =========================================================================
    // CRUD: OBTENER TODAS (GET /categorias/ingredientes)
    // =========================================================================
    
    /**
     * Obtiene el listado de categorías de ingredientes desde la API.
     * @return array Retorna un array con 'success' y 'data' o 'error'.
     */
    public function obtenerCategoriasIngredientes(): array
    {
        try {
            $response = $this->getApiClient()->get('/categorias/ingredientes'); 

            if ($response->successful()) {
                return [
                    'success' => true, 
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido al obtener categorías (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor de la API: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: CREAR (POST /nuevacategoriaingrediente)
    // =========================================================================

    /**
     * Crea una nueva categoría de ingrediente.
     * @param array $data Array con nombreCategoria.
     */
    public function crearCategoriaIngrediente(array $data): array
    {
        try {
            $payload = [
                'nombreCategoria' => $data['nombreCategoria']
            ];

            $response = $this->getApiClient()->post('/nuevacategoriaingrediente', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al crear la categoría (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar crear: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: ACTUALIZAR (PUT /categoriaingrediente/{id})
    // =========================================================================

    /**
     * Actualiza una categoría de ingrediente existente por ID.
     */
    public function actualizarCategoriaIngrediente(int $id, array $data): array
    {
        try {
            $payload = [
                'nombreCategoria' => $data['nombreCategoria']
            ];

            $response = $this->getApiClient()->put("/categoriaingrediente/{$id}", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al actualizar la categoría (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar actualizar: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: ELIMINAR (DELETE /eliminarcategoria/{id})
    // =========================================================================

    /**
     * Elimina una categoría de ingrediente por ID.
     */
    public function eliminarCategoriaIngrediente(int $id): array
    {
        try {
            $response = $this->getApiClient()->delete("/eliminarcategoria/{$id}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error al eliminar la categoría (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar eliminar: ' . $e->getMessage()
            ];
        }
    }
}