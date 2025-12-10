<?php

namespace App\Services\Pedidos;

use Illuminate\Support\Facades\Http;
use Exception;

class EstadoPedidoService
{
    // Asumimos que tu API de Java usa el endpoint /estadosPedidos
    protected $baseUrl = 'http://localhost:8080/estadosPedidos'; 

    public function obtenerEstados()
    {
        $response = Http::get($this->baseUrl);

        if ($response->successful()) {
            return $response->json();
        }

        // Si la API devuelve un error, lanzamos una excepción
        throw new Exception("Error al obtener estados: " . $response->body());
    }

    /**
     * Solución de emergencia para el error 405 Method Not Allowed en Java.
     * En lugar de llamar a /estadosPedidos/{id} (que falla),
     * llama a /estadosPedidos (lista completa) y busca el ID en PHP.
     */
    public function obtenerEstadoPorId($id)
    {
        // Paso 1: Llamamos al endpoint que sabemos que funciona (la lista completa)
        $response = Http::get($this->baseUrl);

        if ($response->successful()) {
            $todosLosEstados = $response->json();

            // Paso 2: Buscamos el estado por ID dentro de la lista en Laravel
            foreach ($todosLosEstados as $estado) {
                // Buscamos el estado cuyo 'id_ESTADO_PEDIDO' coincide
                if (isset($estado['id_ESTADO_PEDIDO']) && $estado['id_ESTADO_PEDIDO'] == $id) {
                    return $estado; // Devolvemos solo el estado encontrado
                }
            }
            
            // Si no lo encontramos después de recorrer la lista
            throw new Exception("Estado de pedido ID {$id} no encontrado en la lista.");
        }

        // Si la llamada a la lista falló por completo
        throw new Exception("Error al obtener lista de estados: " . $response->body());
    }

    public function crearEstado(array $data)
    {
        // Enviamos los datos mapeados (NOMBRE_ESTADO)
        $response = Http::post($this->baseUrl, $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception("Error al crear estado: " . $response->body());
    }

    public function actualizarEstado($id, array $data)
    {
        // Enviamos los datos mapeados (NOMBRE_ESTADO)
        $response = Http::put("{$this->baseUrl}/{$id}", $data);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception("Error al actualizar estado: " . $response->body());
    }

    public function eliminarEstado($id)
    {
        $response = Http::delete("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            return true;
        }

        throw new Exception("Error al eliminar estado: " . $response->body());
    }
}