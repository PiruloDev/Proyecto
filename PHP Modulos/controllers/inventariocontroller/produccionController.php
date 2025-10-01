<?php
require_once __DIR__ . '/../../services/inventarioservices/InventarioService.php';

class ProduccionController {
    private $service;

    public function __construct() {
        $this->service = new InventarioService();
    }

    public function manejarPeticion() {
        $accion = $_GET['accion'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'registrar') {
            $this->registrar();
        } else {
            $this->mostrarFormulario();
        }
    }

    public function mostrarFormulario() {
        $receta = null;
        $mensaje = null;
        $idProducto = $_GET['idProducto'] ?? null;

        if ($accion = ($_GET['accion'] ?? null) === 'verReceta' && $idProducto) {
            $resultado = $this->service->obtenerReceta($idProducto);
            if ($resultado['status'] === 200) {
                $receta = $resultado['body'];
            }
        }

        require __DIR__ . '/../../views/produccion/produccion_form.php';
    }

    private function registrar() {
        $idProducto = $_POST['idProducto'];
        $cantidad   = $_POST['cantidad'];

        // Enviar ingredientesDescontados como array vacío para cumplir con API
        $resultado = $this->service->registrarProduccion($idProducto, $cantidad, []);

        if ($resultado['status'] === 200) {
            $mensaje = $resultado['body']['mensaje'];
        } else {
            $mensaje = $resultado['body']['error'];
        }

        require __DIR__ . '/../../views/produccion/resultado.php';
    }
}
