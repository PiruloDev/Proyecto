<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class InventarioService
{
    private $baseUrl;

    public function __construct()
    {
        // Obtiene la URL base de tu microservicio desde el archivo .env de Laravel
        $this->baseUrl = env('API_SPRING_URL');
    }

    // ===========================================
    // MÉTODOS DE INGREDIENTES
    // ===========================================

    public function obtenerIngredientes()
    {
        $response = Http::get("{$this->baseUrl}/ingredientes/lista");

        if ($response->successful()) {
            return $response->json();
        }
        return [];
    }


    public function agregarIngredientes(array $data)
    {
        $response = Http::post("{$this->baseUrl}/crearingrediente", $data);

        // Spring devuelve un String como respuesta. 200 OK es suficiente para éxito.
        if ($response->successful() || $response->status() === 200) {
            return ["success" => true, "response" => $response->body()];
        } else {
            return ["success" => false, "error" => $response->body() ?? "HTTP {$response->status()}"];
        }
    }


    public function actualizarIngrediente(int $id, array $data)
    {
        $response = Http::put("{$this->baseUrl}/ingrediente/{$id}", $data);

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        } else {
            return ["success" => false, "error" => $response->body() ?? "HTTP {$response->status()}"];
        }
    }


    public function actualizarCantidadIngrediente(int $id, array $data)
    {
        // Usamos PATCH con la ruta raíz del Spring Boot
        $response = Http::patch("{$this->baseUrl}/{$id}/cantidad", $data);

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        } else {
            return ["success" => false, "error" => $response->body() ?? "HTTP {$response->status()}"];
        }
    }


    public function eliminarIngrediente(int $id)
    {
        $response = Http::delete("{$this->baseUrl}/ingrediente/{$id}");

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        } else {
            return ["success" => false, "error" => $response->body() ?? "HTTP {$response->status()}"];
        }
    }
}
