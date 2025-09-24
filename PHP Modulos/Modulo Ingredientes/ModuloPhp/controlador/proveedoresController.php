<?php
require_once __DIR__ . '/../service/proveedoresService.php';

class proveedoresController {
    private $proveedoresService;
    
    public function __construct() {
        $this->proveedoresService = new proveedoresService();
    }

    public function manejarPeticion() {
        $mensaje = "";
        $accion = $_GET['accion'] ?? 'listar';

        switch($accion) {
            case 'agregar':
                $mensaje = $this->procesarAgregar();
                break;
            case 'actualizar':
                $mensaje = $this->procesarActualizar();
                break;
            case 'eliminar':
                $mensaje = $this->procesarEliminar();
                break;
            case 'listar':
            default:
                // Solo listar
                break;
        }

        // Obtener proveedores
        $proveedores = $this->proveedoresService->obtenerProveedores();

        // Cargar vista
        require __DIR__ . '/../vista/proveedoresView.php';
    }

    private function procesarAgregar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p style='color:red;'>Método no permitido.</p>";
        }

        $nombre = trim($_POST["nombreProv"] ?? '');
        $telefono = trim($_POST["telefonoProv"] ?? '');
        $activo = isset($_POST["activoProv"]) ? (bool)$_POST["activoProv"] : false;
        $email = trim($_POST["emailProv"] ?? '');
        $direccion = trim($_POST["direccionProv"] ?? '');

        if (empty($nombre) || empty($telefono)) {
            return "<p style='color:red;'>El nombre y teléfono son obligatorios.</p>";
        }

        $this->proveedoresService->crearProveedor($nombre, $telefono, $activo, $email, $direccion);
        return "<p style='color:green;'>Proveedor agregado correctamente.</p>";
    }

    private function procesarActualizar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p style='color:red;'>Método no permitido.</p>";
        }

        $id = (int)($_POST["id"] ?? 0);
        if ($id <= 0) {
            return "<p style='color:red;'>ID inválido.</p>";
        }

        $nombre = trim($_POST["nombreProv"] ?? '');
        $telefono = trim($_POST["telefonoProv"] ?? '');
        $activo = isset($_POST["activoProv"]) ? (bool)$_POST["activoProv"] : false;
        $email = trim($_POST["emailProv"] ?? '');
        $direccion = trim($_POST["direccionProv"] ?? '');

        $this->proveedoresService->actualizarProveedor($id, $nombre, $telefono, $activo, $email, $direccion);
        return "<p style='color:green;'>Proveedor actualizado correctamente.</p>";
    }

    private function procesarEliminar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "<p style='color:red;'>Método no permitido.</p>";
        }

        $id = (int)($_POST["id"] ?? 0);
        if ($id <= 0) {
            return "<p style='color:red;'>ID inválido.</p>";
        }

        $this->proveedoresService->eliminarProveedor($id);
        return "<p style='color:green;'>Proveedor eliminado correctamente.</p>";
    }
}
?>
