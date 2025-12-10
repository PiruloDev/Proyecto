<?php

namespace App\Services\Pedidos;

use Illuminate\Support\Facades\Http;
use Exception;

class PedidosApiService
{
    protected $baseUrl = 'http://localhost:8080/pedidos';
    protected $productosUrl = 'http://localhost:8080/productos';

    // --- Métodos de Productos (Usados por CarritoController) ---

    public function obtenerProductos()
    {
        try {
            $response = Http::get($this->productosUrl);
            $response->throw(); 
            $productosResponse = $response->json();
            
            // Devuelve el array de productos (maneja estructuras de respuesta de Spring)
            if (isset($productosResponse['content']) && is_array($productosResponse['content'])) {
                return $productosResponse['content'];
            }
            return $productosResponse;
            
        } catch (Exception $e) {
            throw new Exception("Error al obtener productos de la API.");
        }
    }

    // --- Métodos de Pedidos CRUD (Usados por PedidosController) ---

    public function obtenerPedidos()
    {
        try {
            $response = Http::get($this->baseUrl);
            $response->throw(); 
            return $response->json();
        } catch (Exception $e) {
            // Este mensaje se mostrará en el Listado de Pedidos
            throw new Exception("Error al obtener el listado de pedidos de la API.");
        }
    }

    public function obtenerPedidoPorId($id)
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$id}");
            $response->throw();
            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            if ($e->response->status() === 404) {
                return null;
            }
            throw new Exception("Error al obtener pedido ID {$id} de la API.");
        }
    }
    
    public function crearPedido($data)
    {
        try {
            // Llama a POST /pedidos (para el CRUD manual)
            $response = Http::post($this->baseUrl, $data);
            $response->throw();
            return $response->json();
        } catch (Exception $e) {
            $message = $response->json('message') ?? 'Error al crear pedido en la API.';
            throw new Exception($message);
        }
    }

    public function actualizarPedido($id, $data)
    {
        try {
            $response = Http::put("{$this->baseUrl}/{$id}", $data);
            $response->throw();
            return $response->json();
        } catch (Exception $e) {
            throw new Exception("Error al actualizar pedido ID {$id} en la API.");
        }
    }

    public function eliminarPedido($id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/{$id}");
            $response->throw();
            return $response->json();
        } catch (Exception $e) {
            throw new Exception("Error al eliminar pedido ID {$id} en la API.");
        }
    }
    
    // --- Método de Checkout (Usado por CarritoController) ---

    /**
     * Procesa el carrito de compras a través del endpoint de la API de Java.
     * @param array $payload Debe contener 'cliente_id' y 'items'.
     */
    public function crearPedidoCheckout(array $payload)
    {
        try {
            $response = Http::post($this->baseUrl, $payload);
            $response->throw(); 
            return $response->json();
        } catch (Exception $e) {
            $message = $response->json('message') ?? 'Error desconocido en la API al finalizar el pedido.';
            throw new Exception($message);
        }
    }
}