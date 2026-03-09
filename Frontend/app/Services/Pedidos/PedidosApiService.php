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
        // Agregamos logging para ver qué estamos enviando exactamente a Java
        \Log::info("Enviando actualización a API Java para Pedido #{$id}:", $data);

        $response = Http::put("{$this->baseUrl}/{$id}", $data);

        if ($response->failed()) {
            // Esto imprimirá en storage/logs/laravel.log el error real de Java
            \Log::error("Error desde API Java (Pedido #{$id}): " . $response->body());
            
            $mensajeError = $response->json('message') ?? "Error servidor Java: " . $response->status();
            throw new Exception($mensajeError);
        }

        return $response->json();
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
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
        ->post($this->baseUrl, $payload) 
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
        $data = Http::get($this->detallesUrl)->throw()->json();
        \Log::info("Detalles desde API Java:", $data); // Esto te dirá exactamente cómo se llaman las llaves
        return $data;
    } catch (Exception $e) { }
}

public function obtenerTodosLosPedidos()
{
    try {
        // Usamos la baseUrl (http://localhost:8080/pedidos) que ya tienes definida
        return Http::get($this->baseUrl)->throw()->json();
    } catch (Exception $e) {
        throw new Exception("Error al obtener el listado global de pedidos de la API.");
    }
}

/* =========================
    CATÁLOGOS PARA MODALES
   ========================= */

public function obtenerEstados()
{
    try {
        return Http::get("http://localhost:8080/estadosPedidos")->throw()->json();
    } catch (Exception $e) {
        return []; // Retorna array vacío si falla
    }
}

public function obtenerClientes()
{
    try {
        return Http::get("http://localhost:8080/detalle/cliente")->throw()->json();
    } catch (Exception $e) {
        return [];
    }
}

public function obtenerEmpleados()
{
    try {
        return Http::get("http://localhost:8080/detalle/empleado")->throw()->json();
    } catch (Exception $e) {
        return [];
    }
}
}
