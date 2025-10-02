<?php
require_once __DIR__ . '/../../services/productosservices/ProductosService.php';

class ProductosControllerAdmin {
    private $service;

    public function __construct() {
        $this->service = new ProductosService();
    }

    // Devuelve array de productos
    public function obtenerProductos() {
        $productos = $this->service->obtener();
        if (!is_array($productos)) return [];
        return $productos;
    }

    // Maneja POST para crear/actualizar/eliminar y devuelve un mensaje string (HTML with alert)
    public function manejarPeticion() {
        $accion = $_POST['accion'] ?? null;
        $mensaje = '';

        if ($accion === 'crear') {
            $data = [
                "ID_CATEGORIA_PRODUCTO" => $_POST["categoriaId"] ?? null,
                "NOMBRE_PRODUCTO" => $_POST["nombre"] ?? '',
                "DESCRIPCION_PRODUCTO" => $_POST["descripcion"] ?? '',
                "PRODUCTO_STOCK_MIN" => $_POST["stockMinimo"] ?? 0,
                "PRECIO_PRODUCTO" => $_POST["precio"] ?? 0,
                "FECHA_VENCIMIENTO_PRODUCTO" => $_POST["fechaVencimiento"] ?? null,
                "TIPO_PRODUCTO_MARCA" => $_POST["marca"] ?? '',
                "ACTIVO" => isset($_POST['activo']) ? 1 : 0
            ];
            $resultado = $this->service->crear($data);
            $mensaje = (isset($resultado['success']) && $resultado['success']) ? "<div class='alert alert-success'>✔ Producto creado</div>" : "<div class='alert alert-danger'>Error al crear producto</div>";
        }

        if ($accion === 'actualizar') {
            $id = $_POST["id"] ?? null;
            $data = [
                "ID_CATEGORIA_PRODUCTO" => $_POST["categoriaId"] ?? null,
                "NOMBRE_PRODUCTO" => $_POST["nombre"] ?? '',
                "DESCRIPCION_PRODUCTO" => $_POST["descripcion"] ?? '',
                "PRODUCTO_STOCK_MIN" => $_POST["stockMinimo"] ?? 0,
                "PRECIO_PRODUCTO" => $_POST["precio"] ?? 0,
                "FECHA_VENCIMIENTO_PRODUCTO" => $_POST["fechaVencimiento"] ?? null,
                "TIPO_PRODUCTO_MARCA" => $_POST["marca"] ?? '',
                "ACTIVO" => isset($_POST['activo']) ? 1 : 0
            ];
            $resultado = $this->service->actualizar($id, $data);
            $mensaje = (isset($resultado['success']) && $resultado['success']) ? "<div class='alert alert-success'>✔ Producto actualizado</div>" : "<div class='alert alert-danger'>Error al actualizar producto</div>";
        }

        if ($accion === 'eliminar') {
            $id = $_POST["id"] ?? null;
            $resultado = $this->service->eliminar($id);
            $mensaje = (isset($resultado['success']) && $resultado['success']) ? "<div class='alert alert-success'>✔ Producto eliminado</div>" : "<div class='alert alert-danger'>Error al eliminar producto</div>";
        }

        return $mensaje;
    }
}
