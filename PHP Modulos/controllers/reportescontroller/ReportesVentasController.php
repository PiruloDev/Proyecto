<?php
require_once __DIR__ . '/../Modelo/ReportesVentasService.php';

class ReportesVentasController {
    private $reportesVentasService;

    public function __construct() {
        $this->reportesVentasService = new ReportesVentasService();
    }

    public function manejarPeticion() {
        $mensaje = "";

        // ----- ELIMINAR POR GET -----
        if (isset($_GET['eliminar'])) {
            $id = intval($_GET['eliminar']);
            $resultado = $this->reportesVentasService->eliminarVenta($id);

            if (!empty($resultado['success'])) {
                $mensaje = "<p style='color:green;'>Venta eliminada correctamente.</p>";
            } else {
                $error = $resultado['error'] ?? 'Error desconocido';
                $mensaje = "<p style='color:red;'>Error al eliminar venta: " . htmlspecialchars($error) . "</p>";
            }
        }

        // ----- POST, PATCH, DELETE -----
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? 'agregar';
            $id = isset($_POST['id']) ? intval($_POST['id']) : null;
            $idCliente = intval($_POST['idCliente'] ?? 0);
            $idPedido = intval($_POST['idPedido'] ?? 0);
            $fecha = trim($_POST['fecha'] ?? '');
            $hora = trim($_POST['hora'] ?? '');
            $totalFactura = floatval($_POST['totalFactura'] ?? 0);

            $fechaFacturacion = $fecha && $hora ? $fecha . "T" . $hora : null;

            if ($accion === 'agregar') {
                if ($idCliente && $idPedido && $fechaFacturacion && $totalFactura > 0) {
                    $resultado = $this->reportesVentasService->agregarVenta([
                        "idCliente" => $idCliente,
                        "idPedido" => $idPedido,
                        "fechaFacturacion" => $fechaFacturacion,
                        "totalFactura" => $totalFactura
                    ]);
                    if (!empty($resultado['success'])) {
                        $mensaje = "<p style='color:green;'>Venta agregada correctamente.</p>";
                    } else {
                        $mensaje = "<p style='color:red;'>Error al agregar venta: " . htmlspecialchars($resultado['error'] ?? 'Error desconocido') . "</p>";
                    }
                } else {
                    $mensaje = "<p style='color:red;'>Todos los campos son obligatorios para registrar la venta.</p>";
                }

            } elseif ($accion === 'actualizar') {
                if ($id) {
                    $resultado = $this->reportesVentasService->actualizarVenta($id, [
                        "idCliente" => $idCliente,
                        "idPedido" => $idPedido,
                        "fechaFacturacion" => $fechaFacturacion,
                        "totalFactura" => $totalFactura
                    ]);
                    if (!empty($resultado['success'])) {
                        $mensaje = "<p style='color:green;'>Venta actualizada correctamente.</p>";
                    } else {
                        $mensaje = "<p style='color:red;'>Error al actualizar venta: " . htmlspecialchars($resultado['error'] ?? 'Error desconocido') . "</p>";
                    }
                } else {
                    $mensaje = "<p style='color:red;'>ID requerido para actualizar.</p>";
                }

            } elseif ($accion === 'eliminar') {
                if ($id) {
                    $resultado = $this->reportesVentasService->eliminarVenta($id);
                    if (!empty($resultado['success'])) {
                        $mensaje = "<p style='color:green;'>Venta eliminada correctamente.</p>";
                    } else {
                        $mensaje = "<p style='color:red;'>Error al eliminar venta: " . htmlspecialchars($resultado['error'] ?? 'Error desconocido') . "</p>";
                    }
                } else {
                    $mensaje = "<p style='color:red;'>ID requerido para eliminar.</p>";
                }

            } else {
                $mensaje = "<p style='color:red;'>Acción no reconocida.</p>";
            }
        }

        // ----- OBTENER LISTA DE VENTAS -----
        $ventas = $this->reportesVentasService->obtenerVentas();

        // ----- CARGAR VISTA -----
        require __DIR__ . '/../Vista/ReportesVentasIndex.php';
    }
}
