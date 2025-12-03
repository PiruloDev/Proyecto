<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class DetallePedidoService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_SPRING_URL', 'http://localhost:8080');
    }

    private function handleResponse(Response $response, string $action): array
    {
        if ($response->successful()) {
            return [
                'success' => true,
                'data' => $response->json()
            ];
        }

        $error_detail = $response->json()['message'] ?? $response->body();
        return [
            'success' => false,
            'error' => "HTTP {$response->status()} - Error al {$action}: " . $error_detail
        ];
    }

    // GET - Obtener todos los detalles de pedidos
    public function obtenerDetalles(): array|bool
    {
        $url = "{$this->baseUrl}/detalles-pedidos";
        $response = Http::get($url);
        return $response->successful() ? $response->json() : false;
    }

    // POST - Crear detalle de pedido
    public function crearDetallePedido(array $data): array
    {
        $url = "{$this->baseUrl}/detalles-pedidos";
        $response = Http::post($url, $data);
        return $this->handleResponse($response, 'crear detalle de pedido');
    }

    // PUT - Editar detalle de pedido
    public function editarDetallePedido(int $id, array $data): array
    {
        $url = "{$this->baseUrl}/detalles-pedidos/{$id}";
        $response = Http::put($url, $data);
        return $this->handleResponse($response, 'editar detalle de pedido');
    }

    // DELETE - Eliminar detalle de pedido
    public function eliminarDetallePedido(int $id): array
    {
        $url = "{$this->baseUrl}/detalles-pedidos/{$id}";
        $response = Http::delete($url);
        return $this->handleResponse($response, 'eliminar detalle de pedido');
    }
}
