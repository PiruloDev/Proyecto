<?php
header('Content-Type: application/json');
include 'conexion.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método no permitido. Use POST.'
    ]);
    exit();
}

// Verificar si se recibió el ID del producto
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id_producto = $_POST['id'];
    
    try {
        // Primero, obtener los datos del producto
        $consulta_producto = "SELECT NOMBRE_PRODUCTO FROM Productos WHERE ID_PRODUCTO = ?";
        $stmt_producto = $conexion->prepare($consulta_producto);
        $stmt_producto->bind_param("i", $id_producto);
        $stmt_producto->execute();
        $resultado = $stmt_producto->get_result();
        $producto = $resultado->fetch_assoc();
        
        if ($producto) {
            $nombre_producto = $producto['NOMBRE_PRODUCTO'];
            
            // Verificar si el producto tiene referencias en otras tablas
            $consulta_referencias = "SELECT COUNT(*) as total FROM Detalle_Pedidos WHERE ID_PRODUCTO = ?";
            $stmt_referencias = $conexion->prepare($consulta_referencias);
            $stmt_referencias->bind_param("i", $id_producto);
            $stmt_referencias->execute();
            $resultado_ref = $stmt_referencias->get_result();
            $referencias = $resultado_ref->fetch_assoc();
            
            if ($referencias['total'] > 0) {
                // El producto tiene referencias, no se puede eliminar
                echo json_encode([
                    'success' => false,
                    'hasReferences' => true,
                    'mensaje' => "No se puede eliminar el producto '{$nombre_producto}' porque está asociado a {$referencias['total']} pedido(s).<br><br>🔄 <strong>Opciones:</strong><br>• Desactivar el producto en lugar de eliminarlo<br>• Eliminar o modificar los pedidos que lo contienen<br>• Realizar eliminación forzada (⚠️ Riesgo de datos corruptos)"
                ]);
            } else {
                // El producto no tiene referencias, se puede eliminar
                $consulta_eliminar = "DELETE FROM Productos WHERE ID_PRODUCTO = ?";
                $stmt_eliminar = $conexion->prepare($consulta_eliminar);
                $stmt_eliminar->bind_param("i", $id_producto);
                
                if ($stmt_eliminar->execute()) {
                    echo json_encode([
                        'success' => true,
                        'hasReferences' => false,
                        'mensaje' => "El producto '{$nombre_producto}' ha sido eliminado exitosamente."
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'hasReferences' => false,
                        'mensaje' => "Error: No se pudo eliminar el producto."
                    ]);
                }
            }
        } else {
            echo json_encode([
                'success' => false,
                'hasReferences' => false,
                'mensaje' => "Error: El producto no existe."
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'hasReferences' => false,
            'mensaje' => "Error en la base de datos: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'hasReferences' => false,
        'mensaje' => "Error: Parámetros no válidos."
    ]);
}
?>
