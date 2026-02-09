<?php

namespace App\Services\Pedidos;

use Illuminate\Support\Facades\Http;
use Exception;

class PedidosApiService
{
    protected $baseUrl = 'http://localhost:8080/pedidos';
    protected $productosUrl = 'http://localhost:8080/productos';

    /* =========================
       PRODUCTOS
       ========================= */

    public function obtenerProductos()
    {
        try {
            $response = Http::get($this->productosUrl);
            $response->throw();

            $productosResponse = $response->json();

            if (isset($productosResponse['content']) && is_array($productosResponse['content'])) {
                return $productosResponse['content'];
            }

            return $productosResponse;

        } catch (Exception $e) {
            throw new Exception("Error al obtener productos de la API.");
        }
    }

    /* =========================
       PEDIDOS CRUD
       ========================= */

    public function obtenerPedidos()
    {
        try {
            return Http::get($this->baseUrl)->throw()->json();
        } catch (Exception $e) {
            throw new Exception("Error al obtener el listado de pedidos de la API.");
        }
    }

    public function obtenerPedidoPorId($id)
    {
        try {
            return Http::get("{$this->baseUrl}/{$id}")->throw()->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            if ($e->response && $e->response->status() === 404) {
                return null;
            }
            throw new Exception("Error al obtener pedido ID {$id} de la API.");
        }
    }

    public function crearPedido($data)
    {
        try {
            return Http::post($this->baseUrl, $data)->throw()->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $message = $e->response?->json('message') ?? 'Error al crear pedido en la API.';
            throw new Exception($message);
        }
    }

    public function actualizarPedido($id, $data)
    {
        try {
            return Http::put("{$this->baseUrl}/{$id}", $data)->throw()->json();
        } catch (Exception $e) {
            throw new Exception("Error al actualizar pedido ID {$id} en la API.");
        }
    }

    public function eliminarPedido($id)
    {
        try {
            return Http::delete("{$this->baseUrl}/{$id}")->throw()->json();
        } catch (Exception $e) {
            throw new Exception("Error al eliminar pedido ID {$id} en la API.");
        }
    }

    /* =========================
       CHECKOUT (CLAVE)
       ========================= */

    public function crearPedidoCheckout(array $payload)
{
    try {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->asJson() 
        ->post($this->baseUrl, $payload) // Enviamos el payload con cliente_id, etc.
        ->throw()
        ->json();
    } catch (\Exception $e) {
        throw new Exception("Error API: " . ($e->response?->body() ?? $e->getMessage()));
    }
}
    /* =========================
       DASHBOARD CLIENTE
       ========================= */

    public function obtenerPedidosPorCliente($clienteId)
    {
        try {
            return Http::get("{$this->baseUrl}/cliente/{$clienteId}")
                ->throw()
                ->json();
        } catch (Exception $e) {
            throw new Exception("Error al obtener los pedidos del cliente {$clienteId} de la API.");
        }
    }

    protected $detallesUrl = 'http://localhost:8080/detalles-pedidos'; // URL de tu nuevo Controller en Java

public function obtenerTodosLosDetalles()
{
    try {
        return Http::get($this->detallesUrl)->throw()->json();
    } catch (Exception $e) {
        throw new Exception("Error al obtener los detalles de pedidos.");
    }
}
}
