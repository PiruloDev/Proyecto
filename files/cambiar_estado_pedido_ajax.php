<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Debug de sesión completo
error_log("AJAX Cambiar Estado - Session ID: " . session_id());
error_log("AJAX Cambiar Estado - Session Data: " . json_encode($_SESSION));

// Verificar autenticación de empleado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'empleado') {
    
    // Debug extendido para entender el problema
    $debug_info = [
        'session_id' => session_id(),
        'usuario_logueado' => $_SESSION['usuario_logueado'] ?? 'NO_SET',
        'usuario_tipo' => $_SESSION['usuario_tipo'] ?? 'NO_SET',
        'all_session' => $_SESSION
    ];
    
    error_log("AJAX Auth Failed Cambiar Estado - Debug: " . json_encode($debug_info));
    
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'mensaje' => 'No autorizado para cambiar estados.'
    ]);
    exit();
}

require_once 'conexion.php';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos");
    }

    // Obtener datos de la petición
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        throw new Exception("Datos de entrada inválidos");
    }
    
    $pedido_id = isset($data['pedido_id']) ? intval($data['pedido_id']) : 0;
    $nuevo_estado = isset($data['nuevo_estado']) ? intval($data['nuevo_estado']) : 0;
    
    // Validar datos
    if ($pedido_id <= 0) {
        throw new Exception("ID de pedido inválido");
    }
    
    if ($nuevo_estado < 1 || $nuevo_estado > 5) {
        throw new Exception("Estado inválido");
    }
    
    error_log("Cambiando estado - Pedido: $pedido_id, Nuevo estado: $nuevo_estado, Empleado: " . $_SESSION['usuario_id']);
    
    // Verificar que el pedido existe
    $stmt = $conexion->prepare("SELECT ID_PEDIDO, ID_ESTADO_PEDIDO, ID_EMPLEADO FROM Pedidos WHERE ID_PEDIDO = ?");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pedido = $result->fetch_assoc();
    
    if (!$pedido) {
        throw new Exception("Pedido no encontrado");
    }
    
    // Si el pedido no tiene empleado asignado, asignarlo al empleado actual
    if (empty($pedido['ID_EMPLEADO'])) {
        $stmt = $conexion->prepare("UPDATE Pedidos SET ID_EMPLEADO = ? WHERE ID_PEDIDO = ?");
        $stmt->bind_param("ii", $_SESSION['usuario_id'], $pedido_id);
        $stmt->execute();
        
        error_log("Pedido $pedido_id asignado al empleado " . $_SESSION['usuario_id']);
    }
    
    // Actualizar el estado del pedido
    $stmt = $conexion->prepare("UPDATE Pedidos SET ID_ESTADO_PEDIDO = ? WHERE ID_PEDIDO = ?");
    $stmt->bind_param("ii", $nuevo_estado, $pedido_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al actualizar el estado del pedido");
    }
    
    // Obtener el nombre del nuevo estado
    $stmt = $conexion->prepare("SELECT NOMBRE_ESTADO FROM Estado_Pedidos WHERE ID_ESTADO_PEDIDO = ?");
    $stmt->bind_param("i", $nuevo_estado);
    $stmt->execute();
    $result = $stmt->get_result();
    $estado = $result->fetch_assoc();
    
    $nombre_estado = $estado ? $estado['NOMBRE_ESTADO'] : "Estado $nuevo_estado";
    
    error_log("Estado cambiado exitosamente - Pedido: $pedido_id, Nuevo estado: $nombre_estado");
    
    echo json_encode([
        'success' => true,
        'mensaje' => "Estado del pedido #$pedido_id cambiado a '$nombre_estado'",
        'pedido_id' => $pedido_id,
        'nuevo_estado' => $nuevo_estado,
        'nombre_estado' => $nombre_estado
    ]);
    
} catch (Exception $e) {
    error_log("Error en cambiar_estado_pedido_ajax.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
?>
