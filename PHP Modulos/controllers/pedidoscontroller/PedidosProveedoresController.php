<?php

require_once __DIR__ . '/../Modelo/PedidosProveedoresService.php';

class PedidosProveedoresController {

    private $pedidosProveedoresService;

    // Constructor de la clase
    public function __construct() {
        // Creamos una nueva instancia de PedidosProveedoresService y la guardamos en la propiedad
        $this->pedidosProveedoresService = new PedidosProveedoresService();
    }

    public function manejarPeticion() {
        $mensaje = "";

        // Obtener el método de la petición HTTP
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $action = $method;

        // Simular los métodos PUT y DELETE a través de un campo oculto
        if ($method === "POST" && isset($_POST['_method'])) {
            $action = strtoupper($_POST['_method']);
        }

        // Manejar la petición según el método HTTP
        switch ($action) {
            case "POST":
                $mensaje = $this->handleCreate();
                break;

            case "PUT":
                $mensaje = $this->handleUpdate();
                break;

            case "DELETE":
                $mensaje = $this->handleDelete();
                break;

            case "GET":
            default:
                // No hay acción específica, solo se carga la vista
                break;
        }

        // Obtener la lista de pedidos de proveedores para mostrar en la vista
        $pedidosProveedores = $this->pedidosProveedoresService->obtenerPedidosProveedores();

        // Cargar la vista con los datos y el mensaje
        require __DIR__ . '/../Vista/pedidos_proveedores.php';
    }

    /**
     * Lógica para manejar la creación de un nuevo pedido de proveedor (POST).
     */
    private function handleCreate() {
        $id_PROVEEDOR = isset($_POST['id_PROVEEDOR']) ? (int)trim($_POST['id_PROVEEDOR']) : null;
        $numero_PEDIDO = isset($_POST['numero_PEDIDO']) ? (int)trim($_POST['numero_PEDIDO']) : null;
        $estado_PEDIDO = isset($_POST['estado_PEDIDO']) ? trim($_POST['estado_PEDIDO']) : null;
        $fecha_PEDIDO = date("c");

        if (!empty($id_PROVEEDOR) && !empty($numero_PEDIDO) && !empty($estado_PEDIDO)) {
            $resultado = $this->pedidosProveedoresService->agregarPedidoProveedor($id_PROVEEDOR, $numero_PEDIDO, $fecha_PEDIDO, $estado_PEDIDO);

            return $resultado["success"]
                ? "<p style='color:green;'>Pedido a proveedor agregado correctamente.</p>"
                : "<p style='color:red;'>Error: " . ($resultado["error"] ?? "Error desconocido") . "</p>";
        }

        return "<p style='color:red;'>Todos los campos del pedido deben estar llenos para CREAR.</p>";
    }

    /**
     * Lógica para manejar la actualización de un pedido de proveedor (PUT).
     */
    private function handleUpdate() {
        $id_PEDIDO = isset($_POST['id_PEDIDO']) ? (int)trim($_POST['id_PEDIDO']) : null;
        $id_PROVEEDOR = isset($_POST['id_PROVEEDOR']) ? (int)trim($_POST['id_PROVEEDOR']) : null;
        $numero_PEDIDO = isset($_POST['numero_PEDIDO']) ? (int)trim($_POST['numero_PEDIDO']) : null;
        $estado_PEDIDO = isset($_POST['estado_PEDIDO']) ? trim($_POST['estado_PEDIDO']) : null;
        $fecha_PEDIDO = date("c"); // Se puede actualizar la fecha o mantener la original

        if (!empty($id_PEDIDO) && !empty($id_PROVEEDOR) && !empty($numero_PEDIDO) && !empty($estado_PEDIDO)) {
            $datos_pedido = [
                "id_PROVEEDOR" => $id_PROVEEDOR,
                "numero_PEDIDO" => $numero_PEDIDO,
                "fecha_PEDIDO" => $fecha_PEDIDO,
                "estado_PEDIDO" => $estado_PEDIDO
            ];

            $resultado = $this->pedidosProveedoresService->actualizarPedidoProveedor($id_PEDIDO, $datos_pedido);

            return $resultado["success"]
                ? "<p style='color:green;'>Pedido a proveedor (ID: {$id_PEDIDO}) actualizado correctamente.</p>"
                : "<p style='color:red;'>Error al actualizar (ID: {$id_PEDIDO}): " . ($resultado["error"] ?? "Error desconocido") . "</p>";
        }

        return "<p style='color:red;'>Todos los campos y el ID del pedido deben estar llenos para ACTUALIZAR.</p>";
    }

    /**
     * Lógica para manejar la eliminación de un pedido de proveedor (DELETE).
     */
    private function handleDelete() {
        $id_PEDIDO = isset($_POST['id_PEDIDO']) ? (int)trim($_POST['id_PEDIDO']) : null;

        if (!empty($id_PEDIDO)) {
            $resultado = $this->pedidosProveedoresService->eliminarPedidoProveedor($id_PEDIDO);

            return $resultado["success"]
                ? "<p style='color:green;'>Pedido a proveedor (ID: {$id_PEDIDO}) eliminado correctamente.</p>"
                : "<p style='color:red;'>Error al eliminar (ID: {$id_PEDIDO}): " . ($resultado["error"] ?? "Error desconocido") . "</p>";
        }

        return "<p style='color:red;'>El ID del pedido debe estar presente para ELIMINAR.</p>";
    }
}