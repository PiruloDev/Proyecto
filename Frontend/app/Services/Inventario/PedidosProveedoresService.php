<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PedidosProveedoresService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://32.193.167.191:8080'); 
    }

    protected function getApiClient()
    {
        return Http::baseUrl($this->baseUrl);
    }
    
    // =========================================================================
    // CRUD: OBTENER TODOS (GET /pedido/proveedores)
    // =========================================================================
    
    public function obtenerPedidos(): array
    {
        try {
            $response = $this->getApiClient()->get('/pedido/proveedores'); 

            if ($response->successful()) {
                return [
                    'success' => true, 
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Error desconocido al obtener pedidos (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor de la API: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: CREAR (POST /pedido/proveedores)
    // El payload debe contener: idProveedor, numeroPedido, fechaPedido, estadoPedido, y una lista de detalles.
    // =========================================================================

    public function crearPedidoCompleto(array $data): array
    {
        try {
            // El API de Spring (PedidosProveedoresController.java) usa POST /pedido/proveedores
            $response = $this->getApiClient()->post('/pedido/proveedores', $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            // Si falla, el API de Spring devuelve un mensaje en el body 
            return [
                'success' => false,
                'error' => $response->body() ?? 'Error al guardar el pedido (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar crear el pedido: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: OBTENER POR ID (GET /pedido/proveedores/{id})
    // =========================================================================
    
    public function obtenerPedidoConDetalles(int $id): array
    {
        try {
            $response = $this->getApiClient()->get("/pedido/proveedores/{$id}"); 

            if ($response->successful()) {
                return [
                    'success' => true, 
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => 'Pedido no encontrado o error en la API (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'No se pudo conectar con el servidor de la API: ' . $e->getMessage()
            ];
        }
    }

    // =========================================================================
    // CRUD: ACTUALIZAR (PUT /pedido/proveedores/{id})
    // Solo actualiza el encabezado del pedido
    // =========================================================================
    
    public function actualizarPedido(int $id, array $data): array
    {
        try {
            // El API de Spring (PedidosProveedoresController.java) usa PUT /pedido/proveedores/{id}
            $response = $this->getApiClient()->put("/pedido/proveedores/{$id}", $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            // Si falla, devuelve el mensaje de error o el estado
            return [
                'success' => false,
                'error' => $response->body() ?? 'Error al actualizar el pedido (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar actualizar el pedido: ' . $e->getMessage()
            ];
        }
    }
    
    // =========================================================================
    // CRUD: ELIMINAR (DELETE /pedido/proveedores/{id})
    // =========================================================================
    
    public function eliminarPedido(int $id): array
    {
        try {
            $response = $this->getApiClient()->delete("/pedido/proveedores/{$id}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->body()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body() ?? 'Error al eliminar el pedido (' . $response->status() . ')'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Fallo de conexión al intentar eliminar el pedido: ' . $e->getMessage()
            ];
        }
    }

    public function entregarPedido(int $id): array
{
    try {
        $response = $this->getApiClient()->patch("/pedido/proveedores/{$id}/entregar");

        if ($response->successful()) {
            return ['success' => true, 'response' => $response->body()];
        }

        return [
            'success' => false,
            'error' => $response->body() ?? 'Error al entregar pedido (' . $response->status() . ')'
        ];
    } catch (\Exception $e) {
        return ['success' => false, 'error' => 'Fallo de conexión: ' . $e->getMessage()];
    }
}

}