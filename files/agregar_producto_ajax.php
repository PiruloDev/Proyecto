<?php
header('Content-Type: application/json');
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar datos de entrada
        $nombre = trim($_POST['nombre_producto'] ?? '');
        $precio = floatval($_POST['precio_producto'] ?? 0);
        $stock = intval($_POST['stock_min'] ?? 0);
        $categoria = trim($_POST['tipo_producto_marca'] ?? '');
        $fecha_vencimiento = trim($_POST['fecha_vencimiento'] ?? '');
        
        // Validaciones
        if (empty($nombre)) {
            throw new Exception('El nombre del producto es requerido');
        }
        
        if (empty($categoria)) {
            throw new Exception('La categoría/marca es requerida');
        }
        
        if ($precio <= 0) {
            throw new Exception('El precio debe ser mayor a 0');
        }
        
        if ($stock < 0) {
            throw new Exception('El stock no puede ser negativo');
        }
        
        if (empty($fecha_vencimiento)) {
            throw new Exception('La fecha de vencimiento es requerida');
        }
        
        // Verificar que la fecha de vencimiento sea posterior a hoy
        $fecha_actual = date('Y-m-d');
        if ($fecha_vencimiento <= $fecha_actual) {
            throw new Exception('La fecha de vencimiento debe ser posterior a la fecha actual');
        }
        
        // Insertar producto
        $sql = "INSERT INTO Productos (
                    ID_ADMIN,
                    ID_CATEGORIA_PRODUCTO,
                    NOMBRE_PRODUCTO, 
                    PRODUCTO_STOCK_MIN,
                    PRECIO_PRODUCTO,
                    FECHA_VENCIMIENTO_PRODUCTO,
                    FECHA_INGRESO_PRODUCTO,
                    TIPO_PRODUCTO_MARCA
                ) VALUES (1, 1, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Error en la preparación de la consulta: ' . $conexion->error);
        }
        
        $fecha_ingreso = date('Y-m-d');
        $stmt->bind_param('sidsss', $nombre, $stock, $precio, $fecha_vencimiento, $fecha_ingreso, $categoria);
        
        if ($stmt->execute()) {
            // Obtener el ID del producto recién insertado
            $producto_id = $conexion->insert_id;
            
            echo json_encode([
                'success' => true,
                'mensaje' => 'Producto agregado exitosamente',
                'detalles' => [
                    'id' => $producto_id,
                    'nombre' => htmlspecialchars($nombre),
                    'precio' => number_format($precio, 2),
                    'stock' => $stock,
                    'categoria' => htmlspecialchars($categoria),
                    'fecha_vencimiento' => date('d/m/Y', strtotime($fecha_vencimiento)),
                    'fecha_ingreso' => date('d/m/Y')
                ]
            ]);
        } else {
            throw new Exception('Error al insertar el producto: ' . $stmt->error);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método de solicitud no válido'
    ]);
}

$conexion->close();
?>
