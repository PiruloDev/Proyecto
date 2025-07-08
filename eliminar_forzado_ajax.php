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
            
            // Realizar eliminación forzada
            $consulta_eliminar = "DELETE FROM Productos WHERE ID_PRODUCTO = ?";
            $stmt_eliminar = $conexion->prepare($consulta_eliminar);
            $stmt_eliminar->bind_param("i", $id_producto);
            
            if ($stmt_eliminar->execute()) {
                echo json_encode([
                    'success' => true,
                    'mensaje' => "⚠️ ELIMINACIÓN FORZADA COMPLETADA<br><br>El producto '{$nombre_producto}' ha sido eliminado permanentemente del sistema.<br><br>🚨 <strong>Nota:</strong> Si este producto tenía referencias en pedidos, esas referencias han sido rotas y pueden causar errores en el sistema."
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'mensaje' => "Error: No se pudo eliminar el producto."
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => "Error: El producto no existe."
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'mensaje' => "Error en la base de datos: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'mensaje' => "Error: Parámetros no válidos."
    ]);
}
?>
