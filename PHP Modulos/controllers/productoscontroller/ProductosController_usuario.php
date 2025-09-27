<?php
require_once __DIR__ . '/../../services/productosservices/ProductosService.php';

class ProductosController_usuario {
    private $service;

    public function __construct() {
        $this->service = new ProductosService();
    }

    public function manejarPeticion() {
        $productos = $this->service->obtener();
        require __DIR__ . '/../Vistas/ProductosIndex_usuario.php';
    }
}
