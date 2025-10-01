<?php
require_once __DIR__ . '/../services/CategoriaProductosService.php';

class CategoriaProductosController {
    private $service;

    public function __construct($db) {
        $this->service = new CategoriaProductosService($db);
    }

    public function index() {
        $categorias = $this->service->listarCategorias();
        include __DIR__ . '/../views/categorias/index.php';
    }

    public function crear($nombre) {
        $this->service->crearCategoria($nombre);
        header("Location: index.php");
    }

    public function actualizar($id, $nombre) {
        $this->service->actualizarCategoria($id, $nombre);
        header("Location: index.php");
    }

    public function eliminar($id) {
        $this->service->eliminarCategoria($id);
        header("Location: index.php");
    }
}

