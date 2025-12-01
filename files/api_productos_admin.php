<?php
/**
 * API REST para gestión de productos - Administrador
 * Endpoints para CRUD completo de productos con logs de cambios
 */

session_start();
header('Content-Type: application/json');

// Verificar autenticación de administrador
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_tipo'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once 'conexion.php';

// Función para registrar cambios en el log
function registrarCambio($conexion, $producto_id, $tipo_cambio, $valor_anterior, $valor_nuevo, $usuario_id, $ip_usuario) {
    $stmt = $conexion->prepare("INSERT INTO productos_logs (producto_id, tipo_cambio, valor_anterior, valor_nuevo, usuario_id, usuario_tipo, ip_usuario) VALUES (?, ?, ?, ?, ?, 'admin', ?)");
    $stmt->bind_param("isssss", $producto_id, $tipo_cambio, $valor_anterior, $valor_nuevo, $usuario_id, $ip_usuario);
    return $stmt->execute();
}

// Obtener información del usuario
$usuario_id = $_SESSION['usuario_id'] ?? 0;
$ip_usuario = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Manejar diferentes métodos HTTP
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path_parts = explode('/', trim(parse_url($request_uri, PHP_URL_PATH), '/'));

try {
    switch ($method) {
        case 'GET':
            handleGet();
            break;
        case 'POST':
            handlePost();
            break;
        case 'PUT':
            handlePut();
            break;
        case 'PATCH':
            handlePatch();
            break;
        case 'DELETE':
            handleDelete();
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}

/**
 * GET - Obtener productos o producto específico
 */
function handleGet() {
    global $conexion;
    
    $producto_id = $_GET['id'] ?? null;
    $incluir_logs = $_GET['incluir_logs'] ?? false;
    
    if ($producto_id) {
        // Obtener producto específico
        $stmt = $conexion->prepare("
            SELECT p.*, c.NOMBRE_CATEGORIA_PRODUCTO as categoria_nombre,
                   a.NOMBRE_ADMIN as admin_nombre
            FROM Productos p 
            LEFT JOIN Categoria_Productos c ON p.ID_CATEGORIA_PRODUCTO = c.ID_CATEGORIA_PRODUCTO
            LEFT JOIN Administradores a ON p.ID_ADMIN = a.ID_ADMIN
            WHERE p.ID_PRODUCTO = ?
        ");
        $stmt->bind_param("i", $producto_id);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();
        
        if (!$producto) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            return;
        }
        
        $response = ['success' => true, 'data' => $producto];
        
        // Incluir logs si se solicita
        if ($incluir_logs) {
            $stmt = $conexion->prepare("
                SELECT * FROM productos_logs 
                WHERE producto_id = ? 
                ORDER BY fecha_cambio DESC 
                LIMIT 50
            ");
            $stmt->bind_param("i", $producto_id);
            $stmt->execute();
            $logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $response['logs'] = $logs;
        }
        
        echo json_encode($response);
    } else {
        // Obtener todos los productos
        $activos_solo = $_GET['activos_solo'] ?? false;
        $categoria = $_GET['categoria'] ?? null;
        $buscar = $_GET['buscar'] ?? null;
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = min(100, max(10, intval($_GET['limit'] ?? 20)));
        $offset = ($page - 1) * $limit;
        
        $where_conditions = [];
        $params = [];
        $param_types = "";
        
        if ($activos_solo) {
            $where_conditions[] = "p.ACTIVO = 1";
        }
        
        if ($categoria) {
            $where_conditions[] = "p.ID_CATEGORIA_PRODUCTO = ?";
            $params[] = $categoria;
            $param_types .= "i";
        }
        
        if ($buscar) {
            $where_conditions[] = "p.NOMBRE_PRODUCTO LIKE ?";
            $params[] = "%$buscar%";
            $param_types .= "s";
        }
        
        $where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";
        
        // Contar total de productos
        $count_query = "
            SELECT COUNT(*) as total 
            FROM Productos p 
            LEFT JOIN Categoria_Productos c ON p.ID_CATEGORIA_PRODUCTO = c.ID_CATEGORIA_PRODUCTO
            $where_clause
        ";
        
        if ($params) {
            $stmt = $conexion->prepare($count_query);
            $stmt->bind_param($param_types, ...$params);
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'];
        } else {
            $total = $conexion->query($count_query)->fetch_assoc()['total'];
        }
        
        // Obtener productos paginados
        $query = "
            SELECT p.*, c.NOMBRE_CATEGORIA_PRODUCTO as categoria_nombre,
                   a.NOMBRE_ADMIN as admin_nombre
            FROM Productos p 
            LEFT JOIN Categoria_Productos c ON p.ID_CATEGORIA_PRODUCTO = c.ID_CATEGORIA_PRODUCTO
            LEFT JOIN Administradores a ON p.ID_ADMIN = a.ID_ADMIN
            $where_clause
            ORDER BY p.FECHA_ULTIMA_MODIFICACION DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        $param_types .= "ii";
        
        $stmt = $conexion->prepare($query);
        if ($params) {
            $stmt->bind_param($param_types, ...$params);
        }
        $stmt->execute();
        $productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $productos,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
}

/**
 * POST - Crear nuevo producto
 */
function handlePost() {
    global $conexion, $usuario_id, $ip_usuario;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $required_fields = ['nombre_producto', 'precio_producto', 'stock_min', 'fecha_vencimiento', 'tipo_producto_marca', 'id_categoria_producto'];
    
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => "Campo requerido: $field"]);
            return;
        }
    }
    
    // Validaciones
    if (floatval($input['precio_producto']) <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El precio debe ser mayor a 0']);
        return;
    }
    
    if (intval($input['stock_min']) < 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El stock no puede ser negativo']);
        return;
    }
    
    $stmt = $conexion->prepare("
        INSERT INTO Productos (
            ID_ADMIN, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, 
            PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO,
            FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA, ACTIVO
        ) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?, 1)
    ");
    
    $stmt->bind_param("iiisdss", 
        $usuario_id,
        $input['id_categoria_producto'],
        $input['nombre_producto'],
        $input['stock_min'],
        $input['precio_producto'],
        $input['fecha_vencimiento'],
        $input['tipo_producto_marca']
    );
    
    if ($stmt->execute()) {
        $nuevo_id = $conexion->insert_id;
        
        // Registrar creación en logs
        registrarCambio($conexion, $nuevo_id, 'precio', '0', $input['precio_producto'], $usuario_id, $ip_usuario);
        registrarCambio($conexion, $nuevo_id, 'stock', '0', $input['stock_min'], $usuario_id, $ip_usuario);
        
        http_response_code(201);
        echo json_encode([
            'success' => true, 
            'message' => 'Producto creado correctamente',
            'data' => ['id' => $nuevo_id]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al crear el producto']);
    }
}

/**
 * PUT - Actualizar producto completo
 */
function handlePut() {
    global $conexion, $usuario_id, $ip_usuario;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $producto_id = intval($input['id'] ?? 0);
    
    if ($producto_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
        return;
    }
    
    // Obtener datos actuales del producto
    $stmt = $conexion->prepare("SELECT * FROM Productos WHERE ID_PRODUCTO = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $producto_actual = $stmt->get_result()->fetch_assoc();
    
    if (!$producto_actual) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        return;
    }
    
    $stmt = $conexion->prepare("
        UPDATE Productos SET 
            ID_CATEGORIA_PRODUCTO = ?, NOMBRE_PRODUCTO = ?, 
            PRODUCTO_STOCK_MIN = ?, PRECIO_PRODUCTO = ?, 
            FECHA_VENCIMIENTO_PRODUCTO = ?, TIPO_PRODUCTO_MARCA = ?
        WHERE ID_PRODUCTO = ?
    ");
    
    $stmt->bind_param("isiissi",
        $input['id_categoria_producto'],
        $input['nombre_producto'],
        $input['stock_min'],
        $input['precio_producto'],
        $input['fecha_vencimiento'],
        $input['tipo_producto_marca'],
        $producto_id
    );
    
    if ($stmt->execute()) {
        // Registrar cambios específicos
        if ($producto_actual['PRECIO_PRODUCTO'] != $input['precio_producto']) {
            registrarCambio($conexion, $producto_id, 'precio', $producto_actual['PRECIO_PRODUCTO'], $input['precio_producto'], $usuario_id, $ip_usuario);
        }
        
        if ($producto_actual['PRODUCTO_STOCK_MIN'] != $input['stock_min']) {
            registrarCambio($conexion, $producto_id, 'stock', $producto_actual['PRODUCTO_STOCK_MIN'], $input['stock_min'], $usuario_id, $ip_usuario);
        }
        
        echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto']);
    }
}

/**
 * PATCH - Actualización parcial (precio/stock específicos)
 */
function handlePatch() {
    global $conexion, $usuario_id, $ip_usuario;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $producto_id = intval($input['id'] ?? 0);
    $campo = $input['campo'] ?? '';
    $valor = $input['valor'] ?? '';
    
    if ($producto_id <= 0 || empty($campo)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de producto y campo requeridos']);
        return;
    }
    
    // Obtener producto actual
    $stmt = $conexion->prepare("SELECT PRECIO_PRODUCTO, PRODUCTO_STOCK_MIN FROM Productos WHERE ID_PRODUCTO = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    
    if (!$producto) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        return;
    }
    
    switch ($campo) {
        case 'precio':
            $nuevo_precio = floatval($valor);
            if ($nuevo_precio <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El precio debe ser mayor a 0']);
                return;
            }
            
            $stmt = $conexion->prepare("UPDATE Productos SET PRECIO_PRODUCTO = ? WHERE ID_PRODUCTO = ?");
            $stmt->bind_param("di", $nuevo_precio, $producto_id);
            
            if ($stmt->execute()) {
                registrarCambio($conexion, $producto_id, 'precio', $producto['PRECIO_PRODUCTO'], $nuevo_precio, $usuario_id, $ip_usuario);
                echo json_encode(['success' => true, 'message' => 'Precio actualizado correctamente', 'nuevo_valor' => $nuevo_precio]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar precio']);
            }
            break;
            
        case 'stock':
            $nuevo_stock = intval($valor);
            if ($nuevo_stock < 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El stock no puede ser negativo']);
                return;
            }
            
            $stmt = $conexion->prepare("UPDATE Productos SET PRODUCTO_STOCK_MIN = ? WHERE ID_PRODUCTO = ?");
            $stmt->bind_param("ii", $nuevo_stock, $producto_id);
            
            if ($stmt->execute()) {
                registrarCambio($conexion, $producto_id, 'stock', $producto['PRODUCTO_STOCK_MIN'], $nuevo_stock, $usuario_id, $ip_usuario);
                echo json_encode(['success' => true, 'message' => 'Stock actualizado correctamente', 'nuevo_valor' => $nuevo_stock]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar stock']);
            }
            break;
            
        case 'activo':
            $activo = intval($valor);
            $tipo_log = $activo ? 'activacion' : 'desactivacion';
            
            $stmt = $conexion->prepare("UPDATE Productos SET ACTIVO = ? WHERE ID_PRODUCTO = ?");
            $stmt->bind_param("ii", $activo, $producto_id);
            
            if ($stmt->execute()) {
                registrarCambio($conexion, $producto_id, $tipo_log, $activo ? '0' : '1', $activo, $usuario_id, $ip_usuario);
                echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente', 'nuevo_valor' => $activo]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al actualizar estado']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Campo no válido']);
            break;
    }
}

/**
 * DELETE - Eliminar producto (soft delete)
 */
function handleDelete() {
    global $conexion, $usuario_id, $ip_usuario;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $producto_id = intval($input['id'] ?? 0);
    $forzar_eliminacion = $input['forzar'] ?? false;
    
    if ($producto_id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID de producto requerido']);
        return;
    }
    
    if ($forzar_eliminacion) {
        // Eliminación física
        $stmt = $conexion->prepare("DELETE FROM Productos WHERE ID_PRODUCTO = ?");
        $stmt->bind_param("i", $producto_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado permanentemente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al eliminar producto']);
        }
    } else {
        // Soft delete
        $stmt = $conexion->prepare("UPDATE Productos SET ACTIVO = 0 WHERE ID_PRODUCTO = ?");
        $stmt->bind_param("i", $producto_id);
        
        if ($stmt->execute()) {
            registrarCambio($conexion, $producto_id, 'desactivacion', '1', '0', $usuario_id, $ip_usuario);
            echo json_encode(['success' => true, 'message' => 'Producto desactivado correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al desactivar producto']);
        }
    }
}
?>
