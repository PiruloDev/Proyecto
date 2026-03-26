<?php

namespace App\Services\Pedidos;

use Illuminate\Support\Facades\Http;
use Exception;

class PedidosApiService
{
    protected $baseUrl;
    protected $productosUrl;
    protected $detallesUrl;

    public function __construct()
    {
        // Usamos la variable de entorno, si no existe, usa la IP de tu instancia
        $apiHost = env('API_BASE_URL', 'http://32.193.167.191:8080');
        
        $this->baseUrl = "{$apiHost}/pedidos";
        $this->productosUrl = "{$apiHost}/productos";
        $this->detallesUrl = "{$apiHost}/detalles-pedidos";
    }

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
            \Log::info("Enviando actualización a API Java para Pedido #{$id}:", $data);
            $response = Http::put("{$this->baseUrl}/{$id}", $data);

            if ($response->failed()) {
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
       CHECKOUT
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

    public function obtenerTodosLosDetalles()
    {
        try {
            $data = Http::get($this->detallesUrl)->throw()->json();
            return $data;
        } catch (Exception $e) {
            return [];
        }
    }

    /* =========================
       CATÁLOGOS (CORREGIDOS)
       ========================= */

    public function obtenerEstados()
    {
        $apiHost = env('API_BASE_URL', 'http://32.193.167.191:8080');
        try {
            return Http::get("{$apiHost}/estadosPedidos")->throw()->json();
        } catch (Exception $e) {
            return [];
        }
    }

    public function obtenerClientes()
    {
        $apiHost = env('API_BASE_URL', 'http://32.193.167.191:8080');
        try {
            return Http::get("{$apiHost}/detalle/cliente")->throw()->json();
        } catch (Exception $e) {
            return [];
        }
    }

    public function obtenerEmpleados()
    {
        $apiHost = env('API_BASE_URL', 'http://32.193.167.191:8080');
        try {
            return Http::get("{$apiHost}/detalle/empleado")->throw()->json();
        } catch (Exception $e) {
            return [];
        }
    }
}