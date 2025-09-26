<?php
require_once __DIR__ . '/../Modelo/ProductosService_Admin.php';

class ProductosController_admin {
    private $service;

    public function __construct() {
        $this->service = new ProductosService_Admin();
    }

    public function manejarPeticion() {
        $mensaje = "";
        $accion = $_POST["accion"] ?? null;

        if ($accion === "crear") {
            $nombre = $_POST["nombre"] ?? '';
            $precio = $_POST["precio"] ?? 0;
            $resultado = $this->service->crear([
                "nombreProducto" => $nombre,
                "precio" => $precio
            ]);
            $mensaje = $resultado["success"] ? "✔ Administrador creado" : "❌ Error";
        }

        if ($accion === "actualizar") {
            $id = $_POST["id"] ?? 0;
            $nombre = $_POST["nombre"] ?? '';
            $resultado = $this->service->actualizar($id, ["nombreProducto" => $nombre]);
            $mensaje = $resultado["success"] ? "✔ Administrador actualizado" : "❌ Error";
        }

        if ($accion === "eliminar") {
            $id = $_POST["id"] ?? 0;
            $resultado = $this->service->eliminar($id);
            $mensaje = $resultado["success"] ? "✔ Administrador eliminado" : "❌ Error";
        }

        $administradores = $this->service->obtener();
        require __DIR__ . '/../Vistas/ProductosIndex_admin.php';
    }
}
