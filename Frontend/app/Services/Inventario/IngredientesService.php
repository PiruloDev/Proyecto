<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * IngredientesService se encarga de la lógica de negocio 
 * y de comunicarse con la API de Spring Boot (el backend).
 */
class IngredientesService
{
    protected $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base de la API desde el archivo .env
        // Nota: Asegúrate de que API_BASE_URL esté definido en .env
        $this->baseUrl = env('API_BASE_URL', 'http://localhost:8080'); 
    }

    /**
     * Helper para obtener el cliente HTTP con el token de autorización.
     * Asume que el token JWT está guardado en la sesión de Laravel.
     * * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function getApiClient()
    {
        // Se asume que el token JWT es guardado en la sesión después del login.
        // Adaptar si el token se guarda en otro lugar (ej. base de datos, caché).
        $token = Session::get('jwt_token'); 

        // Crea un cliente HTTP con la configuración base
        $client = Http::baseUrl($this->baseUrl);

        // Si existe un token, lo añade al encabezado Authorization
        if ($token) {
            $client = $client->withToken($token);
        }

        return $client;
    }

    // =========================================================================
    // CRUD: OBTENER TODOS
    // =========================================================================
    
    /**
     * Obtiene el listado de ingredientes desde la API.
     * @return array Retorna un array con 'success' y 'data' o 'error'.
     */
    public function obtenerIngredientes(): array
    {
        try {
            // Realiza la petición GET
            $response = $this->getApiClient()->get('/ingredientes');

            if ($response->successful()) {
                // Si la respuesta es 200 OK, retorna los datos.
                return [
                    'success' => true, 
                    'data' => $response->json()
                ];
            }

            // Manejo de errores de la API (ej. 401 Unauthorized, 404 Not Found, 500 Server Error)
            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido al obtener ingredientes (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            // Manejo de excepciones de conexión (ej. API no está levantada)
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor de la API: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: CREAR
    // =========================================================================

    /**
     * Agrega un nuevo ingrediente a través de la API.
     * @param array $data Datos del ingrediente a crear.
     * @return array Retorna un array con 'success' y 'response' o 'error'.
     */
    public function agregarIngredientes(array $data): array
    {
        try {
            $response = $this->getApiClient()->post('/ingredientes', $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body() // O el JSON retornado si es el ID
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
    // CRUD: ACTUALIZAR
    // =========================================================================

    /**
     * Actualiza un ingrediente existente por ID.
     * @param int $id ID del ingrediente.
     * @param array $data Datos a actualizar.
     * @return array Retorna un array con 'success' y 'response' o 'error'.
     */
    public function actualizarIngrediente(int $id, array $data): array
    {
        try {
            // Se usa PUT para actualizar el recurso completo
            $response = $this->getApiClient()->put("/ingredientes/{$id}", $data);

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

    /**
     * Actualiza solo la cantidad de un ingrediente (ejemplo de PATCH).
     * @param int $id ID del ingrediente.
     * @param array $data Array con la clave 'cantidadIngrediente'.
     * @return array Retorna un array con 'success' y 'response' o 'error'.
     */
    public function actualizarCantidadIngrediente(int $id, array $data): array
    {
        try {
            // Se usa PATCH para una actualización parcial
            $response = $this->getApiClient()->patch("/ingredientes/cantidad/{$id}", $data);

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

    // =========================================================================
    // CRUD: ELIMINAR
    // =========================================================================

    /**
     * Elimina un ingrediente por ID.
     * @param int $id ID del ingrediente a eliminar.
     * @return array Retorna un array con 'success' y 'response' o 'error'.
     */
    public function eliminarIngrediente(int $id): array
    {
        try {
            $response = $this->getApiClient()->delete("/ingredientes/{$id}");

            // Una respuesta DELETE exitosa suele ser 200, 204 o 202
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
}