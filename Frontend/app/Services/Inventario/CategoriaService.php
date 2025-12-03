<?php

namespace App\Services\Inventario;

use Illuminate\Support\Facades\Http;

class CategoriaService
{
    private $base;

    public function __construct()
    {
        $this->base = env('API_SPRING_URL');
    }

    /** LISTAR TODAS */
    public function obtenerCategorias()
    {
        $response = Http::get("{$this->base}/categorias/ingredientes");

        if ($response->successful()) {
            return $response->json();
        }
        return [];
    }

    /** CREAR */
    public function crearCategoria(string $nombre)
    {
        $response = Http::post("{$this->base}/nuevacategoriaingrediente", [
            "nombreCategoria" => $nombre
        ]);

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        }
        return ["success" => false, "error" => $response->body()];
    }

    /** EDITAR */
    public function editarCategoria(int $id, string $nombre)
    {
        $response = Http::put("{$this->base}/categoriaingrediente/{$id}", [
            "nombreCategoria" => $nombre
        ]);

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        }
        return ["success" => false, "error" => $response->body()];
    }

    /** ELIMINAR */
    public function eliminarCategoria(int $id)
    {
        $response = Http::delete("{$this->base}/eliminarcategoria/{$id}");

        if ($response->successful()) {
            return ["success" => true, "response" => $response->body()];
        }
        return ["success" => false, "error" => $response->body()];
    }
}
