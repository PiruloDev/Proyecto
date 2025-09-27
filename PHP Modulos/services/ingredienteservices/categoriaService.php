<?php
class CategoriaService {
    private $baseUrl = "http://localhost:8080";

    // Obtener todas las categorías (GET)
    public function obtenerCategorias() {
        $url = $this->baseUrl . "/categorias/ingredientes";
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    // Crear una categoría (POST)
    public function crearCategoria($nombreCategoria) {
        $url = $this->baseUrl . "/nuevacategoriaingrediente";
        $data = ["nombreCategoria" => $nombreCategoria];
        
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json",
                "method"  => "POST",
                "content" => json_encode($data)
            ]
        ];
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    // Editar una categoría (PUT)
    public function editarCategoria($id, $nombreCategoria) {
        $url = $this->baseUrl . "/categoriaingrediente/" . $id;
        $data = ["nombreCategoria" => $nombreCategoria];
        
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json",
                "method"  => "PUT",
                "content" => json_encode($data)
            ]
        ];
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    // Eliminar una categoría (DELETE)
    public function eliminarCategoria($id) {
        $url = $this->baseUrl . "/eliminarcategoria/" . $id;

        $options = [
            "http" => [
                "method"  => "DELETE"
            ]
        ];
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }
}
