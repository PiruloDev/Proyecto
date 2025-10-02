<?php
require_once __DIR__ . '/../../services/productosservices/ProductosService.php';

class ProductosControllerUsuario {
    private $service;

    public function __construct() {
        $this->service = new ProductosService();
    }

    public function obtenerProductos() {
        return $this->service->obtener();
    }

    public function obtenerProductoPorId($id) {
        return $this->service->obtenerPorId($id);
    }
}
