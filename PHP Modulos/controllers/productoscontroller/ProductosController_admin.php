<?php
require_once __DIR__ . '/../../services/productosservices/ProductosService.php';

class ProductosController_admin {
    private $service;

    public function __construct() {
        $this->service = new ProductosService();
    }

    public function manejarPeticion() {
        $mensaje = "";
        $accion = $_POST["accion"] ?? null;

        if ($accion === "crear") {
            $data = [
                "nombreProducto" => $_POST["nombre"],
                "precio" => $_POST["precio"],
                "stockMinimo" => $_POST["stockMinimo"],
                "marcaProducto" => $_POST["marca"],
                "fechaVencimiento" => $_POST["fechaVencimiento"]
            ];
            $resultado = $this->service->crear($data);
            $mensaje = $resultado["success"] ? "✔ Producto creado" : "❌ Error";
        }

        if ($accion === "actualizar") {
            $id = $_POST["id"];
            $data = [
                "nombreProducto" => $_POST["nombre"],
                "precio" => $_POST["precio"],
                "stockMinimo" => $_POST["stockMinimo"],
                "marcaProducto" => $_POST["marca"],
                "fechaVencimiento" => $_POST["fechaVencimiento"]
            ];
            $resultado = $this->service->actualizar($id, $data);
            $mensaje = $resultado["success"] ? "✔ Producto actualizado" : "❌ Error";
        }

        if ($accion === "eliminar") {
            $id = $_POST["id"];
            $resultado = $this->service->eliminar($id);
            $mensaje = $resultado["success"] ? "✔ Producto eliminado" : "❌ Error";
        }

        $productos = $this->service->obtener();

        require __DIR__ . '/../Vistas/ProductosIndex_admin.php';
    }
}
