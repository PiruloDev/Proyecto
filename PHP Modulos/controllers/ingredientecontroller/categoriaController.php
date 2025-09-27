<?php
require_once __DIR__ . '/../service/categoriaService.php';

class CategoriaController {
    private $service;

    public function __construct() {
        $this->service = new CategoriaService();
    }

    public function manejarPeticion($accion) {
        switch ($accion) {
            case 'listar':
                $categorias = $this->service->obtenerCategorias();
                include __DIR__ . '/../vista/categoriaView.php';
                break;

            case 'agregar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $nombre = $_POST['nombreCategoria'];
                    $mensaje = $this->service->crearCategoria($nombre);
                }
                $categorias = $this->service->obtenerCategorias();
                include __DIR__ . '/../vista/categoriaView.php';
                break;

            case 'editar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $id = $_POST['idCategoria'];
                    $nombre = $_POST['nombreCategoria'];
                    $mensaje = $this->service->editarCategoria($id, $nombre);
                }
                $categorias = $this->service->obtenerCategorias();
                include __DIR__ . '/../vista/categoriaView.php';
                break;

            case 'eliminar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $id = $_POST['idCategoria'];
                    $mensaje = $this->service->eliminarCategoria($id);
                }
                $categorias = $this->service->obtenerCategorias();
                include __DIR__ . '/../vista/categoriaView.php';
                break;

            default:
                $categorias = $this->service->obtenerCategorias();
                include __DIR__ . '/../vista/categoriaView.php';
                break;
        }
    }
}
