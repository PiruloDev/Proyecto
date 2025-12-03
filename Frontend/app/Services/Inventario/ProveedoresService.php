<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;

class ProveedoresService
{
    protected $baseUrl = 'http://localhost:8080/proveedores';

    // ============================================================
    // OBTENER LISTA
    // ============================================================

    public function obtenerProveedores()
    {
        try {
            $response = Http::get($this->baseUrl);

            if ($response->failed()) {
                return ['success' => false, 'error' => 'Error al conectarse a la API'];
            }

            return ['success' => true, 'data' => $response->json()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ============================================================
    // CREAR
    // ============================================================

    public function agregarProveedor($data)
    {
        try {
            $response = Http::post($this->baseUrl, $data);

            if ($response->successful()) {
                return ['success' => true];
            }

            return ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ============================================================
    // ACTUALIZAR
    // ============================================================

    public function actualizarProveedor($id, $data)
    {
        try {
            $response = Http::put("{$this->baseUrl}/$id", $data);

            if ($response->successful()) {
                return ['success' => true];
            }

            return ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ============================================================
    // ELIMINAR
    // ============================================================

    public function eliminarProveedor($id)
    {
        try {
            $response = Http::delete("{$this->baseUrl}/$id");

            if ($response->successful()) {
                return ['success' => true];
            }

            return ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
