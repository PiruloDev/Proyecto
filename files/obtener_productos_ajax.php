<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Debug de sesión completo
error_log("AJAX Debug Productos - Session ID: " . session_id());
error_log("AJAX Debug Productos - Session Data: " . json_encode($_SESSION));

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
    
    error_log("AJAX Auth Failed Productos - Debug: " . json_encode($debug_info));
    
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'mensaje' => 'No autorizado para productos. Debug info logged.'
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
    error_log("Productos AJAX - Input: " . print_r($input, true));
    
    $buscar = isset($input['buscar']) ? trim($input['buscar']) : '';
    $estado = isset($input['estado']) ? $input['estado'] : '';
    $categoria = isset($input['categoria']) ? $input['categoria'] : '';

    // Primero, verificar si las tablas existen
    $test_query = "SHOW TABLES LIKE 'Productos'";
    $test_result = $conexion->query($test_query);
    if (!$test_result || $test_result->num_rows == 0) {
        throw new Exception("La tabla 'Productos' no existe");
    }

    // Construir consulta base - simplificada inicialmente
    $query = "
        SELECT 
            p.ID_PRODUCTO as id,
            p.NOMBRE_PRODUCTO as nombre,
            'Sin categoría' as categoria,
            p.PRECIO_PRODUCTO as precio,
            p.PRODUCTO_STOCK_MIN as stock_min,
            p.FECHA_VENCIMIENTO_PRODUCTO as vencimiento,
            p.ACTIVO as activo
        FROM Productos p
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    // Filtros
    if (!empty($buscar)) {
        $query .= " AND p.NOMBRE_PRODUCTO LIKE ?";
        $params[] = '%' . $buscar . '%';
        $types .= 's';
    }
    
    if ($estado !== '') {
        $query .= " AND p.ACTIVO = ?";
        $params[] = (int)$estado;
        $types .= 'i';
    }
    
    $query .= " ORDER BY p.NOMBRE_PRODUCTO ASC LIMIT 100";
    
    error_log("Productos AJAX - Query: " . $query);
    error_log("Productos AJAX - Params: " . print_r($params, true));
    
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
    
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = [
            'id' => (int)$row['id'],
            'nombre' => $row['nombre'],
            'categoria' => $row['categoria'] ?: 'Sin categoría',
            'precio' => (float)$row['precio'],
            'stock_min' => (int)$row['stock_min'],
            'vencimiento' => $row['vencimiento'],
            'activo' => (bool)$row['activo']
        ];
    }
    
    // Obtener categorías (simplificado)
    $categorias = [];
    try {
        $cat_query = "SHOW TABLES LIKE 'Categoria_Productos'";
        $cat_result = $conexion->query($cat_query);
        if ($cat_result && $cat_result->num_rows > 0) {
            $categorias_query = "SELECT ID_CATEGORIA_PRODUCTO as id, NOMBRE_CATEGORIAPRODUCTO as nombre FROM Categoria_Productos ORDER BY NOMBRE_CATEGORIAPRODUCTO LIMIT 20";
            $categorias_result = $conexion->query($categorias_query);
            
            if ($categorias_result) {
                while ($cat = $categorias_result->fetch_assoc()) {
                    $categorias[] = [
                        'id' => (int)$cat['id'],
                        'nombre' => $cat['nombre']
                    ];
                }
            }
        }
    } catch (Exception $cat_error) {
        error_log("Error cargando categorías: " . $cat_error->getMessage());
    }
    
    error_log("Productos AJAX - Resultado: " . count($productos) . " productos encontrados");
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'categorias' => $categorias,
        'total' => count($productos),
        'debug' => [
            'query' => $query,
            'params' => $params
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error en obtener_productos_ajax.php: " . $e->getMessage());
    error_log("Error stack: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'mensaje' => $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>
