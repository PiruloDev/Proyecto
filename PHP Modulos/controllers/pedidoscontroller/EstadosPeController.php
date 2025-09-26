<?php

require_once __DIR__ . '/../Modelo/EstadosPeService.php';

class EstadosPeController {

    private $estadosPeService;

    public function __construct() {
        
        $this->estadosPeService = new EstadosPeService();
    }

    public function manejarPeticion() {
        $mensaje = "";

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $action = $method;

        if ($method === "POST" && isset($_POST['_method'])) {
            $action = strtoupper($_POST['_method']);
        }

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
              
                break;
        }
        
        $estadosPedidos = $this->estadosPeService->obtenerEstadosPedidos();
        
        require __DIR__ . '/../Vista/estados.php';
    }

    private function getPostData(array $keys) {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = isset($_POST[$key]) ? trim($_POST[$key]) : null;
        }
        return $data;
    }

    private function handleCreate() {
        $data = $this->getPostData(['nombre_ESTADO']);
        $nombreEstado = $data['nombre_ESTADO'];
        
        if (!empty($nombreEstado)) {
            $resultado = $this->estadosPeService->agregarEstadoPedido($nombreEstado);
            return $resultado["success"] 
                ? "<p style='color:green;'>Estado de pedido agregado correctamente.</p>"
                : "<p style='color:red;'>Error: " . $resultado["error"] . "</p>";
        }
        return "<p style='color:red;'>El nombre del estado no puede estar vacío.</p>";
    }

    private function handleUpdate() {
        $data = $this->getPostData(['id', 'nombre_ESTADO']);
        $id = (int)($data['id'] ?? 0);
        $nombreEstado = $data['nombre_ESTADO'];

        if (!empty($id) && !empty($nombreEstado)) {
            $resultado = $this->estadosPeService->actualizarEstadoPedido($id, $nombreEstado);
            return $resultado["success"] 
                ? "<p style='color:green;'>Estado de pedido actualizado correctamente (ID: {$id}).</p>"
                : "<p style='color:red;'>Error al actualizar (ID: {$id}): " . $resultado["error"] . "</p>";
        }
        return "<p style='color:red;'>El ID y el nombre del estado deben estar llenos para ACTUALIZAR.</p>";
    }

    private function handleDelete() {
        $data = $this->getPostData(['id']);
        $id = (int)($data['id'] ?? 0);

        if (!empty($id)) {
            $resultado = $this->estadosPeService->eliminarEstadoPedido($id);
            return $resultado["success"]
                ? "<p style='color:green;'>Estado de pedido eliminado correctamente (ID: {$id}).</p>"
                : "<p style='color:red;'>Error al eliminar (ID: {$id}): " . $resultado["error"] . "</p>";
        }
        return "<p style='color:red;'>El ID del estado de pedido debe estar presente para ELIMINAR.</p>";
    }
}