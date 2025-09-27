<?php
class IngredientesService {
    private $baseUrl;
    private $endpoints;

    public function __construct() {
        $config = require __DIR__ . '/../config/config.php';
        $this->baseUrl = $config['BASE_URL'];
        $this->endpoints = $config['ENDPOINTS']['INGREDIENTES'];
    }

    public function obtenerIngredientes() {
        $url = $this->baseUrl . $this->endpoints['LISTAR'];
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public function crearIngrediente($ingredienteData) {
        $url = $this->baseUrl . $this->endpoints['CREAR'];
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json",
                "method"  => "POST",
                "content" => json_encode($ingredienteData)
            ]
        ];
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    public function editarIngrediente($id, $ingredienteData) {
        $url = $this->baseUrl . $this->endpoints['EDITAR'] . "/" . $id;
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json",
                "method"  => "PUT",
                "content" => json_encode($ingredienteData)
            ]
        ];
        $context  = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    public function eliminarIngrediente($id) {
        $url = $this->baseUrl . $this->endpoints['ELIMINAR'] . "/" . $id;
        $options = [
            "http" => [
                "method"  => "DELETE"
