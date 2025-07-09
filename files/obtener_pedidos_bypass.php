<?php
// Temporary bypass for testing - DO NOT USE IN PRODUCTION
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// LOG THE REQUEST
error_log("BYPASS Pedidos - Request received");
error_log("BYPASS Pedidos - Session: " . json_encode($_SESSION));

require_once 'conexion.php';

try {
    // Force empleado session for testing
    $_SESSION['usuario_logueado'] = true;
    $_SESSION['usuario_tipo'] = 'empleado';
    $_SESSION['usuario_id'] = 1; // Use a test employee ID
    $_SESSION['usuario_nombre'] = 'Test Employee';
    
    error_log("BYPASS Pedidos - Forced session: " . json_encode($_SESSION));
    
    // Simple query to get all pedidos
    $sql = "
        SELECT 
            p.ID_PEDIDO,
            p.FECHA_INGRESO,
            p.TOTAL_PRODUCTO,
            c.NOMBRE_CLI,
            e.NOMBRE_EMPLEADO,
            ep.NOMBRE_ESTADO,
            p.CANTIDAD_PRODUCTOS
        FROM Pedidos p
        LEFT JOIN Clientes c ON p.ID_CLIENTE = c.ID_CLIENTE
        LEFT JOIN Empleados e ON p.ID_EMPLEADO = e.ID_EMPLEADO  
        LEFT JOIN Estados_Pedidos ep ON p.ID_ESTADO = ep.ID_ESTADO
        WHERE p.ID_ESTADO IN (1, 2, 3)
        ORDER BY p.FECHA_INGRESO DESC
        LIMIT 20
    ";
    
    $stmt = $pdo_conexion->prepare($sql);
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos,
        'total' => count($pedidos),
        'mensaje' => 'Datos cargados con bypass - SOLO PARA TESTING',
        'session_info' => $_SESSION
    ]);
    
} catch (Exception $e) {
    error_log("BYPASS Pedidos Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error: ' . $e->getMessage(),
        'session_info' => $_SESSION
    ]);
}
?>
