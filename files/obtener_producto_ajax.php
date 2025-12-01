<?php
include 'conexion.php';

// Configurar respuesta JSON
header('Content-Type: application/json');

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método no permitido. Solo se acepta POST.'
    ]);
    exit();
}

// Verificar que se envió el ID
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'ID de producto requerido.'
    ]);
    exit();
}

$id_producto = intval($_POST['id']);

try {
    // Obtener información del producto usando PDO
    $consulta_producto = "SELECT 
        ID_PRODUCTO, 
        NOMBRE_PRODUCTO, 
        PRECIO_PRODUCTO, 
        PRODUCTO_STOCK_MIN, 
        TIPO_PRODUCTO_MARCA, 
        FECHA_VENCIMIENTO_PRODUCTO,
        COALESCE(ACTIVO, 1) as ACTIVO
        FROM Productos 
        WHERE ID_PRODUCTO = :id";
    
    $stmt_producto = $pdo_conexion->prepare($consulta_producto);
    $stmt_producto->bindParam(':id', $id_producto, PDO::PARAM_INT);
    $stmt_producto->execute();
    $producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        echo json_encode([
            'success' => false,
            'mensaje' => 'Producto no encontrado.'
        ]);
        exit();
    }
    
    // Verificar referencias en detalle_pedidos
    $consulta_referencias = "SELECT COUNT(*) as total FROM detalle_pedidos WHERE ID_PRODUCTO = :id";
    $stmt_referencias = $pdo_conexion->prepare($consulta_referencias);
    $stmt_referencias->bindParam(':id', $id_producto, PDO::PARAM_INT);
    $stmt_referencias->execute();
    $referencias_result = $stmt_referencias->fetch(PDO::FETCH_ASSOC);
    $referencias = $referencias_result ? $referencias_result['total'] : 0;
    
    // Preparar respuesta
    $response = [
        'success' => true,
        'producto' => [
            'id' => $producto['ID_PRODUCTO'],
            'nombre' => $producto['NOMBRE_PRODUCTO'],
            'precio' => $producto['PRECIO_PRODUCTO'],
            'stock' => $producto['PRODUCTO_STOCK_MIN'],
            'categoria' => $producto['TIPO_PRODUCTO_MARCA'],
            'fecha_vencimiento' => date('d/m/Y', strtotime($producto['FECHA_VENCIMIENTO_PRODUCTO'])),
            'activo' => $producto['ACTIVO']
        ],
        'referencias' => $referencias
    ];
    
    echo json_encode($response);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error inesperado: ' . $e->getMessage()
    ]);
}
?>
