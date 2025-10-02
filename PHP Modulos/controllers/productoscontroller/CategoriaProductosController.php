<?php
require_once __DIR__ . '/../../services/productosservices/CategoriaProductosService.php';

class CategoriaProductosController {
    private $service;

    public function __construct() {
        $this->service = new CategoriaProductosService();
    }

    public function listarCategorias() {
        $cats = $this->service->listarCategorias(); // <-- Método real en tu service
        if (!is_array($cats)) return [];
        return $cats;
    }

    public function manejarPeticion() {
        $accion = $_POST['accion'] ?? null;
        $mensaje = '';

        if ($accion === 'crear') {
            $nombre = $_POST['nombre'] ?? '';
            $res = $this->service->crearCategoria(['nombre' => $nombre]); // usar el método correcto
            $mensaje = $res ? "<div class='alert alert-success'>✔ Categoría creada</div>" : "<div class='alert alert-danger'>Error al crear categoría</div>";
        }

        if ($accion === 'actualizar') {
            $id = $_POST['id'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $res = $this->service->actualizarCategoria($id, ['nombre' => $nombre]);
            $mensaje = $res ? "<div class='alert alert-success'>✔ Categoría actualizada</div>" : "<div class='alert alert-danger'>Error al actualizar categoría</div>";
        }

        if ($accion === 'eliminar') {
            $id = $_POST['id'] ?? null;
            $res = $this->service->eliminarCategoria($id);
            $mensaje = $res ? "<div class='alert alert-success'>✔ Categoría eliminada</div>" : "<div class='alert alert-danger'>Error al eliminar categoría</div>";
        }

        return $mensaje;
    }
}
