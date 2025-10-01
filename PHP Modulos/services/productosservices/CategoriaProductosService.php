<?php
require_once __DIR__ . '/../../config/configCategoriaProductos.php';

class CategoriaProductosService {

    public function listarCategorias() {
        return consumirGET('/categorias');  
    }

    public function crearCategoria($nombre) {
        return consumirAPI('/categorias', 'POST', [
            'nombre' => $nombre
        ]);
    }

    public function actualizarCategoria($id, $nombre) {
        return consumirAPI("/categorias/$id", 'PUT', [
            'nombre' => $nombre
        ]);
    }

    public function eliminarCategoria($id) {
        return consumirAPI("/categorias/$id", 'DELETE');
    }
}
