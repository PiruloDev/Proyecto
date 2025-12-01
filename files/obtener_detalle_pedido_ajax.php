<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Debug de sesión completo
error_log("AJAX Detalle Pedido - Session ID: " . session_id());
error_log("AJAX Detalle Pedido - Session Data: " . json_encode($_SESSION));

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
    
    error_log("AJAX Auth Failed Detalle Pedido - Debug: " . json_encode($debug_info));
    
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'mensaje' => 'No autorizado para ver detalles de pedidos.'
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
    
    // Validar datos
    if ($pedido_id <= 0) {
        throw new Exception("ID de pedido inválido");
    }
    
    error_log("Obteniendo detalle del pedido: $pedido_id");
    
    // Obtener información básica del pedido
    $stmt = $conexion->prepare("
        SELECT 
            p.ID_PEDIDO,
            p.FECHA_INGRESO,
            p.FECHA_ENTREGA,
            p.TOTAL_PRODUCTO,
            COALESCE(c.NOMBRE_CLI, 'Cliente eliminado') as cliente,
            COALESCE(c.EMAIL_CLI, '') as email_cliente,
            COALESCE(c.TELEFONO_CLI, '') as telefono_cliente,
            COALESCE(e.NOMBRE_EMPLEADO, 'No asignado') as empleado,
            COALESCE(ep.NOMBRE_ESTADO, 'Estado desconocido') as estado_nombre,
            p.ID_ESTADO_PEDIDO as estado_id
        FROM Pedidos p
        LEFT JOIN Clientes c ON p.ID_CLIENTE = c.ID_CLIENTE
        LEFT JOIN Empleados e ON p.ID_EMPLEADO = e.ID_EMPLEADO
        LEFT JOIN Estado_Pedidos ep ON p.ID_ESTADO_PEDIDO = ep.ID_ESTADO_PEDIDO
        WHERE p.ID_PEDIDO = ?
    ");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pedido = $result->fetch_assoc();
    
    if (!$pedido) {
        throw new Exception("Pedido no encontrado");
    }
    
    // Verificar si la columna DESCRIPCION_PRODUCTO existe
    $check_descripcion = $conexion->query("SHOW COLUMNS FROM Productos LIKE 'DESCRIPCION_PRODUCTO'");
    $has_descripcion = $check_descripcion && $check_descripcion->num_rows > 0;
    
    // Construir consulta según las columnas disponibles
    $descripcion_field = $has_descripcion ? 
        "COALESCE(prod.DESCRIPCION_PRODUCTO, prod.TIPO_PRODUCTO_MARCA, '') as producto_descripcion" :
        "COALESCE(prod.TIPO_PRODUCTO_MARCA, '') as producto_descripcion";
    
    // Obtener los productos del pedido con información completa
    $stmt = $conexion->prepare("
        SELECT 
            dp.ID_DETALLE,
            dp.CANTIDAD_PRODUCTO,
            dp.PRECIO_UNITARIO,
            dp.SUBTOTAL,
            COALESCE(prod.NOMBRE_PRODUCTO, 'Producto eliminado') as producto_nombre,
            $descripcion_field,
            COALESCE(cat.NOMBRE_CATEGORIAPRODUCTO, 'Sin categoría') as categoria_nombre,
            prod.ID_PRODUCTO as producto_id
        FROM Detalle_Pedidos dp
        LEFT JOIN Productos prod ON dp.ID_PRODUCTO = prod.ID_PRODUCTO
        LEFT JOIN Categoria_Productos cat ON prod.ID_CATEGORIA_PRODUCTO = cat.ID_CATEGORIA_PRODUCTO
        WHERE dp.ID_PEDIDO = ?
        ORDER BY dp.ID_DETALLE
    ");
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $productos = $result->fetch_all(MYSQLI_ASSOC);
    
    // Calcular totales
    $total_items = 0;
    $total_calculado = 0;
    foreach ($productos as $producto) {
        $total_items += $producto['CANTIDAD_PRODUCTO'];
        $total_calculado += $producto['SUBTOTAL'];
    }
    
    error_log("Detalle obtenido - Pedido: $pedido_id, Productos: " . count($productos) . ", Total items: $total_items");
    
    echo json_encode([
        'success' => true,
        'pedido' => [
            'id' => $pedido['ID_PEDIDO'],
            'fecha_ingreso' => $pedido['FECHA_INGRESO'],
            'fecha_entrega' => $pedido['FECHA_ENTREGA'],
            'total' => $pedido['TOTAL_PRODUCTO'],
            'cantidad_productos' => $total_items,
            'cliente' => $pedido['cliente'],
            'email_cliente' => $pedido['email_cliente'],
            'telefono_cliente' => $pedido['telefono_cliente'],
            'empleado' => $pedido['empleado'],
            'estado_nombre' => $pedido['estado_nombre'],
            'estado_id' => $pedido['estado_id']
        ],
        'productos' => array_map(function($prod) {
            return [
                'id_detalle' => $prod['ID_DETALLE'],
                'producto_id' => $prod['producto_id'],
                'nombre' => $prod['producto_nombre'],
                'descripcion' => $prod['producto_descripcion'],
                'categoria' => $prod['categoria_nombre'],
                'cantidad' => $prod['CANTIDAD_PRODUCTO'],
                'precio_unitario' => $prod['PRECIO_UNITARIO'],
                'subtotal' => $prod['SUBTOTAL']
            ];
        }, $productos),
        'resumen' => [
            'total_items' => $total_items,
            'total_calculado' => $total_calculado,
            'total_productos_diferentes' => count($productos)
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error en obtener_detalle_pedido_ajax.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
?>
