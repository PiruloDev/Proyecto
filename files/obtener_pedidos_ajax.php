<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Debug de sesión completo
error_log("AJAX Debug - Session ID: " . session_id());
error_log("AJAX Debug - Session Data: " . json_encode($_SESSION));
error_log("AJAX Debug - Headers: " . json_encode(getallheaders()));

// Verificar autenticación de empleado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true || 
    !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'empleado') {
    
    // Debug extendido para entender el problema
    $debug_info = [
        'session_id' => session_id(),
        'usuario_logueado' => $_SESSION['usuario_logueado'] ?? 'NO_SET',
        'usuario_tipo' => $_SESSION['usuario_tipo'] ?? 'NO_SET',
        'all_session' => $_SESSION,
        'cookies' => $_COOKIE
    ];
    
    error_log("AJAX Auth Failed - Debug: " . json_encode($debug_info));
    
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'mensaje' => 'No autorizado. Debug info logged.',
        'debug' => $debug_info
    ]);
    exit();
}

require_once 'conexion.php';

try {
    // Verificar conexión
    if (!$conexion) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
    }
    
    // Para debug, loggear los parámetros
    error_log("Pedidos AJAX - Input: " . print_r($input, true));
    error_log("Pedidos AJAX - Session: " . print_r($_SESSION, true));
    
    $estado = isset($input['estado']) ? $input['estado'] : '';
    $fecha = isset($input['fecha']) ? $input['fecha'] : '';
    $solo_pendientes = isset($input['solo_pendientes']) ? $input['solo_pendientes'] : false;

    // Primero, verificar si las tablas existen
    $test_query = "SHOW TABLES LIKE 'Pedidos'";
    $test_result = $conexion->query($test_query);
    if (!$test_result || $test_result->num_rows == 0) {
        throw new Exception("La tabla 'Pedidos' no existe");
    }

    // Construir consulta con las tablas correctas
    $query = "
        SELECT 
            p.ID_PEDIDO as id,
            COALESCE(c.NOMBRE_CLI, 'Cliente eliminado') as cliente,
            COALESCE(e.NOMBRE_EMPLEADO, 'No asignado') as empleado,
            p.FECHA_INGRESO as fecha_ingreso,
            p.FECHA_ENTREGA as fecha_entrega,
            p.TOTAL_PRODUCTO as total,
            p.ID_ESTADO_PEDIDO as estado,
            COALESCE(ep.NOMBRE_ESTADO, 'Estado desconocido') as estado_nombre
        FROM Pedidos p
        LEFT JOIN Clientes c ON p.ID_CLIENTE = c.ID_CLIENTE
        LEFT JOIN Empleados e ON p.ID_EMPLEADO = e.ID_EMPLEADO
        LEFT JOIN Estado_Pedidos ep ON p.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    // Filtros
    if ($solo_pendientes) {
        $query .= " AND p.ID_ESTADO_PEDIDO IN (1, 2, 3)"; // Pendiente, Preparación, Listo
    }
    
    if (!empty($estado)) {
        $query .= " AND p.ID_ESTADO_PEDIDO = ?";
        $params[] = $estado;
        $types .= 'i';
    }
    
    if (!empty($fecha)) {
        $query .= " AND DATE(p.FECHA_INGRESO) = ?";
        $params[] = $fecha;
        $types .= 's';
    }
    
    $query .= " ORDER BY p.FECHA_INGRESO DESC, p.ID_PEDIDO DESC LIMIT 50";
    
    error_log("Pedidos AJAX - Query: " . $query);
    error_log("Pedidos AJAX - Params: " . print_r($params, true));
    
    $stmt = $conexion->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Error preparando consulta: " . $conexion->error);
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Error obteniendo resultados: " . $stmt->error);
    }
    
    $pedidos = [];
    while ($row = $result->fetch_assoc()) {
        $pedidos[] = [
            'id' => (int)$row['id'],
            'cliente' => $row['cliente'] ?: 'Cliente eliminado',
            'empleado' => $row['empleado'] ?: 'No asignado',
            'fecha_ingreso' => $row['fecha_ingreso'],
            'fecha_entrega' => $row['fecha_entrega'],
            'total' => (float)$row['total'],
            'estado' => (int)$row['estado'],
            'estado_nombre' => $row['estado_nombre'] ?: 'Estado desconocido'
        ];
    }
    
    error_log("Pedidos AJAX - Resultado: " . count($pedidos) . " pedidos encontrados");
    
    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos,
        'total' => count($pedidos),
        'debug' => [
            'query' => $query,
            'params' => $params,
            'session_user' => $_SESSION['usuario_id'] ?? 'no_set'
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error en obtener_pedidos_ajax.php: " . $e->getMessage());
    error_log("Error stack: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'session_info' => [
                'usuario_logueado' => $_SESSION['usuario_logueado'] ?? 'not_set',
                'usuario_tipo' => $_SESSION['usuario_tipo'] ?? 'not_set'
            ]
        ]
    ]);
}
?>
