<?php
/**
 * API para procesar pedidos y descontar stock
 * Maneja el proceso de pedido de productos por parte de clientes
 */

session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Configuración de la base de datos
$host = 'localhost';
$db_name = 'proyectopanaderia';
$username = 'root';
$password = '';

// Función para validar sesión de cliente
function validarSesionCliente() {
    return isset($_SESSION['cliente_id']) && !empty($_SESSION['cliente_id']);
}

// Función para log de actividad
function logActividad($mensaje, $cliente_id = null) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $mensaje";
    if ($cliente_id) {
        $log_entry .= " - Cliente ID: $cliente_id";
    }
    error_log($log_entry);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Manejar solicitudes OPTIONS para CORS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    
    // Solo permitir POST para pedidos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    // Obtener datos del pedido
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos de pedido inválidos');
    }
    
    $producto_id = isset($input['producto_id']) ? (int)$input['producto_id'] : 0;
    $cantidad = isset($input['cantidad']) ? (int)$input['cantidad'] : 1;
    
    // Validaciones básicas
    if ($producto_id <= 0) {
        throw new Exception('ID de producto inválido');
    }
    
    if ($cantidad <= 0) {
        throw new Exception('Cantidad inválida');
    }
    
    // Verificar si el usuario está logueado
    if (!validarSesionCliente()) {
        throw new Exception('Debe iniciar sesión para realizar pedidos');
    }
    
    $cliente_id = $_SESSION['cliente_id'];
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    try {
        // Verificar que el producto existe y está activo
        $stmt = $pdo->prepare("
            SELECT 
                ID_PRODUCTO, 
                NOMBRE_PRODUCTO, 
                PRECIO_PRODUCTO, 
                PRODUCTO_STOCK_MIN,
                ACTIVO
            FROM Productos 
            WHERE ID_PRODUCTO = ? AND ACTIVO = 1
            FOR UPDATE
        ");
        $stmt->execute([$producto_id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            throw new Exception('Producto no encontrado o no disponible');
        }
        
        // Verificar stock disponible
        if ($producto['PRODUCTO_STOCK_MIN'] < $cantidad) {
            throw new Exception('Stock insuficiente. Stock disponible: ' . $producto['PRODUCTO_STOCK_MIN']);
        }
        
        // Verificar que el stock no quede negativo
        if ($producto['PRODUCTO_STOCK_MIN'] - $cantidad < 0) {
            throw new Exception('No se puede procesar el pedido. Stock insuficiente');
        }
        
        // Descontar stock
        $nuevo_stock = $producto['PRODUCTO_STOCK_MIN'] - $cantidad;
        $stmt = $pdo->prepare("
            UPDATE Productos 
            SET PRODUCTO_STOCK_MIN = ?,
                FECHA_ULTIMA_MODIFICACION = NOW()
            WHERE ID_PRODUCTO = ?
        ");
        $stmt->execute([$nuevo_stock, $producto_id]);
        
        // Verificar que la actualización fue exitosa
        if ($stmt->rowCount() === 0) {
            throw new Exception('Error al actualizar el stock');
        }
        
        // Registrar el pedido en la tabla de pedidos
        // Primero obtener el ID del estado "Pendiente"
        $stmt = $pdo->prepare("SELECT ID_ESTADO_PEDIDO FROM Estado_Pedidos WHERE NOMBRE_ESTADO = 'Pendiente'");
        $stmt->execute();
        $estado_pendiente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $pedido_id = null;
        if ($estado_pendiente) {
            // Crear registro de pedido
            $stmt = $pdo->prepare("
                INSERT INTO Pedidos (ID_CLIENTE, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO)
                VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), ?)
            ");
            $total_pedido = $producto['PRECIO_PRODUCTO'] * $cantidad;
            $stmt->execute([$cliente_id, $estado_pendiente['ID_ESTADO_PEDIDO'], $total_pedido]);
            $pedido_id = $pdo->lastInsertId();
            
            // Registrar detalle del pedido
            $subtotal = $producto['PRECIO_PRODUCTO'] * $cantidad;
            $stmt = $pdo->prepare("
                INSERT INTO Detalle_Pedidos (ID_PEDIDO, ID_PRODUCTO, CANTIDAD_PRODUCTO, PRECIO_UNITARIO, SUBTOTAL)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$pedido_id, $producto_id, $cantidad, $producto['PRECIO_PRODUCTO'], $subtotal]);
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        // Log de actividad exitosa
        logActividad("Pedido procesado exitosamente - Producto: {$producto['NOMBRE_PRODUCTO']}, Cantidad: $cantidad, Stock restante: $nuevo_stock", $cliente_id);
        
        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'mensaje' => 'Pedido procesado exitosamente',
            'producto' => [
                'id' => $producto_id,
                'nombre' => $producto['NOMBRE_PRODUCTO'],
                'precio' => $producto['PRECIO_PRODUCTO'],
                'cantidad_pedida' => $cantidad,
                'stock_restante' => $nuevo_stock,
                'total' => $producto['PRECIO_PRODUCTO'] * $cantidad
            ],
            'pedido_id' => $pedido_id,
            'timestamp' => time()
        ]);
        
    } catch (Exception $e) {
        // Rollback en caso de error
        $pdo->rollback();
        throw $e;
    }
    
} catch (PDOException $e) {
    logActividad("Error de base de datos: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    logActividad("Error del servidor: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
