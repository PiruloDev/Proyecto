<?php
require_once __DIR__ . '/../service/detallePedidosService.php';

class DetallePedidosController {
    private $service;

    public function __construct() {
        $this->service = new DetallePedidosService();
    }

    public function manejarPeticion() {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? 'listar';

        switch ($accion) {
            case 'listar':
                $detalles = $this->service->obtenerDetalles();
                include __DIR__ . '/../vista/detallePedidosView.php';
                break;

            case 'crear':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->service->agregarDetalle(
                        $_POST['idPedido'],
                        $_POST['idProducto'],
                        $_POST['cantidadProducto'],
                        $_POST['precioUnitario'],
                        $_POST['subtotal']
                    );
                }
                header("Location: index.php?modulo=detallePedidos&accion=listar");
                break;

            case 'actualizar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->service->actualizarDetalle(
                        $_POST['idDetalle'],
                        $_POST['idPedido'],
                        $_POST['idProducto'],
                        $_POST['cantidadProducto'],
                        $_POST['precioUnitario'],
                        $_POST['subtotal']
                    );
                }
                header("Location: index.php?modulo=detallePedidos&accion=listar");
                break;

            case 'eliminar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->service->eliminarDetalle($_POST['idDetalle']);
                }
                header("Location: index.php?modulo=detallePedidos&accion=listar");
                break;

            default:
                $detalles = $this->service->obtenerDetalles();
                include __DIR__ . '/../vista/detallePedidosView.php';
                break;
        }
    }
}
?>
