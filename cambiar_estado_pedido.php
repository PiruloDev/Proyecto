<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está logueado y es admin
if (!isset($_SESSION['usuario_logueado']) || 
    $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || 
    $_SESSION['usuario_tipo'] !== 'admin') {
    
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['pedido_id']) || !isset($input['nuevo_estado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

$pedido_id = (int)$input['pedido_id'];
$nuevo_estado = (int)$input['nuevo_estado'];

// Validar que el estado sea válido (1-5)
if ($nuevo_estado < 1 || $nuevo_estado > 5) {
    echo json_encode(['success' => false, 'message' => 'Estado inválido']);
    exit();
}

try {
    // Verificar que el pedido existe
    $check_query = "SELECT ID_PEDIDO FROM Pedidos WHERE ID_PEDIDO = ?";
    $check_stmt = mysqli_prepare($conexion, $check_query);
    
    if (!$check_stmt) {
        throw new Exception("Error preparando consulta de verificación: " . mysqli_error($conexion));
    }
    
    mysqli_stmt_bind_param($check_stmt, 'i', $pedido_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
        exit();
    }
    
    // Actualizar el estado del pedido
    $update_query = "UPDATE Pedidos SET ID_ESTADO_PEDIDO = ? WHERE ID_PEDIDO = ?";
    $update_stmt = mysqli_prepare($conexion, $update_query);
    
    if (!$update_stmt) {
        throw new Exception("Error preparando consulta de actualización: " . mysqli_error($conexion));
    }
    
    mysqli_stmt_bind_param($update_stmt, 'ii', $nuevo_estado, $pedido_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Obtener el nombre del estado para el log
        $estados = [
            1 => 'Pendiente',
            2 => 'En Preparación', 
            3 => 'Listo para Entrega',
            4 => 'Entregado',
            5 => 'Cancelado'
        ];
        
        $nombre_estado = $estados[$nuevo_estado];
        
        // Log de la acción (opcional)
        error_log("Admin {$_SESSION['usuario_nombre']} cambió el estado del pedido #{$pedido_id} a {$nombre_estado}");
        
        echo json_encode([
            'success' => true, 
            'message' => "Estado del pedido #{$pedido_id} actualizado a {$nombre_estado}"
        ]);
    } else {
        throw new Exception("Error al actualizar el estado: " . mysqli_error($conexion));
    }
    
} catch (Exception $e) {
    error_log("Error en cambiar_estado_pedido.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>
