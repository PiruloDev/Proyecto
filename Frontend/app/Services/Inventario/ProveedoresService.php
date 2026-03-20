<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProveedoresService
{
    protected $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base de la API desde el archivo .env
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }

    /**
     * Helper para obtener el cliente HTTP.
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function getApiClient()
    {
        return Http::timeout(10)
                   ->acceptJson()
                   ->baseUrl($this->baseUrl);
    }

    /**
     * Helper privado para filtrar y asegurar que solo se envían los campos necesarios.
     */
    private function filterProveedorData(array $data): array
    {
        $allowedKeys = [
            'nombreProv', 
            'telefonoProv', 
            'activoProv', 
            'emailProv', 
            'direccionProv'
        ];
        
        $filtered = array_intersect_key($data, array_flip($allowedKeys));
        
        // Convertir activoProv a boolean si viene como string
        if (isset($filtered['activoProv'])) {
            $filtered['activoProv'] = filter_var($filtered['activoProv'], FILTER_VALIDATE_BOOLEAN);
        }
        
        // Permitir null en direccionProv si está vacío
        if (isset($filtered['direccionProv']) && empty(trim($filtered['direccionProv']))) {
            $filtered['direccionProv'] = null;
        }
        
        return $filtered;
    }

    // =========================================================================
    // CRUD: OBTENER TODOS (GET /proveedores)
    // =========================================================================
    
    /**
     * Obtiene el listado de proveedores desde la API.
     * @return array Retorna un array con 'success' y 'data' o 'error'.
     */
    public function obtenerProveedores(): array
    {
        try {
            $response = $this->getApiClient()->get('/proveedores'); 

            if ($response->successful()) {
                return [
                    'success' => true, 
                    'data' => $response->json()
                ];
            }

            // Log del error para debugging
            Log::error('Error al obtener proveedores', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Error al obtener proveedores (Código: ' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al obtener proveedores', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: CREAR (POST /proveedores)
    // =========================================================================

    /**
     * Crea un nuevo proveedor.
     * @param array $data Array con los datos del proveedor.
     */
    public function crearProveedor(array $data): array
    {
        try {
            $payload = $this->filterProveedorData($data);

            Log::info('Enviando datos a API para crear proveedor', ['payload' => $payload]);

            $response = $this->getApiClient()->post('/proveedores', $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            // Log detallado del error
            Log::error('Error al crear proveedor', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'error' => 'Error al crear el proveedor (Código: ' . $response->status() . '). Revisa los logs para más detalles.'
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al crear proveedor', [
                'error' => $e->getMessage(),
                'payload' => $data
            ]);
            
            return [
                'success' => false,
                'error' => 'Fallo de conexión: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: ACTUALIZAR (PUT /proveedores/{id})
    // =========================================================================

    /**
     * Actualiza un proveedor existente por ID.
     */
    public function actualizarProveedor(int $id, array $data): array
    {
        try {
            $payload = $this->filterProveedorData($data);

            Log::info('Enviando datos a API para actualizar proveedor', [
                'id' => $id,
                'payload' => $payload
            ]);

            $response = $this->getApiClient()->put("/proveedores/{$id}", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            // Log detallado del error
            Log::error('Error al actualizar proveedor', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'error' => 'Error al actualizar el proveedor (Código: ' . $response->status() . '). Revisa los logs.'
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al actualizar proveedor', [
                'id' => $id,
                'error' => $e->getMessage(),
                'payload' => $data
            ]);
            
            return [
                'success' => false,
                'error' => 'Fallo de conexión: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: ELIMINAR (DELETE /proveedores/{id})
    // =========================================================================

    /**
     * Elimina un proveedor por ID.
     */
    public function eliminarProveedor(int $id): array
    {
        try {
            Log::info('Intentando eliminar proveedor', ['id' => $id]);

            $response = $this->getApiClient()->delete("/proveedores/{$id}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            // Log detallado del error
            Log::error('Error al eliminar proveedor', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'Error al eliminar el proveedor (Código: ' . $response->status() . '). Revisa los logs.'
            ];

        } catch (\Exception $e) {
            Log::error('Excepción al eliminar proveedor', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => 'Fallo de conexión: ' . $e->getMessage()
            ];
        }
    }
}