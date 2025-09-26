<?php
require_once __DIR__ . '/../Configuracion/config.php';
require_once __DIR__ . '/../Modelo/ProductosService_Usuario.php';

class ProductosController_usuario {
    private $service;

    public function __construct() {
        $this->service = new ProductosService_Usuarios();
    }

    public function manejarPeticion() {
        $mensaje = "";

        // Crear Usuario
        if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? '') === "crear") {
            $nombre = trim($_POST["nombre"] ?? '');
            if (!empty($nombre)) {
                $resultado = $this->service->crearUsuario($nombre);
                $mensaje = $resultado["success"] 
                    ? "<p style='color:green;'>Usuario creado correctamente.</p>"
                    : "<p style='color:red;'>Error: {$resultado["error"]}</p>";
            }
        }

        // Actualizar Usuario
        if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? '') === "actualizar") {
            $id = (int)($_POST["id"] ?? 0);
            $nombre = trim($_POST["nombre"] ?? '');
            if ($id > 0 && !empty($nombre)) {
                $resultado = $this->service->actualizarUsuario($id, $nombre);
                $mensaje = $resultado["success"] 
                    ? "<p style='color:green;'>Usuario actualizado correctamente.</p>"
                    : "<p style='color:red;'>Error: {$resultado["error"]}</p>";
            }
        }

        // Eliminar Usuario
        if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["accion"] ?? '') === "eliminar") {
            $id = (int)($_POST["id"] ?? 0);
            if ($id > 0) {
                $resultado = $this->service->eliminarUsuario($id);
                $mensaje = $resultado["success"] 
                    ? "<p style='color:green;'>Usuario eliminado correctamente.</p>"
                    : "<p style='color:red;'>Error: {$resultado["error"]}</p>";
            }
        }

        // Consultar Usuarios
        $usuarios = $this->service->obtenerUsuarios();

        // Llamar la vista
        require __DIR__ . '/../Vista/usuarios.php';
    }
}
