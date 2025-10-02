<?php

require_once __DIR__ . '/../../services/pedidoservices/PedidosService.php';

class PedidosController {

    private $pedidosService; 

    public function __construct() {
        $this->pedidosService = new PedidosService();
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
        
        $pedidos = $this->pedidosService->obtenerPedidos();

        require __DIR__ . '/../../views/pedidosviews/pedidos.php'; 
    } 
    
    private function getPostData(array $keys) {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = isset($_POST[$key]) ? trim($_POST[$key]) : null;
        }
        return $data;
    }

    private function handleCreate() {
        $data = $this->getPostData(['id_CLIENTE', 'id_EMPLEADO', 'id_ESTADO_PEDIDO', 'total_PRODUCTO']);
        
        if (!empty($data['id_CLIENTE']) && !empty($data['id_EMPLEADO']) && !empty($data['id_ESTADO_PEDIDO']) && !empty($data['total_PRODUCTO'])) {
            
            $data['id_CLIENTE'] = (int)$data['id_CLIENTE'];
            $data['id_EMPLEADO'] = (int)$data['id_EMPLEADO'];
            $data['id_ESTADO_PEDIDO'] = (int)$data['id_ESTADO_PEDIDO'];
            $data['total_PRODUCTO'] = (float)$data['total_PRODUCTO'];
            
            // ✅ CORRECCIÓN: Solo se registra la fecha de INGRESO al crear.
            $data['fecha_INGRESO'] = date("c"); 
            // ❌ ELIMINADO: $data['fecha_ENTREGA'] = date("c"); 

            $resultado = $this->pedidosService->agregarPedido($data);

            return $resultado["success"] 
                ? "<p style='color:green;'> Pedido agregado correctamente.</p>"
                : "<p style='color:red;'> Error: " . $resultado["error"] . "</p>";
        }

        return "<p style='color:red;'> Todos los campos del pedido deben estar llenos para CREAR.</p>";
    }

    private function handleUpdate() {
        $data = $this->getPostData(['id', 'id_CLIENTE', 'id_EMPLEADO', 'id_ESTADO_PEDIDO', 'total_PRODUCTO']);
        $id = (int)($data['id'] ?? 0);

        if (!empty($id) && !empty($data['id_CLIENTE']) && !empty($data['id_EMPLEADO']) && !empty($data['id_ESTADO_PEDIDO']) && !empty($data['total_PRODUCTO'])) {
            
            // 1. ✅ PASO CRÍTICO: Recuperar el pedido original para obtener las fechas.
            $pedido_original = $this->pedidosService->obtenerPedidoPorId($id);

            if (!$pedido_original) {
                 return "<p style='color:red;'> Error: No se encontró el pedido con ID {$id} para actualizar.</p>";
            }

            $datos_pedido = [
                "id_CLIENTE" => (int)$data['id_CLIENTE'],
                "id_EMPLEADO" => (int)$data['id_EMPLEADO'],
                "id_ESTADO_PEDIDO" => (int)$data['id_ESTADO_PEDIDO'],
                "total_PRODUCTO" => (float)$data['total_PRODUCTO'],
                // 2. ✅ Mantener la fecha de ingreso original.
                "fecha_INGRESO" => $pedido_original['fecha_INGRESO'], 
                // 3. ✅ Mantener la fecha de entrega original (podrías agregar lógica aquí para actualizarla si el nuevo estado es "Entregado")
                "fecha_ENTREGA" => $pedido_original['fecha_ENTREGA'] 
            ];

            $resultado = $this->pedidosService->actualizarPedido($id, $datos_pedido);

            return $resultado["success"] 
                ? "<p style='color:green;'> Pedido actualizado correctamente (ID: {$id}).</p>"
                : "<p style='color:red;'> Error al actualizar (ID: {$id}): " . $resultado["error"] . "</p>";
        }

        return "<p style='color:red;'> Todos los campos del pedido y el ID deben estar llenos para ACTUALIZAR.</p>";
    }

    private function handleDelete() {
        $data = $this->getPostData(['id']);
        $id = (int)($data['id'] ?? 0);

        if (!empty($id)) {
            $resultado = $this->pedidosService->eliminarPedido($id);

            return $resultado["success"]
                ? "<p style='color:green;'> Pedido eliminado correctamente (ID: {$id}).</p>"
                : "<p style='color:red;'> Error al eliminar (ID: {$id}): " . $resultado["error"] . "</p>";
        }

        return "<p style='color:red;'> El ID del pedido debe estar presente para ELIMINAR.</p>";
    }
}