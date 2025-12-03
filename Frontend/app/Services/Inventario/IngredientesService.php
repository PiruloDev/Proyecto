<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;

class IngredientesService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_SPRING_URL');  
    }

    // ===========================================
    // LISTAR INGREDIENTES
    // ===========================================

    public function obtenerIngredientes()
    {
        $response = Http::get("{$this->baseUrl}/ingredientes/lista");

        return $response->successful() ? $response->json() : [];
    }

    // ===========================================
    // CREAR INGREDIENTE
    // ===========================================

    public function agregarIngredientes(array $data)
    {
        $response = Http::post("{$this->baseUrl}/crearingrediente", $data);

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        }

        return ["success" => false, "error" => $response->body()];
    }

    // ===========================================
    // ACTUALIZAR INGREDIENTE
    // ===========================================

    public function actualizarIngrediente(int $id, array $data)
    {
        $response = Http::put("{$this->baseUrl}/ingrediente/{$id}", $data);

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        }

        return ["success" => false, "error" => $response->body()];
    }

    // ===========================================
    // ACTUALIZAR SOLO CANTIDAD
    // ===========================================

    public function actualizarCantidadIngrediente(int $id, array $data)
    {
        $response = Http::patch("{$this->baseUrl}/{$id}/cantidad", $data);

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        }

        return ["success" => false, "error" => $response->body()];
    }

    // ===========================================
    // ELIMINAR INGREDIENTE
    // ===========================================

    public function eliminarIngrediente(int $id)
    {
        $response = Http::delete("{$this->baseUrl}/ingrediente/{$id}");

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        }

        return ["success" => false, "error" => $response->body()];
    }
}
