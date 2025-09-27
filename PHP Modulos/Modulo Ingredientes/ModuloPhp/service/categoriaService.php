<?php
class CategoriaService {
    private $baseUrl;
    private $endpoints;

    public function __construct() {
        $config = require __DIR__ . '/../config/config.php';
        $this->baseUrl = $config['BASE_URL'];
        $this->endpoints = $config['ENDPOINTS']['CATEGORIAS']; // Accedemos solo a la sección de Categorías
    }

    public function obtenerCategorias() {
        $url = $this->baseUrl . $this->endpoints['LISTAR'];
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public function crearCategoria($nombreCategoria) {
        $url = $this->baseUrl . $this->endpoints['CREAR'];
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

    public function editarCategoria($id, $nombreCategoria) {
        $url = $this->baseUrl . $this->endpoints['EDITAR'] . "/" . $id;
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

    public function eliminarCategoria($id) {
        $url = $this->baseUrl . $this->endpoints['ELIMINAR'] . "/" . $id;

        $options = [
            "http" => [
                "method"  => "DELETE"
            ]
        ];
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }
}
