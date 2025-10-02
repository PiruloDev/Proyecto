<?php
require_once __DIR__ . '/../../config/configCategoriaProductos.php';

class CategoriaProductosService {
    public function listarCategorias() {
        return consumirGET_Categorias(EndpointsCategorias::LISTAR);
    }

    public function crearCategoria($data) {
        return consumirAPI_Categorias(EndpointsCategorias::CREAR, 'POST', $data);
    }

    public function actualizarCategoria($id, $data) {
        return consumirAPI_Categorias(EndpointsCategorias::actualizar($id), 'PUT', $data);
    }

    public function eliminarCategoria($id) {
        return consumirAPI_Categorias(EndpointsCategorias::eliminar($id), 'DELETE');
    }
}
?>
