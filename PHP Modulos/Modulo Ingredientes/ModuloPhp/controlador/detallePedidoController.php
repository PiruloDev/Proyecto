<?php
require_once __DIR__ . '/../service/detallePedidosService.php';

class detallePedidosController {
    private $service;

    public function __construct() {
        $this->service = new DetallePedidosService();
    }

    public function manejarPeticion() {
        $accion = $_GET['accion'] ?? 'listar';

        switch ($accion) {
            case 'listar':
                $detalles = $this->service->obtenerDetalles();
                require __DIR__ . '/../vista/detallePedidosView.php';
                break;

            case 'agregar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->service->agregarDetalle(
                        $_POST['idPedido'],
                        $_POST['idProducto'],
                        $_POST['cantidadProducto'],
                        $_POST['precioUnitario'],
                        $_POST['subtotal']
                    );
                    header("Location: index.php?modulo=detallePedidos&accion=listar");
                    exit;
                }
                break;

            case 'editar':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->service->actualizarDetalle(
                        $_POST['idDetalle'],
                        $_POST['idPedido'],
                        $_POST['idProducto'],
                        $_POST['cantidadProducto'],
                        $_POST['precioUnitario'],
                        $_POST['subtotal']
                    );
                    header("Location: index.php?modulo=detallePedidos&accion=listar");
                    exit;
                }

                // Si es GET, obtener datos desde querystring
                $detalle = [
                    "idDetalle" => $_GET['id'],
                    "idPedido" => $_GET['idPedido'] ?? '',
                    "idProducto" => $_GET['idProducto'] ?? '',
                    "cantidadProducto" => $_GET['cantidadProducto'] ?? '',
                    "precioUnitario" => $_GET['precioUnitario'] ?? '',
                    "subtotal" => $_GET['subtotal'] ?? ''
                ];
                require __DIR__ . '/../vista/detallePedidosEditar.php';
                break;

            case 'eliminar':
                if (isset($_GET['id'])) {
                    $this->service->eliminarDetalle($_GET['id']);
                }
                header("Location: index.php?modulo=detallePedidos&accion=listar");
                exit;
                break;

            default:
                echo "Acción no válida.";
        }
    }
}
?>
