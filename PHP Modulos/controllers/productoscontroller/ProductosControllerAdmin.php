<?php
require_once __DIR__ . '/../../services/productosservices/ProductosService.php';

class ProductosControllerAdmin {
    private $service;

    public function __construct() {
        $this->service = new ProductosService();
    }

    public function obtenerProductos() {
        $productos = $this->service->obtener();
        require __DIR__ . '/../../views/productosviews/listar_productos_admin.php';
    }

    public function manejarPeticion() {
        $accion = $_GET['action'] ?? $_POST['accion'] ?? null;
        $mensaje = '';

        if ($accion === 'crear') {
            $data = [
                "nombreProducto" => $_POST["nombre"],
                "precio" => $_POST["precio"],
                "stockMinimo" => $_POST["stockMinimo"],
                "marcaProducto" => $_POST["marca"],
                "fechaVencimiento" => $_POST["fechaVencimiento"]
            ];
            $resultado = $this->service->crear($data);
            $mensaje = $resultado["success"] ? "✔ Producto creado" : "Error";
        }

        if ($accion === 'actualizar') {
            $id = $_POST["id"];
            $data = [
                "nombreProducto" => $_POST["nombre"],
                "precio" => $_POST["precio"],
                "stockMinimo" => $_POST["stockMinimo"],
                "marcaProducto" => $_POST["marca"],
                "fechaVencimiento" => $_POST["fechaVencimiento"]
            ];
            $resultado = $this->service->actualizar($id, $data);
            $mensaje = $resultado["success"] ? "✔ Producto actualizado" : "Error";
        }

        if ($accion === 'eliminar') {
            $id = $_POST["id"];
            $resultado = $this->service->eliminar($id);
            $mensaje = $resultado["success"] ? "✔ Producto eliminado" : "Error";
        }

        $productos = $this->service->obtener();
        require __DIR__ . '/../../views/productosviews/listar_productos_admin.php';
    }
}
?>
