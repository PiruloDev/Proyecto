<?php
require_once __DIR__ . '/../../config/configProductos.php';

class ProductosService {
    public function obtener() {
        $productos = consumirGET_Productos(EndpointsProductos::LISTAR);
        return array_map(function($p) {
            return [
                'idProducto' => $p['Id Producto:'] ?? null,
                'idCategoriaProducto' => $p['Id Categoria Producto:'] ?? null,
                'nombreProducto' => $p['Nombre Producto:'] ?? null,
                'precio' => $p['Precio:'] ?? null,
                'marcaProducto' => $p['Marca Producto:'] ?? null,
                'stockMinimo' => $p['Stock Minímo:'] ?? null,
                'fechaVencimiento' => $p['Fecha Vencimiento:'] ?? null
            ];
        }, $productos);
    }

    public function crear($data) {
        return consumirAPI_Productos(EndpointsProductos::CREAR, 'POST', $data);
    }

    public function actualizar($id, $data) {
        return consumirAPI_Productos(EndpointsProductos::actualizar($id), 'PUT', $data);
    }

    public function eliminar($id) {
        return consumirAPI_Productos(EndpointsProductos::eliminar($id), 'DELETE');
    }
}
?>
