<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PedidosProveedoresService
{
    // URL base del API de Spring Boot (cambia el puerto si es necesario)
    protected $baseUrl = 'http://localhost:8080/pedido/proveedores'; 

    /**
     * Obtener todos los pedidos de proveedores (solo encabezados)
     */
    public function obtenerTodos()
    {
        try {
            $response = Http::get($this->baseUrl);
            
            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Error al obtener pedidos de proveedores: " . $response->body());
            return null; // Devuelve null si no es exitoso
        } catch (\Exception $e) {
            Log::error("Excepción al conectar con el API: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener un pedido completo (encabezado y detalles) por ID
     */
    public function obtenerPedidoCompleto(int $id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$id}");
            
            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Error al obtener pedido completo #{$id}: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Excepción al conectar con el API: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crear un pedido de proveedor completo (encabezado y detalles)
     */
    public function crearPedidoCompleto(array $data)
    {
        try {
            // El backend espera el JSON completo
            $response = Http::post($this->baseUrl, $data);
            
            if ($response->successful() || $response->status() === 201) {
                return ['success' => true, 'data' => $response->body()];
            }

            // Manejo de errores del backend
            $error = $response->body();
            Log::error("Error al crear pedido completo: " . $error);
            return ['success' => false, 'error' => $error];
            
        } catch (\Exception $e) {
            Log::error("Excepción al crear pedido completo: " . $e->getMessage());
            return ['success' => false, 'error' => "Excepción de conexión: " . $e->getMessage()];
        }
    }
    
    /**
     * Editar el encabezado de un pedido de proveedor por ID
     */
    public function editarPedido(int $id, array $data)
    {
        try {
            // Envía el JSON completo (incluyendo detalles, aunque el backend solo actualice el encabezado)
            $response = Http::put("{$this->baseUrl}/{$id}", $data);
            
            if ($response->successful()) {
                return ['success' => true];
            }

            $error = $response->body();
            Log::error("Error al editar pedido #{$id}: " . $error);
            return ['success' => false, 'error' => $error];
            
        } catch (\Exception $e) {
            Log::error("Excepción al editar pedido: " . $e->getMessage());
            return ['success' => false, 'error' => "Excepción de conexión: " . $e->getMessage()];
        }
    }

    /**
     * Eliminar un pedido de proveedor por ID
     */
    public function eliminarPedido(int $id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/{$id}");
            
            if ($response->successful()) {
                return ['success' => true];
            }
            
            $error = $response->body();
            Log::error("Error al eliminar pedido #{$id}: " . $error);
            return ['success' => false, 'error' => $error];

        } catch (\Exception $e) {
            Log::error("Excepción al eliminar pedido: " . $e->getMessage());
            return ['success' => false, 'error' => "Excepción de conexión: " . $e->getMessage()];
        }
    }
}