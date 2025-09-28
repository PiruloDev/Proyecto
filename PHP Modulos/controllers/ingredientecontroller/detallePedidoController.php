<?php
require_once __DIR__ . '../../../services/ingredienteservices/detallePedidosService.php';

class DetallePedidosController {
    private $service;

    public function __construct() {
        $this->service = new DetallePedidosService();
    }

    public function manejarPeticion() {
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? 'listar';

        switch ($accion) {
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
                // CORREGIDO: Redirección usando Ingredienteindex.php
                header("Location: Ingredienteindex.php?modulo=detallePedidos&accion=listar");
                exit; 
            
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
                // CORREGIDO: Redirección usando Ingredienteindex.php
                header("Location: Ingredienteindex.php?modulo=detallePedidos&accion=listar");
                exit;

            case 'eliminar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->service->eliminarDetalle($_POST['idDetalle']);
                }
                // CORREGIDO: Redirección usando Ingredienteindex.php
                header("Location: Ingredienteindex.php?modulo=detallePedidos&accion=listar");
                exit;

            case 'listar':
            default:
                $detalles = $this->service->obtenerDetalles();
                // Se mantiene la ruta de inclusión de la vista que ya corregimos antes.
                include __DIR__ . '/../../views/ingredienteviews/detallePedidosView.php';
                break;
        }
    }
}
?>