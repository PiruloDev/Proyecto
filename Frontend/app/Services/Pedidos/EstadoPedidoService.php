<?php

namespace App\Services\Pedidos;

use Illuminate\Support\Facades\Http;
use Exception;

class EstadoPedidoService
{
    protected $baseUrl;

    public function __construct()
    {
        // Usamos la IP elástica configurada en el .env, o la IP directa si no está definida
        $apiHost = env('API_BASE_URL', 'http://32.193.167.191:8080');
        $this->baseUrl = "{$apiHost}/estadosPedidos";
    }

    public function obtenerEstados()
    {
        try {
            $response = Http::get($this->baseUrl);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Error al obtener estados: " . $response->body());
        } catch (Exception $e) {
            throw new Exception("No se pudo conectar con la API de estados: " . $e->getMessage());
        }
    }

    /**
     * Solución de emergencia para el error 405 Method Not Allowed en Java.
     */
    public function obtenerEstadoPorId($id)
    {
        try {
            $response = Http::get($this->baseUrl);

            if ($response->successful()) {
                $todosLosEstados = $response->json();

                foreach ($todosLosEstados as $estado) {
                    if (isset($estado['id_ESTADO_PEDIDO']) && $estado['id_ESTADO_PEDIDO'] == $id) {
                        return $estado;
                    }
                }
                
                throw new Exception("Estado de pedido ID {$id} no encontrado en la lista.");
            }

            throw new Exception("Error al obtener lista de estados: " . $response->body());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function crearEstado(array $data)
    {
        try {
            $response = Http::post($this->baseUrl, $data);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Error al crear estado: " . $response->body());
        } catch (Exception $e) {
            throw new Exception("Error de conexión al crear estado: " . $e->getMessage());
        }
    }

    public function actualizarEstado($id, array $data)
    {
        try {
            $response = Http::put("{$this->baseUrl}/{$id}", $data);

            if ($response->successful()) {
                return $response->json();
            }

            throw new Exception("Error al actualizar estado: " . $response->body());
        } catch (Exception $e) {
            throw new Exception("Error de conexión al actualizar estado: " . $e->getMessage());
        }
    }

    public function eliminarEstado($id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/{$id}");

            if ($response->successful()) {
                return true;
            }

            throw new Exception("Error al eliminar estado: " . $response->body());
        } catch (Exception $e) {
            throw new Exception("Error de conexión al eliminar estado: " . $e->getMessage());
        }
    }
}