<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || !$_SESSION['usuario_logueado']) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Configuración de la base de datos
$host = 'localhost';
$db_name = 'proyectopanaderia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Verificar el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

switch ($method) {
    case 'POST':
        if (isset($input['accion'])) {
            switch ($input['accion']) {
                case 'agregar':
                    agregarProducto($pdo, $input);
                    break;
                case 'actualizar':
                    actualizarCantidad($input);
                    break;
                case 'eliminar':
                    eliminarProducto($input);
                    break;
                case 'finalizar':
                    finalizarPedido($pdo, $input);
                    break;
                case 'obtener_carrito':
                    obtenerCarrito($pdo);
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Acción no especificada']);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

function agregarProducto($pdo, $input) {
    $producto_id = $input['producto_id'] ?? null;
    $cantidad = $input['cantidad'] ?? 1;
    
    if (!$producto_id) {
        echo json_encode(['success' => false, 'message' => 'ID de producto no especificado']);
        return;
    }
    
    // Verificar que el producto existe y está activo
    $stmt = $pdo->prepare("SELECT * FROM Productos WHERE ID_PRODUCTO = ? AND ACTIVO = 1");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado o no disponible']);
        return;
    }
    
    // Verificar stock disponible
    if ($producto['PRODUCTO_STOCK_MIN'] < $cantidad) {
        echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
        return;
    }
    
    // Agregar o actualizar producto en el carrito
    if (isset($_SESSION['carrito'][$producto_id])) {
        $nueva_cantidad = $_SESSION['carrito'][$producto_id]['cantidad'] + $cantidad;
        if ($nueva_cantidad > $producto['PRODUCTO_STOCK_MIN']) {
            echo json_encode(['success' => false, 'message' => 'Stock insuficiente para la cantidad solicitada']);
            return;
        }
        $_SESSION['carrito'][$producto_id]['cantidad'] = $nueva_cantidad;
    } else {
        $_SESSION['carrito'][$producto_id] = [
            'id' => $producto_id,
            'nombre' => $producto['NOMBRE_PRODUCTO'],
            'precio' => $producto['PRECIO_PRODUCTO'],
            'cantidad' => $cantidad,
            'stock_disponible' => $producto['PRODUCTO_STOCK_MIN']
        ];
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Producto agregado al carrito',
        'carrito_count' => array_sum(array_column($_SESSION['carrito'], 'cantidad'))
    ]);
}

function actualizarCantidad($input) {
    $producto_id = $input['producto_id'] ?? null;
    $cantidad = $input['cantidad'] ?? 1;
    
    if (!$producto_id || $cantidad < 1) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        return;
    }
    
    if (isset($_SESSION['carrito'][$producto_id])) {
        // Verificar stock disponible
        if ($cantidad > $_SESSION['carrito'][$producto_id]['stock_disponible']) {
            echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
            return;
        }
        
        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
        echo json_encode([
            'success' => true, 
            'message' => 'Cantidad actualizada',
            'carrito_count' => array_sum(array_column($_SESSION['carrito'], 'cantidad'))
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
    }
}

function eliminarProducto($input) {
    $producto_id = $input['producto_id'] ?? null;
    
    if (!$producto_id) {
        echo json_encode(['success' => false, 'message' => 'ID de producto no especificado']);
        return;
    }
    
    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
        echo json_encode([
            'success' => true, 
            'message' => 'Producto eliminado del carrito',
            'carrito_count' => array_sum(array_column($_SESSION['carrito'], 'cantidad'))
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
    }
}

function finalizarPedido($pdo, $input) {
    if (empty($_SESSION['carrito'])) {
        echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
        return;
    }
    
    $user_id = $_SESSION['usuario_id'] ?? null;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Usuario no identificado']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Calcular total del pedido
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        $iva = $total * 0.19;
        $total_con_iva = $total + $iva;
        
        // Insertar pedido en la base de datos
        $stmt = $pdo->prepare("
            INSERT INTO Pedidos (ID_CLIENTE, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) 
            VALUES (?, 1, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), ?)
        ");
        $stmt->execute([$user_id, $total_con_iva]);
        $pedido_id = $pdo->lastInsertId();
        
        // Insertar detalles del pedido y actualizar stock
        foreach ($_SESSION['carrito'] as $item) {
            // Verificar stock actual
            $stmt = $pdo->prepare("SELECT PRODUCTO_STOCK_MIN FROM Productos WHERE ID_PRODUCTO = ?");
            $stmt->execute([$item['id']]);
            $stock_actual = $stmt->fetchColumn();
            
            if ($stock_actual < $item['cantidad']) {
                throw new Exception("Stock insuficiente para el producto: " . $item['nombre']);
            }
            
            // Calcular subtotal
            $subtotal = $item['precio'] * $item['cantidad'];
            
            // Insertar detalle del pedido
            $stmt = $pdo->prepare("
                INSERT INTO Detalle_Pedidos (ID_PEDIDO, ID_PRODUCTO, CANTIDAD_PRODUCTO, PRECIO_UNITARIO, SUBTOTAL) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$pedido_id, $item['id'], $item['cantidad'], $item['precio'], $subtotal]);
            
            // Actualizar stock
            $stmt = $pdo->prepare("
                UPDATE Productos 
                SET PRODUCTO_STOCK_MIN = PRODUCTO_STOCK_MIN - ? 
                WHERE ID_PRODUCTO = ?
            ");
            $stmt->execute([$item['cantidad'], $item['id']]);
        }
        
        $pdo->commit();
        
        // Limpiar carrito
        $_SESSION['carrito'] = [];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Pedido realizado con éxito',
            'pedido_id' => $pedido_id,
            'total' => $total_con_iva
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al procesar el pedido: ' . $e->getMessage()]);
    }
}

function obtenerCarrito($pdo) {
    $carrito_info = [];
    $total = 0;
    
    foreach ($_SESSION['carrito'] as $item) {
        // Verificar stock actual
        $stmt = $pdo->prepare("SELECT PRODUCTO_STOCK_MIN FROM Productos WHERE ID_PRODUCTO = ?");
        $stmt->execute([$item['id']]);
        $stock_actual = $stmt->fetchColumn();
        
        $item['stock_disponible'] = $stock_actual;
        $carrito_info[] = $item;
        $total += $item['precio'] * $item['cantidad'];
    }
    
    $iva = $total * 0.19;
    $total_con_iva = $total + $iva;
    
    echo json_encode([
        'success' => true,
        'carrito' => $carrito_info,
        'subtotal' => $total,
        'iva' => $iva,
        'total' => $total_con_iva,
        'carrito_count' => array_sum(array_column($_SESSION['carrito'], 'cantidad'))
    ]);
}
?>
