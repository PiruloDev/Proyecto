<?php
session_start();

header('Content-Type: application/json');

// Verificar que el usuario esté logueado y sea cliente
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once 'conexion.php';

// Obtener el cuerpo de la petición
$input = json_decode(file_get_contents('php://input'), true);
$cliente_id = $input['cliente_id'] ?? $_SESSION['usuario_id'];

// Verificar que el cliente_id coincida con la sesión
if ($cliente_id != $_SESSION['usuario_id']) {
    echo json_encode(['success' => false, 'message' => 'Cliente no autorizado']);
    exit();
}

try {
    // Obtener todos los pedidos del cliente con sus estados actuales
    $stmt = $conexion->prepare("
        SELECT p.ID_PEDIDO, ep.NOMBRE_ESTADO
        FROM Pedidos p
        INNER JOIN Estado_Pedidos ep ON p.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
        WHERE p.ID_CLIENTE = ?
        ORDER BY p.FECHA_INGRESO DESC
    ");
    
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $pedidos = [];
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = [
            'id' => $row['ID_PEDIDO'],
            'estado' => $row['NOMBRE_ESTADO']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    error_log("Error obteniendo estados de pedidos: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor'
    ]);
}
?>
