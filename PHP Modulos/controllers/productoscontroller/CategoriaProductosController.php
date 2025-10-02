<?php
require_once __DIR__ . '/../../services/productosservices/CategoriaProductosService.php';

class CategoriaProductosController {
    private $service;

    public function __construct() {
        $this->service = new CategoriaProductosService();
    }

    public function listarCategorias() {
        $categorias = $this->service->listarCategorias();
        require __DIR__ . '/../../views/admin/listar_categorias_admin.php';
    }

    public function crear($nombre) {
        if ($nombre) {
            $this->service->crearCategoria(['nombre' => $nombre]);
        }
        $this->listarCategorias();
    }

    public function actualizar($id, $nombre) {
        if ($id && $nombre) {
            $this->service->actualizarCategoria($id, ['nombre' => $nombre]);
        }
        $this->listarCategorias();
    }

    public function eliminar($id) {
        if ($id) {
            $this->service->eliminarCategoria($id);
        }
        $this->listarCategorias();
    }
}
?>
